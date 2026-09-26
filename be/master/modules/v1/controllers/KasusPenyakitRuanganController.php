<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KasusPenyakitRuangan;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\KasusPenyakitRuanganView;
use app\modules\v1\models\KasusPenyakitDiagnosaView;
use app\modules\v1\models\Pendaftaran;

use Doco\components\DocoHelpers;
use yii\db\Query;
use yii\db\Connection;
use Doco\components\DocoPrint;

class KasusPenyakitRuanganController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KasusPenyakitRuangan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ListData"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["getListRuangan"] = ["POST", "GET"];
        $verbs["getListKasusPenyakit"] = ["POST", "GET"];
        $verbs["batchCreate"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    private function requestFilterData($model, $query, $request){
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){

            if (isset($advancedFilters['ruangan_nama']) ) {
                $query->andWhere(['ruangan_nama' => $advancedFilters['ruangan_nama'] ]);
            }

            if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                $query->andWhere(['jeniskasuspenyakit_nama' => $advancedFilters['jeniskasuspenyakit_nama'] ]);
            }

            if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(jeniskasuspenyakit_nama)', strtolower($advancedFilters['jeniskasuspenyakit_nama']) ]);
            }

            if (isset($advancedFilters['is_active']) ) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['ruangan_nama'=>SORT_ASC]);

        return $query;
    }


    /**
    * @author Rizal
    * @since 2018-01-05 14:06:35 
    * @update Iqbal@docotel.com
    * @dateUpdate : 2018-10-26 14:06:35
    * @param 
    * @return 
    * @desc 
    */
    public function actionIndex()
    { 
        try {
            $request = Yii::$app->request;
            $model = new KasusPenyakitRuanganView;
            $query = $model::find();

            $queries = $this->requestFilterData($model, $query, $request);            
            
            return new ActiveDataProvider([
                'query' => $queries,
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

    public function actionListData()
    {
        $data = $this->getDataNew();
        $result = ['data' => $data, 'totalCount' => count($data)];
        return $result;
    }

    public function getDataNew($jeniskasuspenyakit_id = null, $ruangan_id = null)
    {
        $where = '';
        if(isset($jeniskasuspenyakit_id) && isset($ruangan_id)) {
            $where = ' AND t.jeniskasuspenyakit_id = '.$jeniskasuspenyakit_id.' AND t.ruangan_id = '.$ruangan_id.' ';
        }

        $order = ' order by a.jeniskasuspenyakit_nama, c.ruangan_nama'; 

        if(isset($_GET['advanced-filter'])){            
            $filter = $_GET['advanced-filter'];                                                 
            if(isset($filter['jeniskasuspenyakit_nama'])){             
                $where .= " and a.jeniskasuspenyakit_nama ILIKE '%".$filter['jeniskasuspenyakit_nama']."%'";
            }       
            if(isset($filter['ruangan_nama'])){             
                $where .= " and c.ruangan_nama ILIKE '%".$filter['ruangan_nama']."%'";
            }
            if(isset($filter['is_active'])){                     
                $status = ($filter['is_active'] == 0) ? 'false' : 'true';
                $where .= " and t.is_active = {$status}";                        
            }           

        }   
        
        if(isset($_GET['order'])){
            $order = ' order by '.$_GET['order'];               
        }

        $sql = "SELECT
                t.jeniskasuspenyakit_id,
                t.ruangan_id,
                a.jeniskasuspenyakit_nama,
                c.ruangan_nama,
                t.is_active
                FROM
                kasuspenyakitruangan_mp AS t
                INNER JOIN jeniskasuspenyakit_m AS a ON t.jeniskasuspenyakit_id = a.jeniskasuspenyakit_id
                INNER JOIN ruangan_m AS c ON t.ruangan_id = c.ruangan_id
                WHERE t.is_deleted = false ".$where.$order." ";

        if(isset($jeniskasuspenyakit_id) && isset($ruangan_id)) {
            $data = Yii::$app->db->createCommand($sql)->queryOne();
        }
        else {
            $data = Yii::$app->db->createCommand($sql)->queryAll();
        }
        
        return $data;
    }

    /**
    * @author iqbal@docotel.com
    * @since 2018-10-25 15:03:18 
    * @param 
    * @return message
    * @desc 
    */
    public function actionCreatePenyakitRuangan()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
           
            $model = new KasusPenyakitRuangan;
            $post = $request->post();
            $model->type_method = 'create';
            if(empty($post)){
                $result = [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Data Tidak Di Temukan'
                        ];                        
                return $result;
            }else{
                $model->attributes = $post;
                if (!$model->validate()) {
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitRuanganForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }else{
                    $result = $model->dataMappingByTwoPK($model->ruangan_id, $model->jeniskasuspenyakit_id);
                    if($result){
                        $result = [
                            'status' => 200,
                            'title' => 'Proses Berhasil',
                            'text' => 'Data Berhasil Tersimpan'
                        ];                        
                        return $result;
                    }else{
                        $model->attributes = $post;
                        $getDefaultData = $model::getDefaultData();
                        $model->last_modified_date = $getDefaultData->date;
                        $model->deleted_date = $getDefaultData->date;
                        if ($model->save()) {
                            $result = [
                                'status' => 200,
                                'title' => 'Proses Berhasil',
                                'text' => 'Data Berhasil Tersimpan'
                            ];                        
                            return $result;
                        } else {
                            $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitRuanganForm');
                            return ['data' => $errors,'status' => 422];
                        }
                    }
                }
            }            
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @author Rizal
    * @since 2018-01-08 15:03:18 
    * @param 
    * @return message
    * @desc 
    */
    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $modelKasusPenyakitRuangan = new KasusPenyakitRuangan;

            if ($request->post()) {
                // check and set model if existing
                $post = $request->post();
                $existing = KasusPenyakitRuangan::find(true)
                ->where(
                    [
                        'jeniskasuspenyakit_id' => $post['jeniskasuspenyakit_id'],
                        'ruangan_id' => $post['ruangan_id'],
                        'is_deleted' => true
                    ]
                    )
                ->one();
                
                if ($existing) {
                    $modelKasusPenyakitRuangan = $existing;
                    $modelKasusPenyakitRuangan->is_deleted = false;
                    $modelKasusPenyakitRuangan->deleted_by = null;
                }
                $modelKasusPenyakitRuangan->attributes = $request->post();
                
                if ($modelKasusPenyakitRuangan->save()) {
                    return ['message' => 'Data berhasil disimpan.'];
                } else {
                    $errors = DocoHelpers::parseError(
                        $modelKasusPenyakitRuangan->errors, 
                        'KasusPenyakitRuanganForm'
                    );
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    /**
    * @author Rizal
    * @since 2018-01-08 15:03:18 
    * @param 
    * @return message
    * @desc 
    */
    public function actionBatchCreate()
    {
        try {
            $request = Yii::$app->request;
            // $transaction = Yii::$app->db->beginTransaction();
            $saved = false;
            $errors = '';
            
            if ($request->post()) {
                // check and set model if existing
                $post = $request->post();
                $list_jeniskasuspenyakit_id = $post['list_jeniskasuspenyakit_id'];
                foreach ($list_jeniskasuspenyakit_id as $jeniskasuspenyakit_id) {
                    $modelKasusPenyakitRuangan = new KasusPenyakitRuangan;
                    $existing = KasusPenyakitRuangan::find(true)
                    ->where(
                        [
                            'jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,
                            'ruangan_id' => $post['ruangan_id']
                        ]
                        )
                    ->one();
                    
                    if ($existing) {
                        if ($existing->is_deleted == false) continue;
                        $modelKasusPenyakitRuangan = $existing;
                        $modelKasusPenyakitRuangan->is_deleted = false;
                        $modelKasusPenyakitRuangan->deleted_by = null;
                    } else {
                        $modelKasusPenyakitRuangan->attributes = $request->post();
                        $modelKasusPenyakitRuangan->jeniskasuspenyakit_id = $jeniskasuspenyakit_id;
                    }
                    
                    if ($modelKasusPenyakitRuangan->save()) {
                        $saved = true;
                    } else {
                        $saved = false;
                        $errors = DocoHelpers::parseError(
                            $modelKasusPenyakitRuangan->errors, 
                            'KasusPenyakitRuanganForm'
                        );
                        break;
                    }
                }

                if ($saved) {
                    // $transaction->commit();
                    return ['message' => 'Data berhasil disimpan.'];
                } else {
                    // $transaction->rollback();
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    /**
    * @author Rizal
    * @since 2018-01-08 16:09:27
    * @param $jeniskasuspenyakit_id int, $diagnosa_id int
    * @return 
    * @desc 
    */
    public function actionUpdate($jeniskasuspenyakit_id, $ruangan_id)
    {
        try {
            $request = Yii::$app->request;
            $model = KasusPenyakitRuangan::find()
            ->where([
                'jeniskasuspenyakit_id'=>$jeniskasuspenyakit_id,
                'ruangan_id'=>$ruangan_id
            ])
            ->one();
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data berhasil diubah.',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitDiagnosaForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
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

    public function actionUpdatePenyakitRuangan()
    {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('ruangan_id');
            $jeniskasuspenyakit_id = $request->get('jeniskasuspenyakit_id');
            $ruanganID = $request->post('ruangan_id');
            $jeniskasuspenyakitID = $request->post('jeniskasuspenyakit_id');

            $model = new KasusPenyakitRuangan;
            $post = $request->post();
            $model->ruangan_id_before = $ruangan_id;
            $model->jeniskasuspenyakit_id_before = $jeniskasuspenyakit_id;
            $model->type_method = 'update';
            if(empty($post)){
                $result = [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => 'Data Tidak Di Temukan'
                        ];                        
                return $result;
            }else{
                $model->attributes = $post;
                if (!$model->validate()) {
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitRuanganForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }else{
                    if ($model->jeniskasuspenyakit_id == $jeniskasuspenyakit_id && $model->ruangan_id == $ruangan_id) {
                        $getKasusPenyakitRuangan = KasusPenyakitRuangan::find()
                                ->Where([
                                        'ruangan_id' => $model->ruangan_id,
                                        'jeniskasuspenyakit_id'=> $model->jeniskasuspenyakit_id])
                                ->one();
                        $getKasusPenyakitRuangan->attributes = $post;
                        if ($getKasusPenyakitRuangan->save()) {
                            $result = [
                                'status' => 200,
                                'title' => 'Proses Berhasil',
                                'text' => 'Data Berhasil Tersimpan'
                            ];                        
                            return $result;
                        } else {
                            $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitRuanganForm');
                            return ['data' => $errors,'status' => 422];
                        }
                    }else{
                        $result = $model->dataMappingByTwoPK($model->ruangan_id, $model->jeniskasuspenyakit_id); // jika ada data namun delete true => false
                        if ($result) {
                            $resultDelete = $model->deleteMappingByTwoPK($ruangan_id, $jeniskasuspenyakit_id);
                            $res = [
                                'status' => 200,
                                'title' => 'Proses Berhasil',
                                'text' => 'Data Berhasil Tersimpan'
                            ];                        
                            return $res;
                        }else{
                            $resultDelete = $model->deleteMappingByTwoPK($ruangan_id, $jeniskasuspenyakit_id);
                            $model = new KasusPenyakitRuangan;
                            $getDefaultData = $model::getDefaultData();
                            $model->attributes = $post;
                            $model->last_modified_date = $getDefaultData->date;
                            $model->deleted_date = $getDefaultData->date;
                            if ($model->save()) {
                                $result = [
                                    'status' => 200,
                                    'title' => 'Proses Berhasil',
                                    'text' => 'Data Berhasil Tersimpan'
                                ];                        
                                return $result;
                            } else {
                                $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitRuanganForm');
                                return ['data' => $errors,'status' => 422];
                            }
                        }
                    }
                }
            } 
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
           \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @author Rizal
    * @since 2018-01-08 16:39:44
    * @param integer id
    * @return 
    * @desc 
    */
    public function actionDelete($jeniskasuspenyakit_id, $ruangan_id)
    {
        try {
            $result = KasusPenyakitRuangan::deleteMapping($jeniskasuspenyakit_id, $ruangan_id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDeletePenyakitRuangan()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id');
        $jeniskasuspenyakit_id = $request->get('jeniskasuspenyakit_id');
        try {
            $request = Yii::$app->request;
            $modelPendaftaran = new Pendaftaran;
            $getPendaftaran = $modelPendaftaran::find()->where(['ruangan_id'=>$ruangan_id,
                                                                'jeniskasuspenyakit_id'=>$jeniskasuspenyakit_id
                                                                ])->count();
            if($getPendaftaran > 0){
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Jenis Kasus Penyakit Ruangan ini sedang dipakai',
                            'status' => 422
                       ];
            }else{
                $model = new KasusPenyakitRuangan;
                $result = $model->deleteMappingByTwoPK($ruangan_id, $jeniskasuspenyakit_id);
                if($result){
                    return [
                            'status' => 200,
                            'title' => 'Hapus Berhasil',
                            'text' => 'Hapus Kasus Penyakit Ruangan Berhasil.',
                        ];
                }else{                
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitRuanganForm');
                    return $response['response'] = [
                                    'title' => 'Proses Gagal !',
                                    'text' => 'Data Gagal di hapus',
                                    'status' => 422
                               ];
                }         
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($jeniskasuspenyakit_id, $ruangan_id)
    {
        return $this->getDataNew($jeniskasuspenyakit_id, $ruangan_id);
    }

    public function actionGetListRuangan() {
        $all_ruangan = Ruangan::getRuangan();
        return $all_ruangan;
    }

    public function actionGetListKasusPenyakit() {
        $all_kasusPenyakit = JenisKasusPenyakit::getKasusPenyakit();
        return $all_kasusPenyakit;
    }

    private function getData($ruangan_id = null)
    {
        $kasusPenyakitRuangan = KasusPenyakitRuangan::find()
            ->select([
                'jeniskasuspenyakit_m.jeniskasuspenyakit_id',
                'jeniskasuspenyakit_m.jeniskasuspenyakit_nama',
                'ruangan_m.ruangan_id',
                'ruangan_m.ruangan_nama',
                'kasuspenyakitruangan_mp.additional_data',
                'kasuspenyakitruangan_mp.is_active',
            ])
            ->joinWith([
                'ruangan' => function ($query) {
                $query->select([
                    'ruangan_m.ruangan_nama',
                    'ruangan_m.ruangan_id'
                ]);
            }])
            ->joinWith([
                'jenisKasusPenyakit' => function ($query) {
                $query->select([
                    'jeniskasuspenyakit_m.jeniskasuspenyakit_nama',
                    'jeniskasuspenyakit_m.jeniskasuspenyakit_id'
                ]);
            }]);
        if ($ruangan_id) {
            $kasusPenyakitRuangan->where(['kasuspenyakitruangan_mp.ruangan_id' => $ruangan_id]);
        }

        return $kasusPenyakitRuangan;
    }

    public function actionExportExcel()
    {
        $title = 'Master Jenis Kasus Penyakit Ruangan';
        
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new KasusPenyakitRuanganView;
        $query = $model::find();

        $queries = $this->requestFilterData($model, $query, $request);

        $resData = $queries->asArray()->all();
        $result = [];

        $no = 0;
        foreach ($resData as $key => $value) {
            $no ++;
            $data[\Yii::t('app', 'Nama Ruangan')] = $value['ruangan_nama'];
            $data[\Yii::t('app', 'Jenis Kasus Penyakit')] = $value['jeniskasuspenyakit_nama'];
            $data[\Yii::t('app', 'Status')] = ($value['is_active'] == false) ? 'Tidak Aktif' : 'Aktif';
            
            $result[] = $data;
        }

        $header = array();

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads",
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

     /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data table
    */
    public function actionExportPdf()
    {
        try {
            $title = 'Master Jenis Kasus Penyakit Ruangan';

            $request = Yii::$app->request;
            $get = $request->get();
            $model = new KasusPenyakitRuanganView;
            $query = $model::find();

            $queries = $this->requestFilterData($model, $query, $request);

            $data = $queries->asArray()->all();
            $result = [];
            $rowNum = 0;
            foreach ($data as $key => $value) {
                $rowNum ++;
                $value['rowNum'] = $rowNum ;
                $value['is_active'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $value;
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $result,
                    'title' => $title,
                ]),
            ];
            $print->Output();
                    
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
