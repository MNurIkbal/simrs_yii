<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\PesanObatAlkes;
use app\modules\v1\models\InfoPemesananObatAlkes;
use app\modules\v1\models\DetailPemesananObatAlkes;
use app\modules\v1\models\MutasiObatRuangan;
use app\modules\v1\models\MutasiObatDetail;
use app\modules\v1\models\PesanObatDetail;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\models\InfoMutasiObatalkesView;
use app\modules\v1\models\DetailMutasiObatAlkesView;
use app\modules\v1\models\InfoStokObatAlkesView;

class TransaksiMutasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\MutasiObatRuangan';
    const FEFO = 'FEFO';
    const FIFO = 'FIFO';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        // unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {

    }

    public function actionGenerateApi($ruangan_id = null)
    {
        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find();

        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
        ]);

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();

        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
        ]);

        // dokter PJ
        $modelDokter = new PegawaiView;
        $queryDokter = $modelDokter::find();
        if ($ruangan_id) {
            $queryDokter->where(['ruangan_id' => $ruangan_id]);
        }

        $queryDokter = DocoRestActiveFilter::advancedFilter($modelDokter, $queryDokter);
        $queryDokter = new ActiveDataProvider([
            'query' => $queryDokter,
        ]);

        // obat
        $modelObat = new ObatAlkes;
        $queryObat = $modelObat::find();

        $queryObat = DocoRestActiveFilter::advancedFilter($modelObat, $queryObat);
        $queryObat = new ActiveDataProvider([
            'query' => $queryObat,
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'dokter' => $queryDokter->getModels(),
            'obat' => $queryObat->getModels(),
        ];
    }

    public function actionGetPemesanan($nopemesanan = null)
    {
        $model = InfoPemesananObatAlkes::find()
            ->select(['infopemesananobatalkes_v.*', 'mutasiobatruangan_t.nomutasioa', 'mutasiobatruangan_t.mutasiobatruangan_id'])
            ->join('LEFT JOIN', 'mutasiobatruangan_t', 'infopemesananobatalkes_v.pesanobatalkes_id = mutasiobatruangan_t.pesanobatalkes_id and mutasiobatruangan_t.is_deleted = false')
            ->joinWith(['ruangan', 'ruangan.instalasi'])->where(['nopemesanan' => $nopemesanan])
            ->asArray()->one();

        /*$detailMutasi = [];
        if (!is_null($model['mutasiobatruangan_id'])) {
            $detailMutasi = DetailMutasiObatAlkesView::find()->where(["mutasiobatruangan_id" => $model['mutasiobatruangan_id']])->all();
        }*/

        return [
            "pemesanan" => $model,
            // "detailMutasi" => $detailMutasi
        ];
    }

    public function actionGetPemesananDetail($nopemesanan)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();

            $result = DetailPemesananObatAlkes::find()
                ->select("
                    detailpemesananobatalkes_v.*,
                    detailmutasiobatalkes_v.jumlah_mutasi,
                    detailmutasiobatalkes_v.jumlah_input as jumlah_input_mutasi,
                    satuankonversi_m.nilai_konversi
                ")
                ->where(["detailpemesananobatalkes_v.nopemesanan" => $nopemesanan])
                ->join("LEFT JOIN", "detailmutasiobatalkes_v", "detailpemesananobatalkes_v.mutasiobatdetail_id = detailmutasiobatalkes_v.mutasiobatdetail_id")
                ->join("LEFT JOIN", "satuankonversi_m", "detailpemesananobatalkes_v.obatalkes_id = satuankonversi_m.obatalkes_id and satuankonversi_m.satuanbesar_id = detailpemesananobatalkes_v.satuanbesar_id and satuankonversi_m.is_deleted = false and satuankonversi_m.is_active = true")
                ->orderBy(['detailpemesananobatalkes_v.obatalkes_nama'=>SORT_ASC]);

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
            ];

        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $request = Yii::$app->request;
            $pesanobatalkes_id = $request->get('pesanobatalkes_id');
            if ($request->post()) {
                $post = $request->post('MutasiObatRuangan');
                $detailPemesanan = DetailPemesananObatAlkes::find()->where(['nopemesanan' => $post['nopemesanan']])->one();
                if($detailPemesanan->statuspesan == DocoConstants::BATAL_PESAN) {
                    return [
                        'data' => [],
                        'message' => 'Tidak bisa melakukan mutasi. Pemesanan telah dibatalkan.',
                        'status' => 422
                    ];
                }

                $model = new MutasiObatRuangan;
                if(!empty($post['mutasiobatruangan_id'])){
                    $model = MutasiObatRuangan::find()->where(['mutasiobatruangan_id'=>$post['mutasiobatruangan_id']])->one();
                }
                $model->attributes = $post;
                $model->pegawaimenyetujui_id = $post['pegawaimenyetujui_id'];

                $detailMutasi = $request->post('DetailMutasiObat',[]);

                if (!empty($post['mutasiobatruangan_id'])) {
                    $update = $this->updatePemesanan($post['mutasiobatruangan_id'], $post['pesanobatalkes_id'], $detailMutasi);
                    $model->update();
                    $transaction->commit();

                    return $update;
                } else {
                    if(!is_null($pesanobatalkes_id)) {
                        $cekMutasi = MutasiObatRuangan::find()->where(['pesanobatalkes_id' => $pesanobatalkes_id])->count();

                        if ($cekMutasi > 0) {
                            return [
                                'data' => [],
                                'message' => 'Mutasi Telah Dilakukan',
                                'status' => 422
                            ];
                        }
                    }
                    
                    if ($model->save()) {
                        $dataInsert = [];
                        $idParent = $model->mutasiobatruangan_id;
                        $modelMutasiObatRuangan = MutasiObatRuangan::findOne($idParent);
                        $pesanobatalkes_id = $modelMutasiObatRuangan->pesanobatalkes_id;
                        $modelPemesananDetail = new DetailPemesananObatAlkes();
                        $result = $modelPemesananDetail->getList([], $post['nopemesanan']);
                        $dataDetail = $result->asArray()->all();
                        $tanggalBerlaku = date('Y-m-d');
                        $konfig = $connection->createCommand("
                            SELECT metodeantrian FROM konfigfarmasi_k
                            WHERE tglberlaku >= '{$tanggalBerlaku}'
                            AND konfigfarmasi_aktif = true
                            AND is_active = true
                        ")->queryOne();

                        if ($konfig) {
                            $currentHarga = isset($konfig['hargaygdigunakan']) ? strtoupper($konfig['hargaygdigunakan']) : DocoConstants::HARGA_MAX;
                        }
                        foreach ($dataDetail as $value) {
                            if(!isset($detailMutasi[$value['pesanobatdetail_id']])){
                                continue;
                            }
                            
                            if($currentHarga == DocoConstants::HARGA_MAX) {
                                $harga_jualsatuan = is_null($value['hargamaksimum']) ? 0 : $value['hargamaksimum'];
                            } elseif($currentHarga == DocoConstants::HARGA_MIN) {
                                $harga_jualsatuan = is_null($value['hargaminimum']) ? 0 : $value['hargaminimum'];
                            } else {
                                $harga_jualsatuan = is_null($value['hargaratarata']) ? 0 : $value['hargaratarata'];
                            }
                            
                            $_jmlmutasi = isset($detailMutasi[$value['pesanobatdetail_id']]) ? $detailMutasi[$value['pesanobatdetail_id']] : 0;
                            
                            if($_jmlmutasi > $value['jumlah_pesan']){
                                $transaction->rollBack();
                                \Yii::$app->response->statusCode = 422;
                                return [
                                    'data' => [],
                                    'message' => 'Jumlah Kirim Salah',
                                    'status' => 422
                                ];
                                throw new \Exception("Jumlah Kirim Salah", 1);
                            }
                            
                            $infoStok = InfoStokObatAlkesView::find()
                                ->select(['obatalkes_id', 'qty_tersedia', 'obatalkes_nama'])
                                ->where([
                                    'ruangan_id'=>$value['ruangan_id'],
                                    'obatalkes_id'=>$value['obatalkes_id']
                                ])->one();

                            if($infoStok == FALSE || $infoStok == null){
                                // throw new \Exception("Stok Tidak Diketahui", 1);

                                // Set jumlah mutasi menjadi 0 
                                // Ketika ruangan pengirim belum pernah sama sekali memiliki transaksi item yang dipesan.
                                $_jmlmutasi = 0;
                            } else {
                                // Set $infoStok->qty_tersedia menjadi 0 jika nilai qty_tersedia minus
                                // Jika nilai qty tersedia minus atau null dapat 
                                // menyebabkan gagal distribusi pengiriman mutasi walaupun qty mutasi 0

                                if (
                                    (isset($infoStok->qty_tersedia) && $infoStok->qty_tersedia < 0)
                                    || is_null($infoStok->qty_tersedia)
                                ) $infoStok->qty_tersedia = 0;

                                if($_jmlmutasi > $infoStok->qty_tersedia){
                                    $transaction->rollBack();
                                    return [
                                        'data' => [],
                                        'message' => 'Qty kirim item ' . $infoStok->obatalkes_nama . ' melebihi stok.',
                                        'status' => 422
                                    ];
                                    throw new \Exception("Qty Melebihi Stok", 1);
                                }    
                            }

                            $dataInsert[] = [
                                'satuankecil_id' => $value['satuankecil_id'],
                                'mutasiobatruangan_id' => $idParent,
                                'obatalkes_id' => $value['obatalkes_id'],
                                'pesanobatdetail_id' => $value['pesanobatdetail_id'],
                                'jumlah_pesan' => $value['jumlah_pesan'],
                                'jumlah_mutasi' => $_jmlmutasi,
                                'harga_netto' => is_null($value['harganetto'])?0:$value['harganetto'],
                                'harga_jualsatuan' => $harga_jualsatuan,
                                'persen_discount' => ($value['discount']) ? $value['discount'] : 0,
                                'total_harga' => ($harga_jualsatuan - $value['discount']) * $value['jumlah_pesan'],
                                'is_active' => true,
                                'satuanbesar_id' => $value['satuanbesar_id'],
                                'jumlah_input' => $value['qty_besar']
                            ];
                        }
                        MutasiObatDetail::batchInsert($dataInsert);

                        $totalharganettomutasi = MutasiObatDetail::find()
                            ->select(['SUM(harga_netto)'])
                            ->where(['mutasiobatruangan_id' => $idParent])
                            ->scalar();

                        $totalhargajual = MutasiObatDetail::find()
                            ->select(['SUM(total_harga)'])
                            ->where(['mutasiobatruangan_id' => $idParent])
                            ->scalar();

                        /****** update mutasi obat ruangan ****/
                        $modelMutasiObatRuangan->totalharganettomutasi = $totalharganettomutasi;
                        $modelMutasiObatRuangan->totalhargajual = $totalhargajual;

                        $modelMutasiObatRuangan->save();

                        /****** end update mutasi obat ruangan ****/

                        $modelPesanObatAlkes = PesanObatAlkes::findOne($pesanobatalkes_id);
                        $modelPesanObatAlkes->mutasiobatruangan_id = $idParent;
                        $modelPesanObatAlkes->statuspesan = DocoConstants::STATUS_PESAN_DIKIRIM;
                        $modelPesanObatAlkes->save(false);

                        foreach ($dataDetail as $row) {
                            if(!isset($detailMutasi[$row['pesanobatdetail_id']])){
                                continue;
                            }
                            $modelMutasiObatDetail = MutasiObatDetail::find()->where([
                                'mutasiobatruangan_id' => $idParent,
                                'obatalkes_id' => $row['obatalkes_id']
                            ])->one();

                            $modelPesanObatAlkesDetail = PesanObatDetail::findOne($row['pesanobatdetail_id']);
                            $modelPesanObatAlkesDetail->mutasiobatdetail_id = $modelMutasiObatDetail->mutasiobatdetail_id;
                            $modelPesanObatAlkesDetail->save();
                        }

                        $detailTrans = $connection->createCommand("
                            SELECT * FROM mutasiobatdetail_t
                            WHERE mutasiobatruangan_id = {$idParent}
                        ")->queryAll();

                        $currentMetode = self::FEFO;
                        if ($konfig) {
                            $currentMetode = isset($konfig['metodeantrian']) ? strtoupper($konfig['metodeantrian']) : self::FEFO;
                        }

                        // Execute By Condition
                        // if ($currentMetode === self::FEFO) {
                        //    $methode = $this->methodeFEFO($detailTrans);
                        // } else {
                        //    $methode = $this->methodeFIFO($detailTrans);
                        // }

                        $transaction->commit();

                        $res = [
                            'message' => 'Data berhasil di simpan',
                            'id' => $idParent,
                            'pemesanan' => DocoHelpers::encrypt($pesanobatalkes_id),
                        ];
                        return $res;
                    } else {
                        return [
                            'data' => $model->errors,
                            'status' => 422
                        ];
                    }
                }
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

    public function updatePemesanan($mutasiobatruangan_id, $pesanobatalkes_id, $data)
    {
        try {
            $detailPemesanan = DetailPemesananObatAlkes::find()
                ->select("
                    detailmutasiobatalkes_v.pesanobatalkes_id,
                    detailmutasiobatalkes_v.mutasiobatdetail_id,
                    detailmutasiobatalkes_v.mutasiobatruangan_id,
                    detailmutasiobatalkes_v.obatalkes_id,
                    detailmutasiobatalkes_v.jumlah_mutasi,
                    detailmutasiobatalkes_v.jumlah_pesan,
                    detailmutasiobatalkes_v.jumlah_input,
                    detailpemesananobatalkes_v.pesanobatdetail_id")
                ->where(["detailmutasiobatalkes_v.pesanobatalkes_id" => $pesanobatalkes_id])
                ->andWhere(["detailpemesananobatalkes_v.pesan_is_deleted" => false])
                ->join("JOIN", "detailmutasiobatalkes_v", "detailpemesananobatalkes_v.mutasiobatdetail_id = detailmutasiobatalkes_v.mutasiobatdetail_id")
                ->asArray()
                ->all()
                ;

            $updateList = [];
            foreach ($detailPemesanan as $detail) {
                $pesanobatdetail_id = $detail["pesanobatdetail_id"];
                $mutasi = $detail["jumlah_mutasi"];
                if ($mutasi != $data[$pesanobatdetail_id]) {
                    $detail["jumlah_mutasi"] = $data[$pesanobatdetail_id];
                    $updateList[] = $detail;
                }
            }

            foreach ($updateList as $update) {
                $command = Yii::$app->db->createCommand();
                $exec = $command->update(
                    "mutasiobatdetail_t",
                    [
                        "jumlah_mutasi" => $update["jumlah_mutasi"],
                    ],
                    "mutasiobatdetail_id = :id",
                    [":id" => $update["mutasiobatdetail_id"]]
                )->execute();
            }

            $res = [
                'message' => 'Data berhasil di simpan',
                'id' => $mutasiobatruangan_id
            ];

            return $res;
        } catch (\Exception $e) {
            // throw new \Exception("Update Gagal", 1);
            throw new \Exception($e->getMessage(), 1);
            // $transaction->rollBack();
            // \Yii::$app->response->statusCode = 500;
            // return ['message' => $e->getMessage()];
        }
    }

    protected function methodeFEFO($data = [])
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $ruangan_id = $request->post('ruanganasal_id');
        $listIdDetail = $idObatAlkes = [];
        foreach ($data as $val) {
            $idObat = $val['obatalkes_id'];
            $listIdDetail[$idObat] = $val;
            $idObatAlkes[] = $idObat;
        }
        $inCondition = "(" . implode(",", $idObatAlkes) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT obatalkes_id,id_stok,
            SUM(stokobat.qtystok_in - stokobat.qtystok_out) as total_stok,
            stokobat.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            FROM (
                SELECT (CASE WHEN stokobatalkesasal_id IS NULL THEN stokobatalkes_id ELSE stokobatalkesasal_id END) as id_stok,
                obatalkes_id,
                qtystok_in,
                qtystok_out, nobatch,harganetto,persendiscount,
                jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,
                tglkadaluarsa FROM stokobatalkes_t
                WHERE obatalkes_id IN {$inCondition} AND ruangan_id = {$ruangan_id}
                AND stokoa_aktif = true
            ) as stokobat
            GROUP BY stokobat.id_stok,stokobat.tglkadaluarsa,obatalkes_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            ORDER BY stokobat.tglkadaluarsa ASC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        foreach ($query as $detail) {
            $idObat = $detail['obatalkes_id'];
            if (isset($listIdDetail[$idObat])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idObat]['jumlah_mutasi'];
                // Lewati ketika stok sudah 0
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'obatalkes_id' => $idObat,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => ($detail['harganetto']) ? $detail['harganetto'] : 0,
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s'),
                        'qtystok_out' => $totalStok,
                        'stokobatalkesasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idObat]['satuankecil_id'],
                        'mutasiobatdetail_id' => $listIdDetail[$idObat]['mutasiobatdetail_id'],
                        'stokoa_aktif' => false,
                        'is_active' => true
                    ];
                    $listIdDetail[$idObat]['jumlah_mutasi'] = $stokItem - $totalStok;
                } else {
                    if ($listIdDetail[$idObat]['jumlah_mutasi'] == $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                    }
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'obatalkes_id' => $idObat,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => ($detail['harganetto']) ? $detail['harganetto'] : 0,
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s'),
                        'qtystok_out' => $listIdDetail[$idObat]['jumlah_mutasi'],
                        'stokobatalkesasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idObat]['satuankecil_id'],
                        'mutasiobatdetail_id' => $listIdDetail[$idObat]['mutasiobatdetail_id'],
                        'stokoa_aktif' => true,
                        'is_active' => true
                    ];
                    $listIdDetail[$idObat]['jumlah_mutasi'] = 0;
                }
            }
        }

        // Insert
        if ($tmpInsert) {
            StokObatAlkes::batchInsert($tmpInsert);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokobatalkes_t SET stokoa_aktif = false, is_active = false
                WHERE (stokobatalkesasal_id IN {$inCondition} OR stokobatalkes_id IN {$inCondition})
                AND stokoa_aktif = true
            ")->execute();
        }


        return true;

    }

    protected function methodeFIFO($data = [])
    {
        // Skip ketika tidak di temukan datanya
        if (!count($data)) return true;
        $request = Yii::$app->request;
        $ruangan_id = $request->post('ruanganasal_id');
        $listIdDetail = $idObatAlkes = [];
        foreach ($data as $val) {
            $idObat = $val['obatalkes_id'];
            $listIdDetail[$idObat] = $val;
            $idObatAlkes[] = $idObat;
        }
        $inCondition = "(" . implode(",", $idObatAlkes) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT obatalkes_id,id_stok,
            SUM(stokobat.qtystok_in - stokobat.qtystok_out) as total_stok,
            stokobat.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            FROM (
                SELECT (CASE WHEN stokobatalkesasal_id IS NULL THEN stokobatalkes_id ELSE stokobatalkesasal_id END) as id_stok,
                obatalkes_id,
                qtystok_in,
                qtystok_out, nobatch,harganetto,persendiscount,
                jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,
                tglkadaluarsa FROM stokobatalkes_t
                WHERE obatalkes_id IN {$inCondition} AND ruangan_id = {$ruangan_id}
                AND stokoa_aktif = true
            ) as stokobat
            GROUP BY stokobat.id_stok,stokobat.tglkadaluarsa,obatalkes_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            ORDER BY stokobat.tglstok_in ASC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        foreach ($query as $detail) {
            $idObat = $detail['obatalkes_id'];
            if (isset($listIdDetail[$idObat])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idObat]['jumlah_mutasi'];
                // Lewati ketika stok sudah 0
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'obatalkes_id' => $idObat,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => ($detail['harganetto']) ? $detail['harganetto'] : 0,
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s'),
                        'qtystok_out' => $stokItem - $totalStok,
                        'stokobatalkesasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idObat]['satuankecil_id'],
                        'mutasiobatdetail_id' => $listIdDetail[$idObat]['mutasiobatdetail_id'],
                        'stokoa_aktif' => false,
                        'is_active' => false
                    ];
                    $listIdDetail[$idObat]['jumlah_mutasi'] = $stokItem - $totalStok;
                } else {
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'obatalkes_id' => $idObat,
                        'tglkadaluarsa' => $detail['tglkadaluarsa'],
                        'nobatch' => $detail['nobatch'],
                        'harganetto' => ($detail['harganetto']) ? $detail['harganetto'] : 0,
                        'persendiscount' => $detail['persendiscount'],
                        'jmldiscount' => $detail['jmldiscount'],
                        'persenppn' => $detail['persenppn'],
                        'persenpph' => $detail['persenpph'],
                        'persenmargin' => $detail['persenmargin'],
                        'jmlmargin' => $detail['jmlmargin'],
                        'qtystok_in' => 0,
                        'tglstok_out' => date('Y-m-d H:i:s'),
                        'qtystok_out' => $listIdDetail[$idObat]['jumlah_mutasi'],
                        'stokobatalkesasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idObat]['satuankecil_id'],
                        'mutasiobatdetail_id' => $listIdDetail[$idObat]['mutasiobatdetail_id'],
                        'stokoa_aktif' => true,
                        'is_active' => true
                    ];
                    $listIdDetail[$idObat]['jumlah_mutasi'] = 0;
                }
            }
        }

        // Insert
        if ($tmpInsert) {
            StokObatAlkes::batchInsert($tmpInsert);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokobatalkes_t SET stokoa_aktif = false, is_active = false
                WHERE (stokobatalkesasal_id IN {$inCondition} OR stokobatalkes_id IN {$inCondition})
                AND stokoa_aktif = true
            ")->execute();
        }
        return true;
    }

    public function actionListPegawai()
    {
        $model = new Pegawai;
        $query = $model::find()->joinWith(['jabatan']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetMutasiByPesan($pesan_id)
    {
        $mutasi = MutasiObatRuangan::find()
                    ->where(['pesanobatalkes_id'=>$pesan_id])
                    ->one();
        return [
            'mutasi' => $mutasi
        ];
    }

    /**
    * @controller actionCetakPdf
    * @attribute #no_pemesanan# => no_pemesanan
    * @attribute #no_mutasi# => no_mutasi
    * @attribute #ruangan_asal# => ruangan_asal
    * @attribute #ruangan_tujuan# => ruangan_tujuan
    * @attribute #tanggal_mutasi# => tanggal_mutasi
    * @attribute #pegawai_mutasi# => pegawai_mutasi
    * @attribute #pegawai_mengetahui# => pegawai_mengetahui
    * @attribute #cetak_mutasi# => table
    **/
    public function actionCetakPdf()
    {
        $model = new MutasiObatRuangan;
        $request = Yii::$app->request;
        $get = $request->get();
        if (isset($get['id'])) {
            $id = $get['id'];
            $mutasi_obat = InfoMutasiObatalkesView::find()->where(['mutasiobatruangan_id' => $id])->one();
            $mutasi_obat_detail = DetailMutasiObatAlkesView::find()->where(['mutasiobatruangan_id' => $id])->all();

            $print = new DocoPrint();
            $print->attributes = [
                '#cetak_mutasi#' => $this->renderPartial('index', [
                    'detail' => $mutasi_obat_detail
                ]),
                '#no_pemesanan#' => $mutasi_obat['nopemesanan'],
                '#no_mutasi#' => $mutasi_obat['nomutasioa'],
                '#ruangan_asal#' => $mutasi_obat['ruangan_asal'],
                '#ruangan_tujuan#' => $mutasi_obat['ruangan_nama'],
                '#tanggal_mutasi#' => date('d-m-Y', strtotime($mutasi_obat['tglmutasioa'])),
                '#pegawai_mutasi#' => $mutasi_obat['pegawai_mutasi'],
                '#pegawai_mengetahui#' => $mutasi_obat['pegawai_mengetahui'],
            ];
            $print->Output();
        }
    }

    public function actionVerifikasiPemesanan($pesanobatalkes_id) {
        try {
            $model = PesanObatAlkes::findOne($pesanobatalkes_id);
            $model->status_verifikasi = DocoConstants::OBAT_SUDAH_DIVERIFIKASI;
            if($model->save()){
                return [
                    'status' => 200,
                    'message' => 'Berhasil',
                    'text' => 'Status verifikasi berhasil di update'
                ];
            }

            return [
                'status' => 422,
                'data' => $model->errors
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
