<?php

/**
 * @author: yaya
 * @since 22 March 2018
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\PemakaianBarang;
use app\modules\v1\models\PemakaianBarangDetail;
use app\modules\v1\models\StokBarang;
use app\modules\v1\businessLogic\StokBarang as BLStokBarang;


class PemakaianBarangController extends DocoActiveController
{
    public $modelClass = PemakaianBarang::class;

    const FIFO = 'FIFO';
    const LIFO = 'LIFO';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionSave()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $idParent = $noPemakaian = null;
            $request = Yii::$app->request;
            $dataJson = $request->post('data',"{}");
            $jwt = Yii::$app->jwt;
            $id_pegawai = !empty($jwt->user->pegawai_id) ? $jwt->user->pegawai_id : null;
            $data = json_decode($dataJson,true);
            // Insert Ke teransakasi pemakaian obat
            $tanggalPemakaian = $request->post("tanggal_pemakaian", date('Y-m-d H:i:s'));
            $model = new PemakaianBarang;
            $model->pegawai_id = $id_pegawai;
            $model->ruangan_id = $request->post('ruangan_id');
            $model->tgl_pemakaianbarang = date('Y-m-d H:i:s',strtotime($tanggalPemakaian));
            $model->keteranganpakai = $request->post('keterangan');
            $model->untuk_keperluan = 'belum tersedia';
            if ($model->validate() && $model->save()) {
                $dataInsert = [];
                $model->refresh();
                $idParent = $model->pemakaianbarang_id;
                $noPemakaian = $model->no_pemakaianbarang;
                foreach ($data as $key => $value) {
                    $dataInsert[] = [
                        'pemakaianbarang_id' => $idParent,
                        'barang_id' => $key,
                        'jumlah_pakai' => $value['permintaan'],
                        'harga_netto' => $value['harga_netto'],
                        'ppn' => $value['ppn'],
                        'harga_jual' => $value['harga_netto'],
                        'catatan_barang' => $value['keterangan'],
                        'satuanbesar_id' => $value['satuanbesar_id'],
                        'jumlah_input' => $value['jumlah_input'],
                        'satuankecil_id' => $value['satuankecil_id']
                    ];
                }
                PemakaianBarangDetail::batchInsert($dataInsert);
                // End Insert ke pemakaianobatalkes_t
                $tanggalBerlaku = date('Y-m-d');
                // Mencari Metode
                $konfig = $connection->createCommand("
                    SELECT metodeantrian FROM konfiggudang_k
                    WHERE tglberlaku >= '{$tanggalBerlaku}'
                    AND is_active = true
                ")->queryOne();
                // Select ulang ke Detail pemakaian berdasarkan id yang di insert yang di atas
                $detailTrans = $connection->createCommand("
                    SELECT *,jumlah_pakai as qty_satuanpakai FROM pemakaianbarangdetail_t
                    WHERE pemakaianbarang_id = {$idParent}
                ")->queryAll();
                // Mencari Metode dengan nilai default LIFO
                $currentMetode = self::LIFO;
                if ($konfig) {
                    $currentMetode = isset($konfig['metodeantrian']) ? strtoupper($konfig['metodeantrian']) : self::LIFO;
                }

                // Execute By Condition
                if ($currentMetode === self::LIFO) {
                   $methode = BLStokBarang::methodeLIFO($detailTrans,date('Y-m-d H:i:s'));
                } else {
                   $methode = BLStokBarang::methodeFIFO($detailTrans,date('Y-m-d H:i:s'));
                }
                $transaction->commit();
                return [
                    'message' => 'sukses',
                    'id_parent' => $idParent,
                    'no_pemakaianbarang' => $noPemakaian
                ];
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ];
        }
    }

    protected function methodeLIFO($data = [],$tanggalPemakaian)
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $ruangan_id = $request->post('ruangan_id');
        $listIdDetail = $listIdBarang = [];
        foreach ($data as $val) {
            $idBarang = $val['barang_id'];
            $listIdDetail[$idBarang] = $val;
            $listIdBarang[] = $idBarang;
        }
        $inCondition = "(" . implode(",", $listIdBarang) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT barang_id,id_stok,
            SUM(sub_query.qtystok_in - sub_query.qtystok_out) as total_stok ,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in
            FROM (
                SELECT (CASE WHEN t.stokbarangasal_id IS NULL THEN t.stokbarang_id ELSE t.stokbarangasal_id END) as id_stok,
                t.barang_id,
                t.qtystok_in,
                (CASE WHEN t.tglstok_in IS NULL THEN child.tglstok_in ELSE t.tglstok_in END) as tglstok_in,
                t.qtystok_out, t.nobatch,t.harganetto,t.persendiscount,
                t.jmldiscount,t.persenppn,t.persenpph,t.persenmargin,t.jmlmargin
                                 FROM stokbarang_t t
                 LEFT JOIN stokbarang_t child ON t.stokbarangasal_id = child.stokbarang_id
                WHERE t.barang_id IN {$inCondition} AND t.ruangan_id = {$ruangan_id}
            ) as sub_query
            GROUP BY sub_query.id_stok ,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in,tglstok_in
            ORDER BY sub_query.tglstok_in DESC

        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya jumlah_pakai selalu berkurang
        foreach ($query as $detail) {
            $idBarang = $detail['barang_id'];
            if (isset($listIdDetail[$idBarang])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idBarang]['jumlah_pakai'];
                // Lewati ketika stok sudah 0
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $totalStok,
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'pemakaianbarangdetail_id' => $listIdDetail[$idBarang]['pemakaianbarangdetail_id'],
                        'stokbarang_aktif' => false,
                        'is_active' => true
                    ];
                    $listIdDetail[$idBarang]['jumlah_pakai'] = $stokItem - $totalStok;
                } else {
                    if ($listIdDetail[$idBarang]['jumlah_pakai'] == $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                    }
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $listIdDetail[$idBarang]['jumlah_pakai'],
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'pemakaianbarangdetail_id' => $listIdDetail[$idBarang]['pemakaianbarangdetail_id'],
                        'stokbarang_aktif' => true,
                        'is_active' => true
                    ];
                    $listIdDetail[$idBarang]['jumlah_pakai'] = 0;
                }
            }
        }

        // Insert
        if ($tmpInsert) {
            StokBarang::batchInsert($tmpInsert);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            /**
            *dinonaktifkan
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
                WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition})
                AND stokbarang_aktif = true
            ")->execute();
            */
        }
        return true;

    }

    protected function methodeFIFO($data = [],$tanggalPemakaian, $mutasi = false)
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $ruangan_id = $request->post('ruangan_id');
        $listIdDetail = $listIdBarang = $trackPenggunaan = [];
        foreach ($data as $val) {
            $idBarang = $val['barang_id'];
            $listIdDetail[$idBarang][] = $val;
            $listIdBarang[] = $idBarang;

            $trackPenggunaan[$idBarang] = isset($trackPenggunaan[$idBarang]) ?
                $trackPenggunaan[$idBarang] : 0;
            $trackPenggunaan[$idBarang] += isset($val['jumlah_input']) ?
                $val['jumlah_input'] : 0;
        }
        $inCondition = "(" . implode(",", $listIdBarang) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT barang_id,id_stok,
            SUM(sub_query.qtystok_in - sub_query.qtystok_out) as total_stok ,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in,tglkadaluarsa
            FROM (
                SELECT (CASE WHEN t.stokbarangasal_id IS NULL THEN t.stokbarang_id ELSE t.stokbarangasal_id END) as id_stok,
                t.barang_id,
                t.qtystok_in,
                (CASE WHEN t.tglstok_in IS NULL THEN child.tglstok_in ELSE t.tglstok_in END) as tglstok_in,
                t.qtystok_out, t.nobatch,t.harganetto,t.persendiscount,
                t.jmldiscount,t.persenppn,t.persenpph,t.persenmargin,t.jmlmargin,t.tglkadaluarsa
                FROM stokbarang_t t
                LEFT JOIN stokbarang_t child ON t.stokbarangasal_id = child.stokbarang_id
                WHERE t.barang_id IN {$inCondition} AND t.ruangan_id = {$ruangan_id}
            ) as sub_query
            GROUP BY sub_query.id_stok ,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,tglstok_in,tglstok_in,tglkadaluarsa
            ORDER BY sub_query.tglstok_in ASC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya jumlah_input selalu berkurang
        foreach ($query as $detail) {
            $idBarang = $detail['barang_id'];
            if (isset($listIdDetail[$idBarang])) {
                $totalStok = $detail['total_stok'];
                $stokItem = $listIdDetail[$idBarang];

                foreach ($stokItem as $key => $barang) {
                    $currentStok = $listIdDetail[$idBarang][$key]['jumlah_input'];

                    if ($currentStok == 0 || $totalStok == 0) continue;
                    $row = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
                        'tglkadaluarsa' => ArrayHelper::getValue($detail,'tglkadaluarsa',null),
                        'nobatch' => ArrayHelper::getValue($detail,'nobatch',null),
                        'harganetto' => ArrayHelper::getValue($detail,'harganetto',0),
                        'persendiscount' => ArrayHelper::getValue($detail,'persendiscount',0),
                        'jmldiscount' => ArrayHelper::getValue($detail,'jmldiscount',0),
                        'persenppn' => ArrayHelper::getValue($detail,'persenppn',0),
                        'persenpph' => ArrayHelper::getValue($detail,'persenpph',0),
                        'persenmargin' => ArrayHelper::getValue($detail,'persenmargin',0),
                        'jmlmargin' => ArrayHelper::getValue($detail,'jmlmargin',0),
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_in' => 0,
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $barang['satuankecil_id'],
                        'stokbarang_aktif' => false,
                        'is_active' => true,
                        'additional_data' => ArrayHelper::getValue($barang,'additional_data',null),
                        'pemakaianbarangdetail_id' => ArrayHelper::getValue($barang,'pemakaianbarangdetail_id',null)
                    ];

                    /*
                    * Habiskan Stok sebelumnya terlebih dahulu,
                    * sebelum berpindah ke stok selanjutnya
                    */
                    if ($currentStok >= $totalStok) {
                        $row['qtystok_out'] = $totalStok;
                        $listOfOutStock[] = $detail['id_stok'];
                        $listIdDetail[$idBarang][$key]['jumlahjumlah_input_pakai'] = $currentStok - $totalStok;
                        $totalStok = 0;
                    } else {
                        $row['qtystok_out'] = $barang['jumlah_input'];
                        $listIdDetail[$idBarang][$key]['jumlah_input'] = 0;
                        $totalStok -= $currentStok;
                    }

                    $tmpInsert[] = $row;
                    if ($mutasi) {
                        $idTerima = isset($barang['terimamutasidetail_id']) ? $barang['terimamutasidetail_id'] : null;
                        // $tmpInsert[] = self::generateMutasi($row, $idTerima);
                    }
                }

                /**
                *commented
                // Lewati ketika stok sudah 0
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
                        'tglkadaluarsa' => @$detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'tglstok_in' => $detail['tglstok_in'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $totalStok,
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'pemakaianbarangdetail_id' => $listIdDetail[$idBarang]['pemakaianbarangdetail_id'],
                        'stokbarang_aktif' => false,
                        'is_active' => false
                    ];
                    $listIdDetail[$idBarang]['jumlah_pakai'] = $stokItem - $totalStok;
                } else {
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
                        'tglkadaluarsa' => ArrayHelper::getValue($detail,'tglkadaluarsa',null),
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => $detail['harganetto'],
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'tglstok_in' => $detail['tglstok_in'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s',strtotime($tanggalPemakaian)),
                        'qtystok_out' => $listIdDetail[$idBarang]['jumlah_pakai'],
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'pemakaianbarangdetail_id' => $listIdDetail[$idBarang]['pemakaianbarangdetail_id'],
                        'stokbarang_aktif' => false,
                        'is_active' => true
                    ];
                    $listIdDetail[$idBarang]['jumlah_pakai'] = 0;
                }
                **/
            }
        }

        // Insert
        if ($tmpInsert) {
            StokBarang::batchInsert($tmpInsert);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            /**
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
                WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition})
                AND stokbarang_aktif = true
            ")->execute();
            */
        }
        return true;
    }

    /**
    * @controller actionCetakPemakaianBarang
    * @attribute #table_pemakaian# => Untuk Menampilkan Tabel pemakaian barang
    * @attribute #no_pemakaian# => untuk menampilkan nomor pemakaian
    * @attribute #tgl_pemakaian# => untuk menampilkan Tanggal pemakaian
    * @attribute #ruangan_pemakaian# => untuk menampilkan Ruangan pemakaian
    **/

    public function actionCetakPemakaianBarang()
    {
        $request = Yii::$app->request;
        if ($request->post()) {
            $post = $request->post();
            $print = new DocoPrint();
            $tanggal = !empty($post['tanggal_pemakaian']) ? date('d M Y', strtotime($post['tanggal_pemakaian'])) : '';
            $no_pemakaianbarang = !empty($post['no_pemakaianbarang']) ? $post['no_pemakaianbarang'] : '';
            $ruangan_pemakai = !empty($post['ruangan_pemakai']) ? $post['ruangan_pemakai'] : '';
            $print->attributes = [
                '#table_pemakaian#' => $this->renderPartial('index',[
                    'detail' => !empty($post['data']) ? json_decode($post['data'],true) : []
                ]),
                '#no_pemakaian#' => $no_pemakaianbarang,
                '#tgl_pemakaian#' => $tanggal,
                '#ruangan_pemakaian#' => strtoupper($ruangan_pemakai),
            ];
            $print->Output();
        }
        \Yii::$app->response->statusCode = 500;
        return ['message' => 'Tidak Ada Data yang harus di cetak'];
    }

}