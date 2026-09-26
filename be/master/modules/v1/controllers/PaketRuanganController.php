<?php
/**
 * @author: arief saputra
 * @description: master untuk CRUD Layar Antrian
**/

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use Doco\components\DocoPrint;
use app\modules\v1\models\TindakanRuangan;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\PaketRuanganMP;
use app\modules\v1\models\PaketRuanganV;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\KategoriTindakan;
use app\modules\v1\models\KelompokTindakan;
use app\modules\v1\models\Jeniskegiatantindakan;
use app\modules\v1\models\TindakanRuanganView;
use Doco\components\DocoHelpers;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;

class PaketRuanganController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TindakanRuangan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-layarantrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        // unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            
            $get = $request->post();
            
            $model = new Ruangan;
            $query = $model::find()->select(['ruangan_id', 'ruangan_nama','is_deleted','is_active'])->where(['is_deleted'=>false, 'is_active'=>true])->groupBy(['ruangan_id', 'ruangan_nama','is_deleted','is_active']);

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
    public function actionView($ruangan_id)
    {
        $model = new TindakanRuanganView;
        $query = $model::find();
        $query->andWhere(['ruangan_id' => $ruangan_id]);

        return new ActiveDataProvider([
                'query' => $query,
            ]);
    }

    private function getData($id = null)
    {
        $returnData = (new \yii\db\Query())
                        ->select([
                                't.ruangan_id',
                                't.daftartindakan_id',
                                't.additional_data',
                                't.is_active',
                                't.is_deleted',
                                't2.ruangan_nama',
                                't3.daftartindakan_kode',
                                't3.daftartindakan_nama',
                                't3.daftartindakan_namalainnya',
                                't4.kelompoktindakan_nama',
                                't5.jeniskegiatantindakan_nama',
                                't6.kategoritindakan_nama',
                        ])->from('tindakanruangan_mp t')
                        ->join('LEFT JOIN', 'ruangan_m t2','t2.ruangan_id = t.ruangan_id')
                        ->join('LEFT JOIN', 'daftartindakan_m t3','t3.daftartindakan_id = t.daftartindakan_id')
                        ->join('LEFT JOIN', 'kelompoktindakan_m t4','t4.kelompoktindakan_id = t3.kelompoktindakan_id')
                        ->join('LEFT JOIN', 'jeniskegiatantindakan_m t5','t5.jeniskegiatantindakan_id = t3.jeniskegiatantindakan_id')
                        ->join('LEFT JOIN', 'kategoritindakan_m t6','t6.kategoritindakan_id = t3.kategoritindakan_id');
                        // ->orderBy([ 't.daftartindakan_id' => SORT_ASC ]);

        if($id)
        {
            $returnData->where(['t.layarantrian_id' => $id]);
        }

        return $returnData;
    }

    /**
     * @author: arief saputra
     * @description: list daftar kode tindakan
    **/
    public function actionListDaftarTindakan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getDaftarTindakan();
        $result->select(['daftartindakan_id','daftartindakan_kode']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['like', 'LOWER(daftartindakan_kode)', $term]);
        }
        return $result->asArray()->all();
    }

    public function actionListDaftarTindakanNama()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getDaftarTindakan();
        $result->select(['daftartindakan_id','daftartindakan_nama']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['like', 'LOWER(daftartindakan_nama)', $term]);
        }
        return $result->asArray()->all();
    }

    public function getDaftarTindakan()
    {
        $data = DaftarTindakan::find()->where(['is_deleted' => false, 'is_active' => true]);
        return $data;
    }

    /**
     * @author: arief saputra
     * @description: list kelompok tindakan
    **/

    public function actionListKelompokTindakan()
    {
        $request = Yii::$app->request;
        $post = $request->post();       
        $result = $this->getKelompokTindakan();
        $result->select(['kelompoktindakan_id','kelompoktindakan_nama']);
        if(!empty($post['term'])){          
            $term = $post['term'];
            $result->where(['like', 'LOWER(kelompoktindakan_nama)', $term]);            
        }
        return $result->asArray()->all();
    }

    public function getKelompokTindakan()
    {
        $data = Kelompoktindakan::find();
        return $data;

    }

    /**
     * @author: arief saputra
     * @description: list kategori tindakan
    **/

    public function actionListKategoriTindakan()
    {
        $request = Yii::$app->request;
        $post = $request->post();       
        $result = $this->getKategoriTindakan();
        $result->select(['kategoritindakan_id','kategoritindakan_nama']);
        if(!empty($post['term'])){          
            $term = $post['term'];
            $result->where(['like', 'LOWER(kategoritindakan_nama)', $term]);            
        }
        return $result->asArray()->all();
    }

    public function getKategoriTindakan()
    {
        $data = KategoriTindakan::find();
        return $data;

    }

    /**
     * @author: arief saputra
     * @description: list jenis kegiatan tindakan
    **/

    public function actionListJenisKegiatanTindakan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getJenisKegiatanTindakan();
        $result->select(['jeniskegiatantindakan_id','jeniskegiatantindakan_nama']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['like', 'LOWER(jeniskegiatantindakan_nama)', $term]);
        }
        return $result->asArray()->all();
    }

    public function getJenisKegiatanTindakan()
    {
        $data = Jeniskegiatantindakan::find();
        return $data;

    }

    protected $_title = "Data Master Paket Ruangan";

    // Export excel
    public function actionExportExcel()
    {
        $model = new PaketRuanganV;
        // Try catch
        try {
            // Declare empty variables
            $data = [];
            $header = $footer = [];
            if(isset($_GET['advanced-filter'])){
                if(isset($_GET['advanced-filter']['ruangan_nama'])){
                    $header['Nama Ruangan'] = $_GET['advanced-filter']['ruangan_nama'];
                }
            }
            // Find model
            $query = $model::find()
                ->where(['is_active' => true])
                ->andWhere(['is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Execute query
            $model = $query->all();
            // return $model;
            // Assign data
            if (!empty($model)) {
                // Declare counter
                $counter = 0;

                // Loop
                foreach ($model as $index => $value) {
                    // Assign data
                    $data[$counter]['nama_ruangan'] = $value->ruangan_nama;
                    $data[$counter]['kode_paket'] = $value->tipepaket_kode;
                    $data[$counter]['nama_paket'] = $value->tipepaket_nama;
                    $data[$counter]['nama_lainnya'] = $value->tipepaket_namalainnya;

                    // Plus the counter
                    $counter++;
                }
            }
            
            // File path
            $filePath = DocoHelpers::exportExcel('Paket Ruangan', $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die();
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionExportPdf
     * @attribute #datatable# => Untuk mengganti data di table
     * @attribute #tgl# => Tanggal sekarang 
     */
    public function actionExportPdf()
    {
        $model = new PaketRuanganV;
        // Try catch
        try {

            // Find model
            $query = $model::find()
                ->where(['is_active' => true])
                ->andWhere(['is_deleted' => false]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            

            // Execute query
            $model = $query->all();

            // Check model
            if (!empty($model)) {
                // Print
                $print = new DocoPrint();

                // Assign attributes
                $print->attributes = [
                    '#tgl#' => Docohelpers::convertTo224(date('Y-m-d')),
                    '#datatable#' => $this->renderPartial('_cetak', [
                        'header' => array(),
                        'model' => $model,
                    ]),
                ];

                // Print output
                $print->Output();
            }
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGenerateApi()
    {

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()->where(['is_deleted'=>false, 'is_active'=>true]);
        $queryRuangan = $queryRuangan->asArray()->all();

        return [
            'ruangan' => $queryRuangan,
        ];
    }

    public function actionSimpanRuanganMp()
    {

        $post = \Yii::$app->request->post();
        $get = \Yii::$app->request->get();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $model = new PaketRuanganMP;
        // $cek_ruangan = PaketRuanganMp::findOne(['ruangan_id'=> $post['ruangan_id'],'tipepaket_id'=> $post['tipepaket_id']]);
        try {
            // if($cek_ruangan){

            $inputPaketRuanganMP = array(
                'ruangan_id' => $post['ruangan_id'],
                'tipepaket_id' => $post['tipepaket_id'],
                'is_active' => 1,
            );
            $model->attributes = $inputPaketRuanganMP;
            if ($model->save()) {
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'status_2' => 200,
                    'title' => 'Simpan Berhasil',
                    'text' => 'Simpan Paket Ruangan Berhasil',
                    // 'post'=>$post
                ];
            } else {
                $transaction->rollBack();
                $result['status'] = 200;
                $result['status_2'] = 500;
                $result['title'] = "Simpan gagal";
                $result['text'] = "Nama Paket sudah di input";
                $result['data'] = $model->getErrors();
            }
            // }else{
            //     $transaction->rollBack();
            //     $result = [
            //         'status' => 500,
            //         'title' => 'Input Gagal',
            //         'text' => 'Paket sudah Pernah Di input',
            //     ];
            // }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                // 'post'=>$post
            ];
        }

    }

    public function actionUbahRuanganMp()
    {
        $post = \Yii::$app->request->post();
        $get = \Yii::$app->request->get();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $model = PaketRuanganMP::findOne(['ruangan_id' => $post['ruangan_id'], 'tipepaket_id' => $post['tipepaket_id']]);
        try {
            if (!empty($model)) {
                $inputPaketRuanganMP = array(
                    'ruangan_id' => $post['ruangan_id'],
                    'tipepaket_id' => $post['tipepaket_id'],
                    'is_default' => $post['is_default'],
                    'is_active' => 1,
                );
                if (!empty($post['is_deleted'])) {
                    $inputPaketRuanganMP['is_deleted'] = $post['is_deleted'];
                }
                $model->attributes = $inputPaketRuanganMP;
                if ($model->save()) {
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'status_2' => 200,
                        'title' => 'Simpan Berhasil',
                        'text' => 'Simpan Paket Ruangan Berhasil',
                    ];
                    if (!empty($post['is_deleted'])) {
                        $result = [
                            'status' => 200,
                            'status_2' => 200,
                            'title' => 'Hapus Berhasil',
                            'text' => 'Hapus Paket Berhasil',
                        ];
                    }
                    if(isset($post['is_default']) && !empty($post['is_default'])){
                        $result = [
                            'status' => 200,
                            'status_2' => 200,
                            'title' => 'Sukses',
                            'text' => 'Set Default Berhasil',
                        ];
                    }
                } else {
                    $transaction->rollBack();
                    $result['status'] = 422;
                    $result['status_2'] = 422;
                    $result['data'] = $model->getErrors();
                }
            } else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['status_2'] = 422;
                $result['title'] = 'Gagal insert';
                $result['text'] = 'Data tidak dikenali';
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionHapusRuanganMp($id = null)
    {
        $post = \Yii::$app->request->post();
        $get = \Yii::$app->request->get();

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            if ($model = (new PaketRuanganMP)->delete(['ruangan_id' => $id])) {
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'status_2' => 200,
                    'title' => 'Hapus Berhasil',
                    'text' => 'Hapus Paket Ruangan Berhasil',
                ];
            } else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['status_2'] = 422;
                $result['data'] = $model->getErrors();
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetRuanganMp($id = null)
    {
        // Try catch
        try {
            // Find model
            $model = new PaketRuanganV();
            $query = $model::find();
            $query->where(['ruangan_id' => $id]);
            $query->andWhere(['is_deleted' => false]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCopyRuangan()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $queryCheck = PaketRuanganMP::find()->where(['ruangan_id'=>$post['ruangan_tujuan'], 'is_deleted'=>'false', 'is_active'=>'true'])->asArray()->all();
                if(count($queryCheck) > 0){
                    throw new \Exception("Salin ruangan tidak bisa dilakukan, Ruangan tujuan sudah memiliki paket tindakan",1);
                }
                $queryGet = "SELECT * FROM paketruangan_mp where ruangan_id = {$post['ruangan_asal']} and is_deleted=false";
                $getSource = $connection->createCommand($queryGet)->queryAll();
                $query  = "UPDATE paketruangan_mp set is_deleted = true, deleted_by='".Yii::$app->jwt->user->pegawai_id."', deleted_date='".date('Y-m-d H:i:s')."' where ruangan_id = '{$post['ruangan_tujuan']}'";
                $deleteOld = $connection->createCommand($query)->execute();
                $sourceData = [];
                foreach ($getSource as $key => $value) {
                    $newData = [];
                    $newData = $value;
                    $newData['ruangan_id'] = $post['ruangan_tujuan'];
                    $sourceData[] = $newData;
                }
                $saveAll = PaketRuanganMP::batchInsert($sourceData, false);
                if(!$saveAll){
                    return $saveAll->getErrors();
                    throw new \Exception("terjadi kesalahan");
                    
                }
                $transaction->commit();
                return true;
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 422;
            return [
                'status'=>422,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'status'=>422,
                'text' => $e->getMessage(),
                'title'=>'Proses Gagal'
            ];
        }
    }

    // public function actionCopyRuangan($id = null)
    // {
    //     $post = \Yii::$app->request->post();
    //     $get = \Yii::$app->request->get();

    //     $connection = Yii::$app->db;
    //     $transaction = $connection->beginTransaction();
    //     $result = array();
    //     $getPaketAsal = PaketRuanganMP::findAll(['ruangan_id' => $id]);
    //     try {
    //         if (!empty($getPaketAsal)) {

    //             // cek tujuan ruangan
    //             $cekTujuanRuangan = PaketRuanganMP::findOne(['ruangan_id' => $post['ruangan_id']]);
    //             if (empty($cekTujuanRuangan)) {

    //                 $copyPaket = array();
    //                 $tempPaketAsal = ArrayHelper::toArray($getPaketAsal);
    //                 foreach ($tempPaketAsal as $k => $v) {
    //                     $v['ruangan_id'] = $post['ruangan_id'];
    //                     $copyPaket[] = $v;
    //                 }
    //                 if ($copyPaketRuangan = PaketRuanganMP::batchInsert($copyPaket)) { /// proses input data paket ruangan secara masal
    //                     $transaction->commit();
    //                     $result = [
    //                         'status' => 200,
    //                         'status_2' => 200,
    //                         'title' => 'Salin Paket Ruangan Berhasil',
    //                         'text' => 'Proses Salin Paket Berhasil Berhasil',
    //                     ];
    //                 } else {
    //                     $transaction->rollBack();
    //                     $result['status'] = 500;
    //                     $result['status_2'] = 422;
    //                     $result['title'] = "Salin Paket Ruangan gagal";
    //                     $result['text'] = "Nama Paket sudah di input";

    //                 }

    //             } else {
    //                 $transaction->rollBack();
    //                 $result['status'] = 422;
    //                 $result['status_2'] = 500;
    //                 $result['title'] = "Salin Paket Ruangan gagal";
    //                 $result['text'] = "Ruangan tersebut sudah memiliki paket sebelumnya";

    //             }
    //             // cek tujuan ruangan
    //         } else {
    //             $transaction->rollBack();
    //             $result['status'] = 422;
    //             $result['status_2'] = 500;
    //             $result['title'] = "Salin Paket Ruangan gagal";
    //             $result['text'] = "Ruangan Asal Tidak memiliki paket";

    //         }

    //         return $result;
    //     } catch (\Exception $e) {
    //         $transaction->rollBack();
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage(),
                // // 'post'=>$post
    //         ];
    //     }
    // }
    // 
    public function actionDelete()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $query = "UPDATE tindakanruangan_mp set is_deleted = true, deleted_by='" . Yii::$app->jwt->user->pegawai_id . "', deleted_date='" . date('Y-m-d H:i:s') . "' where ruangan_id = '{$get['ruangan_id']}' and daftartindakan_id = '{$get['daftartindakan_id']}'";
        // $query = TindakanRuangan::deleteMapping($get['ruangan_id'], $get['daftartindakan_id']);
        $update = Yii::$app->db->createCommand($query)->execute();
        return true;
        // return $query->delete();
    }
    public function actionUpdateDefault()
    {
        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                $is_default = $post['is_default'];
                $query = "UPDATE tindakanruangan_mp set is_default = {$is_default} where daftartindakan_id = '{$post['daftartindakan_id']}' and ruangan_id = '{$post['ruangan_id']}'";
                $update = Yii::$app->db->createCommand($query)->execute();
                if ($update) {
                    $result = [
                        'status' => 200,
                        'status_2' => 200,
                        'title' => 'Sukses',
                        'text' => 'Set Default Paket Berhasil Berhasil',
                    ];
                    return $result;
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        }
    }
    public function actionSalinTindakanRuangan()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            if ($request->post()) {
                $post = $request->post();
                // $post = json_decode($post['data'], true);
                $queryGet = "SELECT * FROM tindakanruangan_mp where ruangan_id = {$post['ruangan_asal']} and is_deleted=false";
                $getSource = $connection->createCommand($queryGet)->queryAll();
                $query = "UPDATE tindakanruangan_mp set is_deleted = true, deleted_by='" . Yii::$app->jwt->user->pegawai_id . "', deleted_date='" . date('Y-m-d H:i:s') . "' where ruangan_id = '{$post['ruangan_tujuan']}'";
                $deleteOld = $connection->createCommand($query)->execute();
                $sourceData = [];
                foreach ($getSource as $key => $value) {
                    $newData = [];
                    $newData = $value;
                    $newData['ruangan_id'] = $post['ruangan_tujuan'];
                    $sourceData[] = $newData;
                }
                $saveAll = TindakanRuangan::batchInsert($sourceData, false);
                if (!$saveAll) {
                    return $saveAll->getErrors();
                    throw new \Exception("terjadi kesalahan");

                }
                $transaction->commit();
                return true;
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        }
    }

    public function actionGetDataRuangan()
    {
        try {
            $request = Yii::$app->request;

            $get = $request->post();

            $model = new Ruangan;
            $query = $model::find()->select(['ruangan_id', 'ruangan_nama', 'is_deleted', 'is_active'])->where(['is_deleted' => false, 'is_active' => true])->groupBy(['ruangan_id', 'ruangan_nama', 'is_deleted', 'is_active']);

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
}
