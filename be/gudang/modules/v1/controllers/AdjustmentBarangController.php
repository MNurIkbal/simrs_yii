<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\AdjusmenBarang;
use app\modules\v1\models\AdjusmenBarangMasuk;
use app\modules\v1\models\AdjusmenBarangKeluar;
use app\modules\v1\models\StokBarang;
use app\modules\v1\models\StokBarangR;
use app\modules\v1\models\Barang;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\SatuanUnit;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\SatuanKonversiBarang;
use app\modules\v1\businessLogic\StokBarang as BLStokBarang;
use SirsCore\features\IntegrasiAkunting;

class AdjustmentBarangController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\AdjusmenBarang';

    const FEFO = 'FEFO';
    const FIFO = 'FIFO';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new AdjusmenBarang;
            $query = $model::find();
            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['daftartindakan_nama'])) {
                    $query->andWhere(['ILIKE', 'daftartindakan_nama', $advancedFilter['daftartindakan_nama']]);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }


    private function cekStok($barang_id, $ruangan_id)
    {
        $query = StokBarangR::find()
            ->where([
                'ruangan_id' => $ruangan_id,
                'barang_id' => $barang_id
            ])->all();

        $qty_sisa = [];
        foreach ($query as $key => $value) {
            $qty_sisa[$value['barang_id']] =  ($value->qty_sisa) ? $value->qty_sisa : 0;
        }

        return $qty_sisa;
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $model = new AdjusmenBarang;

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        try {
            $dataJson = $request->post('data',"{}");
            $data = json_decode($dataJson,true);
            $postAdjustment = $post['post']['AdjusmenBarangForm'];
            $tgl_adjusmen = date('Y-m-d',strtotime($postAdjustment['tgl_adjusmen']));
            $model->attributes = $postAdjustment;
            $model->jenis_adjusmen = ($post['post']['tab-aktif'] == 'masuk') ? 0 : 1;
            $model->ruangan_adjusmen_id = Yii::$app->jwt->ruangan_id;
            $_POST['ruangan_id'] = Yii::$app->jwt->ruangan_id;

            if($model->validate() && $model->save()) {
                if(!empty($data)) {
                    $dataInsert = [];
                    $dataInsertStok = [];
                    $dataKonversi = [];
                    $idParent = $model->adjusmenbarang_id;
                    $barang_id = [];
                    foreach ($data as $key => $value) {
                        $barang_id[] = $value['barang_id'];
                    }

                    $cekStok = $this->cekStok($barang_id, $_POST['ruangan_id']);
                    $konfigSystem = $connection->createCommand("
                        SELECT * FROM konfigsystem_k
                    ")->queryOne();

                    $tanggalBerlaku = date('Y-m-d');
                    $konfig = $connection->createCommand("
                        SELECT metodeantrian FROM konfigfarmasi_k
                        WHERE tglberlaku >= '{$tanggalBerlaku}'
                        AND konfigfarmasi_aktif = true
                        AND is_active = true
                    ")->queryOne();


                    foreach ($data as $key => $value) {
                        $total_konversi = $value['qty_besar'] * $value['nilai_konversi'];

                        $tgl_kadaluarsa = !empty($value['tgl_kadaluarsa']) ? date('Y-m-d', strtotime($value['tgl_kadaluarsa'])) : '2050-12-31';

                        if($post['post']['tab-aktif'] == 'masuk') {
                            $dataInsert[] = [
                                'adjusmenbarang_id' => $idParent,
                                'barang_id' => $value['barang_id'],
                                'tgl_kadaluarsa' => $tgl_kadaluarsa,
                                'qty' => $value['qty_besar'],
                                'satuankecil_id' => $value['satuankecil_id'],
                                'satuanbesar_id' => $value['satuanbesar_id'],
                                'harga_netto' => $value['harga_netto'],
                                'satuankonversibrg_id' => $value['satuankonversi_id'],
                                'qty_konversi' => $total_konversi,
                                'no_batch' => $value['no_batch'],
                            ];

                            $dataInsertStok[] = [
                                'tglstok_in' => $tgl_adjusmen,
                                'stokoa_aktif' => true,
                                'ruangan_id' => $ruangan_id,
                                'barang_id' => $value['barang_id'],
                                'tglkadaluarsa' => $tgl_kadaluarsa,
                                'qtystok_in' => $total_konversi,
                                'qtystok_out' => 0,
                                'harganetto' => $value['harga_netto'],
                                'satuankecil_id' => $value['satuankecil_id'],
                                'adjusmenbarangmasuk_id' => $idParent
                            ];

                            /** pengecekan ke obat alkes untuk nge get stok terakhir **/
                            $qty_sisa = isset($cekStok[$value['barang_id']]) ? $cekStok[$value['barang_id']] : 0;
                            $barang = Barang::findOne($value['barang_id']);
                            $hargamaksimum = $barang->barang_max;
                            $hargaminimum = $barang->barang_min;
                            $harganetto = $value['harga_netto'];

                            if($harganetto > $hargamaksimum && $qty_sisa > 0) {
                                $hargamaksimum = ($konfigSystem['is_pembulatankeatas'] == true) ?
                                    round($harganetto / $total_konversi) :
                                    floor($harganetto / $total_konversi);
                            }
                            if($harganetto < $hargaminimum && $qty_sisa > 0) {
                                $hargaminimum = ($konfigSystem['is_pembulatankeatas'] == true) ?
                                    round($harganetto / $total_konversi) :
                                    floor($harganetto / $total_konversi);
                            }

                            $hargaratarata = ($hargamaksimum + $hargaminimum) / 2;
                            $barang->barang_harganetto = round($harganetto / $total_konversi);
                            $barang->barang_max = $hargamaksimum;
                            $barang->barang_min = $hargaminimum;
                            $barang->barang_average = $hargaratarata;

                            $barang->save(false);

                        } else {
                            $dataInsert[] = [
                                'adjusmenbarang_id' => $idParent,
                                'barang_id' => $value['barang_id'],
                                'satuankecil_id' => $value['satuankecil_id'],
                                'satuanbesar_id' => $value['satuanbesar_id'],
                                'satuankonversibrg_id' => $value['satuankonversi_id'],
                                'qty_konversi' => $total_konversi,
                                'alasan' => $value['alasan'],
                                'qty' => $value['qty_besar'],
                                'no_batch' => $value['no_batch'],
                            ];

                            $dataInsertStok[] = [
                                'ruangan_id' => $ruangan_id,
                                'barang_id' => $value['barang_id'],
                                'qtystok_out' => $total_konversi,
                                'satuankecil_id' => $value['satuankecil_id'],
                                'adjusmenbarangkeluar_id' => $idParent
                            ];

                            $qty_sisa = isset($cekStok[$value['barang_id']]) ? $cekStok[$value['barang_id']] : 0;

                            if($total_konversi > $qty_sisa) {
                                return [
                                    'text' => 'Maaf, Stok Barang '.$value['barang_nama'].' tidak mencukupi.',
                                    'status' => 422,
                                    'title' => 'Proses Gagal!',
                                    'no_adjusmen' => null
                                ];
                            }
                        }
                    }

                    $currentDate = date('Y-m-d H:i:s');
                    if($post['post']['tab-aktif'] == 'masuk') {
                        $aa = AdjusmenBarangMasuk::batchInsert($dataInsert,false);
                        $dataAdjustment = $connection->createCommand("
                            SELECT * FROM adjusmenbarangmasuk_t
                            WHERE adjusmenbarang_id = {$idParent}
                        ")->queryAll();

                        $dataInsertStok = [];
                        foreach ($dataAdjustment as $v) {
                            $dataInsertStok[] = [
                                'tglstok_in' => $currentDate,
                                'stokoa_aktif' => true,
                                'ruangan_id' => Yii::$app->jwt->ruangan_id,
                                'barang_id' => $v['barang_id'],
                                'tglkadaluarsa' => date('Y-m-d', strtotime($v['tgl_kadaluarsa'])),
                                'qtystok_in' => $v['qty_konversi'],
                                'qtystok_out' => 0,
                                'persendiscount' => 0,
                                'jmldiscount' => 0,
                                'persenppn' => 0,
                                'persenpph' => 0,
                                'persenmargin' => 0,
                                'jmlmargin' => 0,
                                'stokbarang_aktif' => true,
                                'harganetto' => $v['harga_netto'],
                                'satuankecil_id' => $v['satuankecil_id'],
                                'adjusmenbarangmasuk_id' => $v['adjusmenbarangmasuk_id']
                            ];
                        }
                        StokBarang::batchInsert($dataInsertStok);
                    } else {
                        AdjusmenBarangKeluar::batchInsert($dataInsert);
                        // Mencari Metode dengan nilai default FEFO
                        $currentMetode = BLStokBarang::FEFO;
                        if ($konfig) {
                            $currentMetode = isset($konfig['metodeantrian'])
                                                ? strtoupper($konfig['metodeantrian']) : BLStokBarang::FEFO;
                        }

                        $dataAdjustment = $connection->createCommand("
                            SELECT * FROM adjusmenbarangkeluar_t
                            WHERE adjusmenbarang_id = {$idParent}
                        ")->queryAll();

                        $detailTrans = [];
                        foreach ($dataAdjustment as $k => $v) {
                             $detailTrans[] = [
                                'barang_id' => $v['barang_id'],
                                'qty_satuanpakai' => $v['qty_konversi'],
                                'satuankecil_id' => isset($v['satuankecil_id']) ? $v['satuankecil_id'] : null,
                                'adjusmenbarangkeluar_id' => $v['adjusmenbarangkeluar_id'],
                            ];
                        }

                        // Execute By Condition
                        if ($currentMetode === BLStokBarang::FEFO) {
                           $methode = BLStokBarang::methodeFEFO($detailTrans,date('Y-m-d H:i:s'));
                        } else {
                           $methode = BLStokBarang::methodeFIFO($detailTrans,date('Y-m-d H:i:s'));
                        }
                    }

                    $transaction->commit();
                    $adjusmen = AdjusmenBarang::find()
                        ->where(['adjusmenbarang_id' => $idParent])
                        ->one();
                
                    IntegrasiAkunting::integratePenerimaanSupplier($adjusmen->no_adjusmen, DocoConstants::ADJ.DocoConstants::JENIS_BARANG);

                    $response = [
                        'text' => 'Adjustment Barang berhasil disimpan',
                        'title' => 'Proses berhasil !',
                        'no_adjusmen' => $adjusmen->no_adjusmen
                    ];

                    return $response;
                }
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Tidak ada data'
                ];
            }
            else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        }
        catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionCekStok($ruangan_id, $barang_id)
    {
        $query = StokBarangR::find()
            ->where([
                'ruangan_id' => $ruangan_id,
                'barang_id' => $barang_id
            ])->one();

        $qty_sisa = ($query) ? $query->qty_sisa : 0;

        return $qty_sisa;
    }


     /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tgl_adjusmen# => tanggal adjustment
    * @attribute #no_adjusmen# => nomor adjustment
    * @attribute #title# => title adjustment
    * @attribute #pegawai_mengetahui# => pegawai mengetahui
    * @attribute #pegawai_menyetujui# => pegawai menyetujui
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $no_adjusmen = $request->get('no_adjusmen');
        $tipe = $request->get('tipe');
        $jenis = ($tipe == 0) ? 'Masuk' : 'Keluar';
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::findOne($ruangan_id);
        $ruangan_nama = ($ruangan) ? $ruangan->ruangan_nama : '';
        $title = 'Adjustment Barang '.$jenis.' '.$ruangan_nama;
        $model = new AdjusmenBarang;

        $header = $model::find()->where(['no_adjusmen' => $no_adjusmen])->one();

        $pegawai_mengetahui = Pegawai::findOne($header->peg_mengetahui_id);
        $pegawai_menyetujui = Pegawai::findOne($header->peg_menyetujui_id);
        $query = $this->getDetail($tipe, $header->adjusmenbarang_id);

        $print = new DocoPrint();
        $print->attributes = [
            '#no_adjusmen#' => $no_adjusmen,
            '#tgl_adjusmen#' => date('d M Y', strtotime($header->tgl_adjusmen)),
            '#title#' => $title,
            '#pegawai_mengetahui#' => ($pegawai_mengetahui) ? $pegawai_mengetahui->nama_pegawai : '',
            '#pegawai_menyetujui#' => ($pegawai_menyetujui) ? $pegawai_menyetujui->nama_pegawai : '',
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
                'tipe' => $tipe,
            ]),
        ];

        $print->Output();
    }

    private function getDetail($tipe, $adjusmenbarang_id)
    {
        if($tipe == 0) {
            $query = AdjusmenBarangMasuk::find()
                ->select(['barang_m.barang_nama', 'adjusmenbarangmasuk_t.qty',
                    'besar.satuanunit_nama as besar', 'kecil.satuanunit_nama as kecil',
                    'adjusmenbarangmasuk_t.tgl_kadaluarsa', 'adjusmenbarangmasuk_t.harga_netto',
                    'konversi.nilai_konversi'])
                ->leftJoin('satuankonversibrg_m konversi', 'konversi.satuankonversibrg_id = adjusmenbarangmasuk_t.satuankonversibrg_id')
                ->leftJoin('satuanunit_m besar', 'besar.satuanunit_id = konversi.satuanbesar_id')
                ->leftJoin('satuanunit_m kecil', 'kecil.satuanunit_id = konversi.satuankecil_id')
                ->leftJoin('barang_m', 'barang_m.barang_id = adjusmenbarangmasuk_t.barang_id')
                ->where(['adjusmenbarang_id' => $adjusmenbarang_id]);
        }
        else {
            $query = AdjusmenBarangKeluar::find()
                ->select(['barang_m.barang_nama', 'adjusmenbarangkeluar_t.qty',
                    'besar.satuanunit_nama as besar', 'kecil.satuanunit_nama as kecil',
                    'adjusmenbarangkeluar_t.alasan', 'konversi.nilai_konversi'])
                ->innerJoin('satuankonversibrg_m konversi', 'konversi.satuankonversibrg_id = adjusmenbarangkeluar_t.satuankonversibrg_id')
                ->innerJoin('satuanunit_m besar', 'besar.satuanunit_id = konversi.satuanbesar_id')
                ->innerJoin('satuanunit_m kecil', 'kecil.satuanunit_id = konversi.satuankecil_id')
                ->innerJoin('barang_m', 'barang_m.barang_id = adjusmenbarangkeluar_t.barang_id')
                ->where(['adjusmenbarangkeluar_t.adjusmenbarang_id' => $adjusmenbarang_id]);
        }

        return $query;
    }

    public function actionGenerateApi($satuankonversi_id = null)
    {
        $satuan_konversi = SatuanKonversiBarang::findOne($satuankonversi_id);
        $satuanbesar_id = '';
        $satuankecil_id = '';
        $satuanBesar = '';
        $satuanKecil = '';
        $nilai_konversi = '';

        if($satuan_konversi) {
            $satuanbesar_id = $satuan_konversi->satuanbesar_id;
            $satuankecil_id = $satuan_konversi->satuankecil_id;
            $satuanBesar = SatuanUnit::findOne($satuan_konversi->satuanbesar_id);
            $satuanBesar = $satuanBesar->satuanunit_nama;
            $satuanKecil = SatuanUnit::findOne($satuan_konversi->satuankecil_id);
            $satuanKecil = ($satuanKecil) ? $satuanKecil->satuanunit_nama : '';
            $nilai_konversi = $satuan_konversi->nilai_konversi;
        }

        return [
            'satuanbesar_id' => $satuanbesar_id,
            'satuankecil_id' => $satuankecil_id,
            'satuanunit_nama_besar' => $satuanBesar,
            'satuanunit_nama_kecil' => $satuanKecil,
            'nilai_konversi' => $nilai_konversi,
        ];
    }

}
