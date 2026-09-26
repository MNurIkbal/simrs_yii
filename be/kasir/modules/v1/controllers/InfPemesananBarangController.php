<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\InfoPemesananBarangView;
use app\modules\v1\models\DetailPemesananBarangView;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PesanBarang;
use app\modules\v1\models\MutasiBarang;
use app\modules\v1\models\MutasiBarangDetail;
use app\modules\v1\models\StokBarang;

class InfPemesananBarangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPemesananBarangView';

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
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoPemesananBarangView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pesanbarang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pesanbarang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pesanbarang']);
                $between = true;
            }

            if(isset($_GET['advanced-filter']['instalasi_tujuan'])){
                $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_tujuan'];
                unset($_GET['advanced-filter']['instalasi_tujuan']);
            }

            if(isset($_GET['advanced-filter']['ruangan_tujuan'])){
                $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_tujuan'];
                unset($_GET['advanced-filter']['ruangan_tujuan']);
            }
        }
        // if($between) {
        $query->andWhere(['between', 'tgl_pesanbarang', $start, $end]);    
        // }
        /**
         * End Special Condition date range
        **/
        
        // return [$start, $end];

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGenerateApi()
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

        // pemesanan
        $modelPemesanan = new PesanBarang;
        $queryPemesanan = $modelPemesanan::find();

        $queryPemesanan = DocoRestActiveFilter::advancedFilter($modelPemesanan, $queryPemesanan);
        $queryPemesanan = new ActiveDataProvider([
            'query' => $queryPemesanan,
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'pemesanan' => $queryPemesanan->getModels(),
        ];
    }

    public function actionGetNopemesanan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataNopemesanan();        
        $result->select(['no_pemesanan']);
        if(!empty($post['term'])){          
            $term = $post['term'];                     
            $result->where(['ILIKE','no_pemesanan',$term]);
        } 
        if(!empty($post['ruangan_id'])){          
            $ruanganid = $post['ruangan_id'];                     
            $result->andWhere(['ruangan_id'=>$ruanganid]);
        }       
        $result->groupBy(['no_pemesanan']);
        return $result->asArray()->all();
    }
    public function dataNopemesanan(){
        $data = InfoPemesananBarangView::find();
        return $data;
    }

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $model = InfoPemesananBarangView::find();
        if ($id) {
            $model->where(['pesanbarang_id' => $id]);
        }

        return $model;
    }

    public function actionDetail()
    {
        try {
            $request = Yii::$app->request;
            $model = new DetailPemesananBarangView;
            $query = $model::find()->where(['pesanbarang_id' => $request->get('id', null)]);

            return [
                'data' => $query->asArray()->all(),
                'count' => $query->count()
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

    public function actionGetKonversi($satuanbesar_id, $satuankecil_id, $qty_pesan)
    {
        $model = new SatuanKonversi();
        $query = $model->find()->where(['satuanbesar_id' => $satuanbesar_id, 'satuankecil_id' => $satuankecil_id])->one();
        $qty = '';

        if($query) {
            $qty = $query->nilai_konversi * $qty_pesan;
        }

        return $qty;
    }

    public function actionGetRuangan($instalasi_id)
    {
        $model = new Ruangan();
        $query = $model->find()->where(['instalasi_id' => $instalasi_id])->asArray()->all();

        return $query;
    }

    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model = new MutasiBarang;
                $model->attributes = $post;
                $model->pesanbarang_id = $model->pesanbarang_id;
                $model->pegawaipengirim_id = $model->pegawaipengirim_id;
                $model->ruangantujuan_id = $model->ruangantujuan_id;
                $model->tgl_mutasibarang = date('Y-m-d H:i:s', strtotime($model->tgl_mutasibarang));
                $model->nomutasi_barang = 'MUTBAR'.$model->pesanbarang_id;
                $model->status_mutasi = DocoConstants::STATUS_MUTASI_DIKIRIM;

                $info_pesan = InfoPemesananBarangView::find()->where(['pesanbarang_id' => $model->pesanbarang_id])->one();
                $model->ruanganasal_id = $info_pesan->ruangan_id;

                if ($model->save(false)) {
                    $pesanan_detail = DetailPemesananBarangView::find()
                        ->where(['pesanbarang_id' => $model->pesanbarang_id])
                        ->all();

                    $dataInsert = [];
                    $tanggalBerlaku = date('Y-m-d');
                    $konfig = $connection->createCommand("
                        SELECT metodeantrian FROM konfigfarmasi_k
                        WHERE tglberlaku >= '{$tanggalBerlaku}'
                        AND konfigfarmasi_aktif = true
                        AND is_active = true
                    ")->queryOne();
                    foreach ($pesanan_detail as $key => $row) {
                        $dataInsert[] = [
                            'pesanbarangdetail_id' => $row['pesanbarangdetail_id'],
                            'barang_id' => $row['barang_id'],
                            'mutasibarang_id' => $model->mutasibarang_id,
                            'qty_mutasi' => $row['qty_pesan'],
                            'qty_dipesan' => $row['qty_pesan'],
                            'satuanbesar_id' => $row['satuanbesar_id'],
                            'satuankecil_id' => $row['satuankecil_id'],
                            'harga_netto' => $row['harga_netto'],
                            'jumlah_input' => $row['jumlah_input'],
                            'is_active' => true
                        ];
                    }

                    MutasiBarangDetail::batchInsert($dataInsert);

                    $modelPesanBarang = PesanBarang::findOne($model->pesanbarang_id);
                    $modelPesanBarang->mutasibarang_id = $model->mutasibarang_id;
                    $modelPesanBarang->statuspesan = DocoConstants::STATUS_PESAN_DIKIRIM;
                    $modelPesanBarang->save(false);

                    $detailTrans = $connection->createCommand("
                        SELECT * FROM mutasibarangdetail_t 
                        WHERE mutasibarang_id = {$model->mutasibarang_id}
                    ")->queryAll();

                    $currentMetode = DocoConstants::FEFO;
                    if ($konfig) {
                        $currentMetode = isset($konfig['metodeantrian']) ? strtoupper($konfig['metodeantrian']) : DocoConstants::FEFO;
                    }

                    // Execute By Condition
                    if ($currentMetode === DocoConstants::FEFO) {
                       $methode = $this->methodeFEFO($detailTrans);
                    } else {
                       $methode = $this->methodeFIFO($detailTrans);
                    }

                    $transaction->commit();
                    return $methode;
                    // return ['message' => 'Berhasil di Simpan.'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'MutasiBarangForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    protected function methodeFEFO($data = [])
    {
        // Skip ketika tidak di temukan datanya

        if (!count($data)) return true;
        $request = Yii::$app->request;
        $info_pesan = InfoPemesananBarangView::find()->where(['pesanbarang_id' => $request->post('pesanbarang_id')])->one();
        // return $request->post();
        $ruangan_id = $info_pesan->ruangan_id;
        $listIdDetail = $idBarangMutasi = [];
        foreach ($data as $val) {
            $idBarang = $val['barang_id'];
            $listIdDetail[$idBarang] = $val;
            $idBarangMutasi[] = $idBarang;
        }
        $inCondition = "(" . implode(",", $idBarangMutasi) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT barang_id,id_stok,
            SUM(stokbarang.qtystok_in - stokbarang.qtystok_out) as total_stok,
            stokbarang.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            FROM (
                SELECT (CASE WHEN stokbarangasal_id IS NULL THEN stokbarang_id ELSE stokbarangasal_id END) as id_stok,
                barang_id,
                qtystok_in,
                qtystok_out, nobatch,harganetto,persendiscount,
                jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,
                tglkadaluarsa FROM stokbarang_t
                WHERE barang_id IN {$inCondition} AND ruangan_id = {$ruangan_id}
                AND stokbarang_aktif = true
            ) as stokbarang
            GROUP BY stokbarang.id_stok,stokbarang.tglkadaluarsa,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            ORDER BY stokbarang.tglkadaluarsa ASC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        foreach ($query as $detail) {
            $idBarang = $detail['barang_id'];
            if (isset($listIdDetail[$idBarang])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idBarang]['jumlah_mutasi'];
                // Lewati ketika stok sudah 0 
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
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
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'mutasibarangdetail_id' => $listIdDetail[$idBarang]['mutasibarangdetail_id'],
                        'stokbarang_aktif' => false,
                        'is_active' => true
                    ];
                    $listIdDetail[$idBarang]['jumlah_mutasi'] = $stokItem - $totalStok;
                } else {
                    if ($listIdDetail[$idBarang]['jumlah_mutasi'] == $totalStok) {
                        $listOfOutStock[] = $detail['id_stok'];
                    }
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
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
                        'qtystok_out' => $listIdDetail[$idBarang]['jumlah_mutasi'],
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'mutasibarangdetail_id' => $listIdDetail[$idBarang]['mutasibarangdetail_id'],
                        'stokbarang_aktif' => true,
                        'is_active' => true
                    ];
                    $listIdDetail[$idBarang]['jumlah_mutasi'] = 0;
                }
            }
        }

        // Insert
        if ($tmpInsert) {
            StokBarang::batchInsert($tmpInsert);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false 
                WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition}) 
                AND stokbarang_aktif = true
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
        $listIdDetail = $idBarangMutasi = [];
        foreach ($data as $val) {
            $idBarang = $val['barang_id'];
            $listIdDetail[$idBarang] = $val;
            $idBarangMutasi[] = $idBarang;
        }
        $inCondition = "(" . implode(",", $idBarangMutasi) . ")";

        $query = Yii::$app->db->createCommand("
            SELECT barang_id,id_stok,
            SUM(stokobat.qtystok_in - stokobat.qtystok_out) as total_stok,
            stokobat.tglkadaluarsa,nobatch,harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            FROM (
                SELECT (CASE WHEN stokbarangasal_id IS NULL THEN stokbarang_id ELSE stokbarangasal_id END) as id_stok,
                barang_id,
                qtystok_in,
                qtystok_out, nobatch,harganetto,persendiscount,
                jmldiscount,persenppn,persenpph,persenmargin,jmlmargin,
                tglkadaluarsa FROM stokbarang_t
                WHERE barang_id IN {$inCondition} AND ruangan_id = {$ruangan_id}
                AND stokbarang_aktif = true
            ) as stokobat
            GROUP BY stokobat.id_stok,stokobat.tglkadaluarsa,barang_id,nobatch,
            harganetto,persendiscount,jmldiscount,persenppn,persenpph,persenmargin,jmlmargin
            ORDER BY stokobat.tglstok_in ASC
        ")->queryAll();

        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        foreach ($query as $detail) {
            $idBarang = $detail['barang_id'];
            if (isset($listIdDetail[$idBarang])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idBarang]['jumlah_mutasi'];
                // Lewati ketika stok sudah 0 
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                if ($stokItem > $totalStok) {
                    $listOfOutStock[] = $detail['id_stok'];
                    // ini akan membuat row baru dengan catatan stok harus di update false
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
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
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'mutasibarangdetail_id' => $listIdDetail[$idBarang]['mutasibarangdetail_id'],
                        'stokbarang_aktif' => false,
                        'is_active' => false
                    ];
                    $listIdDetail[$idBarang]['jumlah_mutasi'] = $stokItem - $totalStok;
                } else {
                    $tmpInsert[] = [
                        'ruangan_id' => $ruangan_id,
                        'barang_id' => $idBarang,
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
                        'qtystok_out' => $listIdDetail[$idBarang]['jumlah_mutasi'],
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $listIdDetail[$idBarang]['satuankecil_id'],
                        'mutasibarangdetail_id' => $listIdDetail[$idBarang]['mutasibarangdetail_id'],
                        'stokbarang_aktif' => true,
                        'is_active' => true
                    ];
                    $listIdDetail[$idBarang]['jumlah_mutasi'] = 0;
                }
            }
        }

        // Insert
        if ($tmpInsert) {
            StokBarang::batchInsert($tmpInsert);
        }

        // Update Stok jadiin false
        if ($listOfOutStock) {
            $inCondition = "(" . implode(",", $listOfOutStock) . ")";
            Yii::$app->db->createCommand("
                UPDATE stokbarang_t SET stokbarang_aktif = false, is_active = false
                WHERE (stokbarangasal_id IN {$inCondition} OR stokbarang_id IN {$inCondition}) 
                AND stokbarang_aktif = true
            ")->execute();
        }
        return true;
    }
}