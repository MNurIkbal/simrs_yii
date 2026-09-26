<?php

/**
 * @Author: Muhammad Fajar Nugroho Alam
 * @Date:   2021-03-15 15:52:00
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\InfoRakObatView;
use Yii;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\Rak;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use yii\helpers\Json;

class RakController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Rak';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update"] = ["POST", "PUT"];
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

    public function actionGetData()
    {
        try {
            $model = new InfoRakObatView;
            $query = $model::find()->where([
                        'is_active' => true,
                    ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query
            ]);
        }
        catch (\yii\db\Exception $e) {
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
        $request = Yii::$app->request;
        try {
            $model = new Rak;
            $post = $request->post();
            $model->scenario = 'default';
            $model->attributes = $post;

            if($model->validate()){
                if($model->save()){
                    $return = [
                        'text' => 'Data Berhasil Di Simpan',
                        'title' => 'Proses Berhasil',
                        'code' => 200
                    ];
                    return $return;
                }else{
                    $errors = DocoHelpers::parseError($model->errors, 'Rak');
                    return ['data' => $errors, 'status' => 422];
                }
            }else{
                $errors = DocoHelpers::parseError($model->errors, 'ManufakturForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        }catch(\yii\db\Exception $e)
        {
            return ['message' => $e->getMessage()];
        }catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try{
            $model = Rak::findOne($id);
            $model->scenario = 'default';
            $model->attributes = $request->post();
            if($model->validate()){
                if($model->save()) {
                    $return = [
                        'text' => 'Data Berhasil Di Ubah',
                        'title' => 'Proses Berhasil!',
                        'code' => 200
                    ];

                    return $return;
                }
                else {
                    $errors = DocoHelpers::parseError($model->errors,'ManufakturForm');
                    return ['data' => $errors,'status' => 422];
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'ManufakturForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
            
        }catch(\yii\db\Exception $e)
        {
            return ['message' => $e->getMessage()];
        }catch(\Exception $e)
        {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');

        $delete = (new Rak)->delete($id);
        
        return [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
    }

    public function actionGetListRuangan()
    {
        $models = new Ruangan;
        $query = $models::find()->select(['ruangan_id','ruangan_nama'])->where(['is_active' => true])->orderBy('ruangan_nama ASC')->asArray()->all();

        return $query;

    }

    public function actionGetListParentRak()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id');
        $rakobat_id = $request->get('rakobat_id');
        
        $model = new Rak;
        $query = $model::find()->select(['rakobat_id','rakobat_nama'])->where(['ruangan_id' => $ruangan_id, 'is_active' => true]);
        if (!empty($rakobat_id)) {
            $query->andWhere(['<>', 'rakobat_id', $rakobat_id]);
        }
        $query->andWhere(['is', 'parentrakobat_id', null]);
        return $query->asArray()->all();
    }

    public function actionRakById() {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = Rak::find()->where(['rakobat_id' => $id])->one();
        $listRuangan = $this->actionGetListRuangan();
        $parentRak = Rak::find()->where(['rakobat_id' => $model->parentrakobat_id])->one();
        return [
            'query' => $model,
            'listRuangan' => $listRuangan,
            'parentRak' => $parentRak
        ];
    }
}


?>