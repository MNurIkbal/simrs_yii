<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\KasusPenyakitDiagnosa;
use app\modules\v1\models\KasusPenyakitDiagnosaView;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\PasienMorbiditasT;
use app\modules\v1\models\Pendaftaran;
use Doco\components\DocoHelpers;
use yii\db\Query;
use yii\db\Connection;
use Doco\components\DocoPrint;

class KasusPenyakitDiagnosaController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KasusPenyakitDiagnosa';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ListData"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["getListDiagnosa"] = ["POST", "GET"];
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

    /**
    * @author Rizal
    * @since 2018-01-05 14:06:35 
    * @param 
    * @return 
    * @desc 
    */

    public function actionIndex()
    { 
        try {
            $request = Yii::$app->request;
            $model = new KasusPenyakitDiagnosaView;
            $query = $model::find();

            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){

                if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                    $query->andWhere(['jeniskasuspenyakit_nama' => $advancedFilters['jeniskasuspenyakit_nama'] ]);
                }

                if (isset($advancedFilters['diagnosa_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(diagnosa_nama)', strtolower($advancedFilters['diagnosa_nama']) ]);
                }

                if (isset($advancedFilters['diagnosa_kode']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(diagnosa_kode)', strtolower($advancedFilters['diagnosa_kode']) ]);
                }

                if (isset($advancedFilters['is_active']) ) {
                    $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
                }

            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['jeniskasuspenyakit_nama'=>SORT_ASC,
                            'diagnosa_kode'=>SORT_ASC]);
            
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

    public function actionListData()
    {
        $data = $this->getDataNew();
        $result = ['data' => $data, 'totalCount' => count($data)];
        return $result;
    }

    /**
    * @author Johndoe
    * @since 2018-03-28 18:03: 
    * @param 
    * @return message
    * @desc 
    */
    public function getDataNew($jeniskasuspenyakit_id = null, $diagnosa_id = null)
    {
        $where = '';
        if(isset($jeniskasuspenyakit_id) && isset($diagnosa_id)) {
            $where = ' AND t.jeniskasuspenyakit_id = '.$jeniskasuspenyakit_id.' AND t.diagnosa_id = '.$diagnosa_id.' ';
        }

        $order = ' order by a.jeniskasuspenyakit_nama, c.diagnosa_nama'; 

        if(isset($_GET['advanced-filter'])){            
            $filter = $_GET['advanced-filter'];                                                 
            if(isset($filter['jeniskasuspenyakit_nama'])){             
                $where .= " and a.jeniskasuspenyakit_nama ILIKE '%".$filter['jeniskasuspenyakit_nama']."%'";
            }       
            if(isset($filter['diagnosa_kode'])){             
                $where .= " and c.diagnosa_kode = '".$filter['diagnosa_kode']."'";
            }
            if(isset($filter['diagnosa_nama'])){             
                $where .= " and c.diagnosa_nama = '".$filter['diagnosa_nama']."'";
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
                t.diagnosa_id,
                a.jeniskasuspenyakit_nama,
                c.diagnosa_kode,
                c.diagnosa_nama,
                t.is_active
                FROM
                kasuspenyakitdiagnosa_mp AS t
                INNER JOIN jeniskasuspenyakit_m AS a ON t.jeniskasuspenyakit_id = a.jeniskasuspenyakit_id
                INNER JOIN diagnosa_m AS c ON t.diagnosa_id = c.diagnosa_id
                WHERE t.is_deleted = false ".$where.$order." ";

        if(isset($jeniskasuspenyakit_id) && isset($diagnosa_id)) {
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
    public function actionCreatePenyakitDiagnosa()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
           
            $model = new KasusPenyakitDiagnosa;
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
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitDiagnosaForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }else{
                    $result = $model->dataMappingByTwoPK($model->jeniskasuspenyakit_id, $model->diagnosa_id);
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
                            $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitDiagnosaForm');
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
            $modelKasusPenyakitDiagnosa = new KasusPenyakitDiagnosa;

            if ($request->post()) {
                // check and set model if existing
                $post = $request->post();
                $existing = KasusPenyakitDiagnosa::find(true)
                ->where(
                    [
                        'jeniskasuspenyakit_id' => $post['jeniskasuspenyakit_id'],
                        'diagnosa_id' => $post['diagnosa_id'],
                        'is_deleted' => true
                    ]
                    )
                ->one();
                
                if ($existing) {
                    $modelKasusPenyakitDiagnosa = $existing;
                    $modelKasusPenyakitDiagnosa->is_deleted = false;
                    $modelKasusPenyakitDiagnosa->deleted_by = null;
                }
                $modelKasusPenyakitDiagnosa->attributes = $request->post();
                
                if ($modelKasusPenyakitDiagnosa->save()) {
                    return ['message' => 'Data berhasil disimpan.'];
                } else {
                    $errors = DocoHelpers::parseError(
                        $modelKasusPenyakitDiagnosa->errors, 
                        'KasusPenyakitDiagnosaForm'
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
            $saved = true;
            $errors = '';
            
            if ($request->post()) {
                // check and set model if existing
                $post = $request->post();
                $list_diagnosa_id = $post['list_diagnosa_id'];
                foreach ($list_diagnosa_id as $diagnosa_id) {
                    $modelKasusPenyakitDiagnosa = new KasusPenyakitDiagnosa;
                    $existing = KasusPenyakitDiagnosa::find(true)
                    ->where(
                        [
                            'jeniskasuspenyakit_id' => $post['jeniskasuspenyakit_id'],
                            'diagnosa_id' => $diagnosa_id
                        ]
                        )
                    ->one();
                    
                    if ($existing) {
                        if ($existing->is_deleted == false) continue;
                        $modelKasusPenyakitDiagnosa = $existing;
                        $modelKasusPenyakitDiagnosa->is_deleted = false;
                        $modelKasusPenyakitDiagnosa->deleted_by = null;
                    } else {
                        $modelKasusPenyakitDiagnosa->attributes = $request->post();
                        $modelKasusPenyakitDiagnosa->diagnosa_id = $diagnosa_id;
                    }
                    
                    if ($modelKasusPenyakitDiagnosa->save()) {
                        $saved = true;
                    } else {
                        $saved = false;
                        $errors = DocoHelpers::parseError(
                            $modelKasusPenyakitDiagnosa->errors, 
                            'KasusPenyakitDiagnosaForm'
                        );
                        return [
                            'data' => $errors,
                            'status' => 422,
                        ];
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


    public function actionBatchUpdate($jeniskasuspenyakit_id, $diagnosa_id)
    {
        try {
            $request = Yii::$app->request;
            $transaction = Yii::$app->db->beginTransaction();
            $saved = false;
            $errors = '';
            
            if ($request->post()) {
                // check and set model if existing
                $post = $request->post('KasusPenyakitDiagnosaForm');
                $list_diagnosa_id = $post['list_diagnosa_id'];
                $existing = KasusPenyakitDiagnosa::find(true)
                ->where(
                    [
                        'jeniskasuspenyakit_id' => $jeniskasuspenyakit_id,
                        'diagnosa_id' => $diagnosa_id
                    ]
                    )
                ->one();
                if($existing) {
                    $jwt = Yii::$app->jwt->user;
                    $update = KasusPenyakitDiagnosa::updateAll([
                        'is_deleted' => true,
                        'deleted_date' => date('Y-m-d H:i:s', time()),
                        'deleted_by' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1'
                    ], 
                        'jeniskasuspenyakit_id = '.$jeniskasuspenyakit_id.' and diagnosa_id = '.$diagnosa_id.' ');
                    
                    if($update) {
                        foreach ($list_diagnosa_id as $diagnosa_id) {
                            $modelKasusPenyakitDiagnosa = new KasusPenyakitDiagnosa;
                            $modelKasusPenyakitDiagnosa->attributes = $request->post('KasusPenyakitDiagnosaForm');
                            $modelKasusPenyakitDiagnosa->diagnosa_id = $diagnosa_id;
                            if ($modelKasusPenyakitDiagnosa->save()) {
                                $saved = true;
                            } 
                            else {
                                $saved = false;
                            }

                            if($saved == true) {
                                $transaction->commit();
                                return ['message' => 'Data berhasil disimpan.'];
                            }
                            else {
                                $transaction->rollback();
                                $errors = DocoHelpers::parseError(
                                    $modelKasusPenyakitDiagnosa->errors, 
                                    'KasusPenyakitDiagnosaForm'
                                );
                                return [
                                    'data' => $errors,
                                    'status' => 422,
                                ];
                            }
                        }
                    }
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
    public function actionUpdate($jeniskasuspenyakit_id, $diagnosa_id)
    {
        try {
            $request = Yii::$app->request;
            $model = KasusPenyakitDiagnosa::find()
            ->where([
                'jeniskasuspenyakit_id'=>$jeniskasuspenyakit_id,
                'diagnosa_id'=>$diagnosa_id
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

    public function actionUpdatePenyakitDiagnosa()
    {
        try {
            $request = Yii::$app->request;
            $jeniskasuspenyakit_id = $request->get('jeniskasuspenyakit_id');
            $diagnosa_id = $request->get('diagnosa_id');
            $jeniskasuspenyakitID = $request->post('jeniskasuspenyakit_id');
            $diagnosaID = $request->post('diagnosa_id');

            $model = new KasusPenyakitDiagnosa;
            $post = $request->post();
            $model->jeniskasuspenyakit_id_before = $jeniskasuspenyakit_id;
            $model->diagnosa_id_before = $diagnosa_id;
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
                    $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitDiagnosaForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }else{
                    if ($model->jeniskasuspenyakit_id == $jeniskasuspenyakit_id && $model->diagnosa_id == $diagnosa_id) {
                        $getKasusPenyakitDiagnosa = KasusPenyakitDiagnosa::find()
                                ->Where(['jeniskasuspenyakit_id'=> $model->jeniskasuspenyakit_id,
                                        'diagnosa_id' => $model->diagnosa_id])
                                ->one();
                        $getKasusPenyakitDiagnosa->attributes = $post;
                        if ($getKasusPenyakitDiagnosa->save()) {
                            $result = [
                                'status' => 200,
                                'title' => 'Proses Berhasil',
                                'text' => 'Data Berhasil Tersimpan'
                            ];                        
                            return $result;
                        } else {
                            $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitDiagnosaForm');
                            return ['data' => $errors,'status' => 422];
                        }
                    }else{
                        $result = $model->dataMappingByTwoPK($model->jeniskasuspenyakit_id, $model->diagnosa_id); // jika ada data namun delete true => false
                        if ($result) {
                            $resultDelete = $model->deleteMappingByTwoPK($jeniskasuspenyakit_id, $diagnosa_id);
                            $res = [
                                'status' => 200,
                                'title' => 'Proses Berhasil',
                                'text' => 'Data Berhasil Tersimpan'
                            ];                        
                            return $res;
                        }else{
                            $resultDelete = $model->deleteMappingByTwoPK($jeniskasuspenyakit_id, $diagnosa_id);
                            $model = new KasusPenyakitDiagnosa;
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
                                $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitDiagnosaForm');
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

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $jeniskasuspenyakit_id = $request->get('jeniskasuspenyakit_id');
        $diagnosa_id = $request->get('diagnosa_id');
        try {
            $model = new KasusPenyakitDiagnosa;
            $result = $model->deleteMappingByTwoPK($jeniskasuspenyakit_id, $diagnosa_id);
            if($result){
                return [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Kasus Penyakit Diagnosa Berhasil.',
                    ];
            }else{                
                $errors = DocoHelpers::parseError($model->errors,'KasusPenyakitDiagnosaForm');
                return $response['response'] = [
                                'title' => 'Proses Gagal !',
                                'text' => 'Data Gagal di hapus',
                                'status' => 422
                           ];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($jeniskasuspenyakit_id, $diagnosa_id)
    {
        return $this->getDataNew($jeniskasuspenyakit_id, $diagnosa_id);
    }

    public function actionGetListDiagnosa() {
        $all_diagnosa = Diagnosa::getDiagnosa();
        return $all_diagnosa;
    }

    public function actionGetListKasusPenyakit() {
        $all_kasusPenyakit = JenisKasusPenyakit::getKasusPenyakit();
        return $all_kasusPenyakit;
    }

    private function getData($id = null)
    {
        $kasusPenyakitDiagnosa = KasusPenyakitDiagnosa::find()
            ->select([
                'jeniskasuspenyakit_m.jeniskasuspenyakit_id',
                'jeniskasuspenyakit_m.jeniskasuspenyakit_nama',
                'diagnosa_m.diagnosa_id',
                'diagnosa_m.diagnosa_kode',
                'diagnosa_m.diagnosa_nama',
                'kasuspenyakitdiagnosa_mp.additional_data',
                'kasuspenyakitdiagnosa_mp.is_active',
            ])
            ->joinWith([
                'diagnosa' => function ($query) {
                $query->select([
                    'diagnosa_m.diagnosa_nama',
                    'diagnosa_m.diagnosa_kode',
                    'diagnosa_m.diagnosa_id'
                ]);
            }])
            ->joinWith([
                'jenisKasusPenyakit' => function ($query) {
                $query->select([
                    'jeniskasuspenyakit_m.jeniskasuspenyakit_nama',
                    'jeniskasuspenyakit_m.jeniskasuspenyakit_id'
                ]);
            }]);
        if ($id) {
            $kasusPenyakitDiagnosa->where(['kasuspenyakitdiagnosa_mp.jeniskasuspenyakit_id' => $id]);
        }

        return $kasusPenyakitDiagnosa;
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $title = 'Master Jenis Kasus Penyakit Diagnosa';
        $get = $request->get();

        $model = new KasusPenyakitDiagnosaView;
        $query = $model::find();
        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                $query->andWhere(['jeniskasuspenyakit_nama' => $advancedFilters['jeniskasuspenyakit_nama'] ]);
            }

            if (isset($advancedFilters['diagnosa_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(diagnosa_nama)', strtolower($advancedFilters['diagnosa_nama']) ]);
            }

            if (isset($advancedFilters['diagnosa_kode']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(diagnosa_kode)', strtolower($advancedFilters['diagnosa_kode']) ]);
            }

            if (isset($advancedFilters['is_active']) ) {
                $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
            }

        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['jeniskasuspenyakit_nama'=>SORT_ASC,
                            'diagnosa_kode'=>SORT_ASC]);
        $resData = $query->asArray()->all();
        $result = [];

        $no = 0;
        foreach ($resData as $key => $value) {
            $no ++;
            $data[\Yii::t('app', 'Jenis Kasus Penyakit')] = $value['jeniskasuspenyakit_nama'];
            $data[\Yii::t('app', 'Kode Diagnosa')] = "'".$value['diagnosa_kode']."'";
            $data[\Yii::t('app', 'Nama Diagnosa')] = $value['diagnosa_nama'];
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
            $request = Yii::$app->request;
            $title = 'Master Jenis Kasus Penyakit Diagnosa';
            $get = $request->get();

            $request = Yii::$app->request;
            $model = new KasusPenyakitDiagnosaView;
            $query = $model::find();

            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){

                if (isset($advancedFilters['jeniskasuspenyakit_nama']) ) {
                    $query->andWhere(['jeniskasuspenyakit_nama' => $advancedFilters['jeniskasuspenyakit_nama'] ]);
                }

                if (isset($advancedFilters['diagnosa_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(diagnosa_nama)', strtolower($advancedFilters['diagnosa_nama']) ]);
                }

                if (isset($advancedFilters['diagnosa_kode']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(diagnosa_kode)', strtolower($advancedFilters['diagnosa_kode']) ]);
                }

                if (isset($advancedFilters['is_active']) ) {
                    $query->andFilterWhere(['is_active' =>  $advancedFilters['is_active'] ]);
                }

            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['jeniskasuspenyakit_nama'=>SORT_ASC,
                            'diagnosa_kode'=>SORT_ASC]);
            $data = $query->asArray()->all();
            $result = [];
            foreach ($data as $key => $value) {
                $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $value;
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('cetak', [
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

    public function getDataDiagnosa($jeniskasuspenyakit_id)
    {
        $query = Diagnosa::find();
        $query->where(['is_active' => true, 'is_deleted' => false])
                        ->orderBy(['diagnosa_nama'=> SORT_ASC ]);
        $query->andWhere(['jeniskasuspenyakit_id' => $jeniskasuspenyakit_id]);
        $result = $query->asArray()->all();

        return $result;
    }

    public function actionGetDiagnosaByJenisPenyakit(){

        try{
            $request = Yii::$app->request;
            $jeniskasuspenyakit_id = $request->get('jeniskasuspenyakit_id');
            $diagnosa = $this->getDataDiagnosa($jeniskasuspenyakit_id);
            $arrdiagnosa = ArrayHelper::map($diagnosa, 'diagnosa_id', 'diagnosa_nama');        
            return [
                'diagnosa' => $arrdiagnosa
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
