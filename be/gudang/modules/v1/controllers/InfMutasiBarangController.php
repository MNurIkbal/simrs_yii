<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\v1\models\InfoMutasiBarangView;
use app\modules\v1\models\InfoDistribusiBarangView;
use app\modules\v1\models\DetailMutasiBarangView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\MutasiBarang;
use app\modules\v1\models\PesanBarang;
use app\modules\v1\models\StokBarang;
use app\modules\v1\models\TerimaMutasiBarang;
use app\modules\v1\models\MutasiBarangDetail;
use app\modules\v1\models\TerimaMutasiBarangDetail;
use app\modules\v1\models\SatuanKonversi;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\businessLogic\StokBarang as BLStokBarang;

class InfMutasiBarangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoMutasiBarangView';
    protected $_title = "Informasi Pemesanan Barang Keluar";

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
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoMutasiBarangView;
            $query = $model::find();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if(isset($_GET['advanced-filter'])) {
                // return $_GET['advanced-filter'];
                if(isset($_GET['advanced-filter']['tgl_mutasibarang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_mutasibarang']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_mutasibarang']);
                }
                // if(isset($_GET['advanced-filter']['instalasi_nama'])){
                //     $_GET['advanced-filter']['instalasi_tujuan_id'] = $_GET['advanced-filter']['instalasi_nama'];
                //     unset($_GET['advanced-filter']['instalasi_nama']);
                // }

                // if(isset($_GET['advanced-filter']['ruangan_nama'])){
                //     $_GET['advanced-filter']['ruangan_tujuan_id'] = $_GET['advanced-filter']['ruangan_nama'];
                //     unset($_GET['advanced-filter']['ruangan_nama']);
                // }
            }
            
            $query->andWhere(['between', 'tgl_mutasibarang', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);

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

    public function actionGenerateApi()
    {
        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find();

        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi->orderBy(['instalasi_nama'=>SORT_ASC]),
            'pagination' => false
        ]);

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find();

        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
            'pagination'=>false
        ]);

        // formulir
        $modelMutasi = new MutasiBarang;
        $queryMutasi = $modelMutasi::find();

        $queryMutasi = DocoRestActiveFilter::advancedFilter($modelMutasi, $queryMutasi);
        $queryMutasi = new ActiveDataProvider([
            'query' => $queryMutasi,
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'mutasi' => $queryMutasi->getModels(),
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new InfoMutasiBarangView;
        $query = $model::find(true)->where(['ruangan_asal_id' => $request->get('ruangan_id')]);
        
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_mutasibarang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_mutasibarang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_mutasibarang']);
            }

            if(isset($_GET['advanced-filter']['instalasi_nama'])){
                $_GET['advanced-filter']['instalasi_tujuan_id'] = $_GET['advanced-filter']['instalasi_nama'];
                unset($_GET['advanced-filter']['instalasi_nama']);
            }

            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $_GET['advanced-filter']['ruangan_tujuan_id'] = $_GET['advanced-filter']['ruangan_nama'];
                unset($_GET['advanced-filter']['ruangan_nama']);
            }
        }

        $query->andWhere(['between', 'tgl_mutasibarang', $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        foreach ($query->asArray()->all() as $key => $value) {
            $tgl_mutasi = date('d-M-Y H:i:s', strtotime($value['tgl_mutasibarang']));
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Mutasi')] = $tgl_mutasi;
            $newValue[\Yii::t('app', 'Nomor Mutasi')] = $value['nomutasi_barang'];
            $newValue[\Yii::t('app', 'Instalasi Tujuan')] = $value['instalasi_nama'];
            $newValue[\Yii::t('app', 'Ruangan Tujuan')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Status')] = $value['statusmutasi'];
            $result[$key] = $newValue;
        }

        $instalasi = new Instalasi;
        $instalasi_nama = '';
        $ruangan_nama = '';

        if(isset($_GET['advanced-filter']['instalasi_tujuan_id'])) {
            $queryInstalasi = $instalasi->findOne($_GET['advanced-filter']['instalasi_tujuan_id']);
            $instalasi_nama = ($queryInstalasi) ? $queryInstalasi->instalasi_nama : '';
        }

        $ruangan = new Ruangan;
        if(isset($_GET['advanced-filter']['ruangan_tujuan_id'])) {
            $queryRuangan = $ruangan->findOne($_GET['advanced-filter']['ruangan_tujuan_id']);
            $ruangan_nama = ($queryRuangan) ? $queryRuangan->ruangan_nama : '';
        }
        
        $header_start = date('d-M-Y H:i:s', strtotime($start));
        $header_end = date('d-M-Y H:i:s', strtotime($end));

        $header = array(
            Yii::t("app", "Periode Mutasi") => (($header_start." s.d. ".$header_end)),
            Yii::t("app", "Nomor Mutasi") => isset($_GET['advanced-filter']['nomutasi_barang']) ? $_GET['advanced-filter']['nomutasi_barang'] : '',
            Yii::t("app", "Instalasi Tujuan") => ($instalasi_nama),
            Yii::t("app", "Ruangan Tujuan") => ($ruangan_nama),
        );

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], [], [], true);
        $filePath->save('php://output');
        die;
    }

    public function actionDelete($id)
    {
        try {
            $result = (new MutasiBarang)->delete($id);
            $result = (new MutasiBarangDetail)->find()->where(['mutasibarang_id' => $id])->one();
            if($result) {
                $result->delete();
            }
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionView($id)
    {
        $model = new InfoDistribusiBarangView;
        $query = $model::find()->where(['mutasibarang_id' => $id]);
        $data  = $query->asArray()->one();

        return $data;
    }

    public function actionPenerimaan($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $model = InfoMutasiBarangView::find();
        if ($id) {
            $model->where(['mutasibarang_id' => $id]);
        }

        return $model;
    }

    public function actionDetail()
    {
        try {
            $request = Yii::$app->request;
            $model = new DetailMutasiBarangView;
            $query = $model::find()->where(['mutasibarang_id' => $request->get('id', null)]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query->orderBy(['barang_nama'=>SORT_ASC]),
            ]);
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

    public function actionGetKonversi($satuanbesar_id, $satuankecil_id, $qty_mutasi)
    {
        $model = new SatuanKonversi();
        $query = $model->find()->where(['satuanbesar_id' => $satuanbesar_id, 'satuankecil_id' => $satuankecil_id])->one();
        $qty = '';

        if($query) {
            $qty = $query->nilai_konversi * $qty_mutasi;
        }

        return $qty;
    }

    protected function mutasiStokBarang($currentMetode) {

        $request = Yii::$app->request;

        if($request->post()) {
            $post = $request->post();
            //stok mutasi barang
            $m_mutasi_detail = new MutasiBarangDetail;
            $mutasi_detail = $m_mutasi_detail->find()->where([
                "mutasibarang_id" => $post["mutasibarang_id"]
            ])->all();

            $modelMutasiBarang = MutasiBarang::findOne($post["mutasibarang_id"]);
        }


        $mutasi = [];
        if ( $mutasi_detail !==null && $modelMutasiBarang !== null ) {
            foreach ($mutasi_detail as $row) {
                $mutasi[$row["mutasibarangdetail_id"]] = [
                    'satuankecil_id' => $row["satuankecil_id"],
                    'mutasibarangdetail_id' => $row["mutasibarangdetail_id"],
                    'barang_id' => $row["barang_id"],
                    'qty_satuanpakai' => $row["qty_mutasi"],
                    'ruangan_id' => $modelMutasiBarang->ruanganasal_id,
                ];
            }
        }

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            if ( $modelMutasiBarang !== null ) {
                if ($currentMetode === DocoConstants::FEFO) {
                    $methodeMutasi = BLStokBarang::methodeFEFO($mutasi, $modelMutasiBarang->tgl_mutasibarang);
                } else {
                    $methodeMutasi = BLStokBarang::methodeFIFO($mutasi, $modelMutasiBarang->tgl_mutasibarang);
                }
                $transaction->commit();
                return $methodeMutasi;
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
                'message' => $e->getMessage(),
                'line'=>$e->getLine(),
                'file'=>$e->getFile()
            ];
        }
        return true;
    }

    public function actionCreate()
    {
        $connection = Yii::$app->db;
        $tanggalBerlaku = date('Y-m-d');
        $konfig = $connection->createCommand("
            SELECT metodeantrian FROM konfigfarmasi_k
            WHERE tglberlaku >= '{$tanggalBerlaku}'
            AND konfigfarmasi_aktif = true
            AND is_active = true
        ")->queryOne();

        $currentMetode = DocoConstants::FEFO;
        if ($konfig) {
            $currentMetode = isset($konfig['metodeantrian']) ? strtoupper($konfig['metodeantrian']) : DocoConstants::FEFO;
        }

        $methodeMutasi = $this->mutasiStokBarang($currentMetode);

        $transaction = $connection->beginTransaction();

        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $model = new TerimaMutasiBarang;
                $model->attributes = $post;
                $model->mutasibarang_id = $model->mutasibarang_id;
                $model->ruanganpenerima_id = $model->ruanganpenerima_id;
                $model->ruanganasalmutasi_id = $model->ruanganasalmutasi_id;
                $model->tglterima = date('Y-m-d H:i:s');
                $model->noterimamutasi = 'TERIM'.$model->mutasibarang_id;
                $model->pegawaipenerima_id = $model->pegawaimenyetujui_id;
                $model->pegawaimengetahui_id = $model->pegawaimengetahui_id;
                if ($model->save(false)) {
                    $mutasi_detail = DetailMutasiBarangView::find()
                        ->where(['mutasibarang_id' => $model->mutasibarang_id])
                        ->all();

                    $dataInsert = [];
                    foreach ($mutasi_detail as $key => $row) {
                        
                        $dataInsert[] = [
                            'terimamutasibarang_id' => $model->terimamutasibarang_id,
                            'mutasibarangdetail_id' => $row['mutasibarangdetail_id'],
                            'satuankecil_id' => $row['satuankecil_id'],
                            'ruangan_id' => $row['ruangan_tujuan_id'],
                            'barang_id' => $row['barang_id'],
                            'jmlmutasi' => $row['qty_mutasi'],
                            'jmlterima' => $row['qty_mutasi'],
                            'is_active' => true
                        ];
                    }

                    TerimaMutasiBarangDetail::batchInsert($dataInsert);

                    $modelMutasiBarang = MutasiBarang::findOne($model->mutasibarang_id);
                    if (!empty($modelMutasiBarang) && $modelMutasiBarang->status_mutasi == DocoConstants::STATUS_MUTASI_DITERIMA) {
                        return [
                            "status" => 422,
                            "title" => "Proses Gagal !",
                            "message" => "Tidak bisa melakukan mutasi. Sudah Dilakukan Penerimaan."
                        ];
                    }
                    $modelMutasiBarang->status_mutasi = DocoConstants::STATUS_MUTASI_DITERIMA;
                    $modelMutasiBarang->save(false);

                    $modelPesanBarang = PesanBarang::findOne($modelMutasiBarang->pesanbarang_id);
                    // $modelPesanBarang->mutasibarang_id = $model->mutasibarang_id;
                    $modelPesanBarang->statuspesan = DocoConstants::STATUS_PESAN_DITERIMA;
                    $modelPesanBarang->save(false);

                    //stok terima mutasi barang
                    $detailTrans = $connection->createCommand("
                        SELECT * FROM terimamutasibarangdetail_t 
                        WHERE terimamutasibarang_id = {$model->terimamutasibarang_id}
                    ")->queryAll();
                    // var_dump($detailTrans);exit;

                    // Execute By Condition
                    if ($currentMetode === DocoConstants::FEFO) {
                       $methode = $this->methodeFEFO($detailTrans);
                    } else {
                       $methode = $this->methodeFIFO($detailTrans);
                    }

                    $transaction->commit();
                    if($methode && $methodeMutasi) {
                        return true;
//                        return [
//                            "status" => 200,
//                            "title" => "Proses Sukses",
//                            "text" => "Penerimaan Barang berhasil disimpan",
//                        ];
                    }
                    // return ['message' => 'Berhasil di Simpan.'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'TerimaMutasiBarangForm');
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
                'message' => $e->getMessage(),
                'line'=>$e->getLine(),
                'file'=>$e->getFile()
            ];
        }
    }

    protected function methodeFEFO($data = [])
    {
        // Skip ketika tidak di temukan datanya

        if (!count($data)) return true;
        $request = Yii::$app->request;
        $ruangan_id = $request->post('ruanganasalmutasi_id');
        $listIdDetail = $idBarangMutasi = [];
        foreach ($data as $val) {
            $idBarang = $val['barang_id'];
            $listIdDetail[$idBarang][] = $val;
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

        $currentDate = date('Y-m-d H:i:s');
        $tmpInsert = $listOfOutStock = [];
        // Kuncinya qty_satuanpakai selalu berkurang
        foreach ($query as $detail) {
            $idBarang = $detail['barang_id'];
            if (isset($listIdDetail[$idBarang])) {
                // ini stok permintaan yang di inputkan
                // contoh permintaan 250 sedangkan stok detail nya 100
                $stokItem = $listIdDetail[$idBarang];
                // Lewati ketika stok sudah 0 
                if ($stokItem == 0) continue;
                $totalStok = $detail['total_stok'];
                // var_dump($stokItem);exit;
                foreach ($stokItem as $key => $barang) {
                    $currentStok = $listIdDetail[$idBarang][$key]['jmlmutasi'];

                    if ($currentStok == 0 || $totalStok == 0) continue;
                    $row = [
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
                        'tglstok_out' => $currentDate,
                        'stokbarangasal_id' => $detail['id_stok'],
                        'satuankecil_id' => $barang['satuankecil_id'],
                        'mutasibarangdetail_id' => $barang['mutasibarangdetail_id'],
                        'stokbarang_aktif' => false,
                        'is_active' => true
                    ];

                    if ($currentStok >= $totalStok) {
                        $row['qtystok_out'] = $totalStok;
                        $listOfOutStock[] = $detail['id_stok'];
                        $listIdDetail[$idBarang][$key]['jmlmutasi'] = $currentStok - $totalStok;
                        $totalStok = 0;
                    } else {
                        $row['qtystok_out'] = $barang['jmlmutasi'];
                        $listIdDetail[$idBarang][$key]['jmlmutasi'] = 0;
                        $totalStok -= $currentStok;
                    }

                    // $tmpInsert[] = $row;
                    $idTerima = isset($barang['terimamutasibarangdetail_id']) ? $barang['terimamutasibarangdetail_id'] : null;
                    $tmpInsert[] = self::generateMutasi($row, $idTerima);
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
                        'terimamutasibarangdetail_id' => $listIdDetail[$idBarang]['terimamutasibarangdetail_id'],
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
                        'terimamutasibarangdetail_id' => $listIdDetail[$idBarang]['terimamutasibarangdetail_id'],
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

    public static function generateMutasi($row, $idTerima)
    {
        $request = Yii::$app->request;
        $row['qtystok_in'] = $row['qtystok_out'];
        $row['ruangan_id'] = $request->post('ruanganpenerima_id');
        $row['tglstok_in'] = $row['tglstok_out'];
        $row['qtystok_out'] = 0;
        $row['terimamutasibarangdetail_id'] = $idTerima;
        $row['stokoa_aktif'] = true;
        unset($row['tglstok_out']);
        unset($row['stokbarangasal_id']);
        unset($row['mutasibarangdetail_id']);
        return $row;
    }

    /**
    * @controller actionPrint
    * @attribute #no_pemesanan#       => Menampilkan Nomor Pemesanan
    * @attribute #no_mutasi#          => Menampilkan Nomor Mutasi
    * @attribute #tgl_cetak#          => Menampilkan Tanggal Cetak
    * @attribute #tgl_dikirim#        => Menampilkan Tanggal Kirim
    * @attribute #ruangan_asal#       => Menampilkan Ruangan Asal
    * @attribute #ruangan_tujuan#     => Menampilkan Ruangan Tujuan
    * @attribute #pegawai_mutasi#     => Menampilkan Pegawai Mutasi
    * @attribute #pegawai_mengetahui# => Menampilkan Pegawai Mengetahui
    * @attribute #detail_mutasi#      => Menampilkan Table Mutasi
    **/
    public function actionPrint($id)
    {
        return Yii::$app->docoPlugin->execute('cetak_mutasi');
    }
}
