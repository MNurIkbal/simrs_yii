<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Spesialis;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PegawaiSpesialisView;
use app\modules\v1\models\DataDokterView;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\helpers\ArrayHelper;


class SpesialisController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Spesialis';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE"];
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

    public function actionIndex() {
        try {
            $request = Yii::$app->request;

            $model = new Spesialis;
            $query = $model::find()->select([
                'spesialis_id',
                'spesialis_kode',
                'spesialis_nama',
                'spesialis_namalainnya',
                'is_active',
            ])
            ->where(['is_deleted' => false])
            ->asArray();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionGetSpesialisPegawai() {
        try {
            $request = Yii::$app->request;

            $model = new DataDokterView;
            $query = $model::find()->select([
                'pegawai_id',
                'nomorindukpegawai',
                'nama_pegawai',
                'spesialis_id',
                'spesialis_nama',
            ])
            ->asArray();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @author Fajar Supriadi
    * @since 2022-01-03 11:17:44 
    * @param 
    * @return response result of create
    * @desc 
    */
    public function actionCreate() {
        try {
            $request = Yii::$app->request;
            $model = new Spesialis;
            $post = $request->post();
            $model->attributes = $post;
            if ($model->validate() && $model->save()) {

                $id = $model->getPrimaryKey();
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil',
                    'text' => 'success',
                    'message' => 'Data berhasil disimpan.',
                    'spesialis_id' => $id
                ];
                
            }else{
                $errors = DocoHelpers::parseError($model->errors,'SpesialisForm');
                return [
                    'data' => $errors,
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

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Spesialis::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data berhasil diubah.',
                    ];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdateSpesialis()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = Spesialis::findOne($post['spesialis_id']);
            $model->attributes = $post;
            if ($model->validate() && $model->save()) { 
                $id = $model->getPrimaryKey();
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil',
                    'text' => 'success',
                    'message' => 'Data berhasil diubah.',
                    'spesialis_id' => $id
                ];
            }else{
                $errors = DocoHelpers::parseError($model->errors,'SpesialisForm');
                return [
                    'data' => $errors,
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

    public function actionDelete($id)
    {
        try {
            $model = new Spesialis;
            $result = $model->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetSpesialisById($id = null) 
    {
        $result = [];        
        if ($id) {
            $query = Spesialis::find()->where([
                'spesialis_id' => $id
            ])->one();
            $result = $query;
        }
        return $result;
    }

    public function actionPackSpesialisPegawai($id = null) 
    {
        $result = [];
        $spesialis = Spesialis::find()->select([
            'spesialis_id',
            'spesialis_nama',
        ])->where([
            'is_active' => true,
            'is_deleted' => false
        ])->orderBy(['spesialis_nama' => SORT_ASC])->asArray()->all();
        $result['list_spesialis'] = $spesialis;

        if ($id) {
            $query = Pegawai::find()->select([
                'pegawai_id',
                'nama_pegawai',
                'spesialis_id',
            ])->where([
                'pegawai_id' => $id
            ])->one();
            $result['pegawai'] = $query;
        }

        return $result;
    }

    public function actionUpdateSpesialisPegawai($id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = Pegawai::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->scenario = "update-spesialis";
                $model->spesialis_id = $post['spesialis_id'];

                if ($model->save()) {
                    return [
                        'message' => 'Data berhasil diubah.',
                    ];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeleteSpesialis()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = Spesialis::findOne($post['spesialis_id']);

            if (empty($model)){
                return [
                    'message' => 'Data spesialis tidak ditemukan!',
                    'status' => 422
                ];
            }
            
            $model->is_deleted = true;
            $model->deleted_date = date('Y-m-d H:i:s');
            $jwt = Yii::$app->jwt->user;
            $model->deleted_by = !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1';

            if ($model->save()) { 
                return [
                    'status' => 200
                ];
            }else{
                $errors = DocoHelpers::parseError($model->errors,'SpesialisForm');
                return [
                    'data' => $errors,
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

    public function actionGetSpesialis()
    {
        $model = Spesialis::find();
        $model->where(['is_active' => 't']);
        $model->andWhere(['is_deleted' => 'f']);
        return $model->asArray()->all();      
    }

}
