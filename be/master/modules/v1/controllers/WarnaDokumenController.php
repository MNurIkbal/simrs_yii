<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\WarnaDokumen;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;

class WarnaDokumenController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\WarnaDokumen';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["delete"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
      $model = new WarnaDokumen;
      $query = $model::find();
      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      return new ActiveDataProvider([
          'query' => $query,
      ]);
    }

    public function actionView($id = null)
    {
      return $this->getData($id)->asArray()->one();
    }

    public function getData($id = null)
    {
      $data = WarnaDokumen::find()
                      ->select([
                              'warnadokrekammedik_m.warnadokrm_id',
                              'warnadokrekammedik_m.warnadokrm_namawarna',
                              'warnadokrekammedik_m.warnadokrm_kodewarna',
                              'warnadokrekammedik_m.is_active'
                      ]);
      if ($id) {
          $data->where(['warnadokrekammedik_m.warnadokrm_id' => $id]);
      }

      return $data;
    }

    public function actionDelete($id)
    {
        try {
            $result = (new WarnaDokumen)->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = WarnaDokumen::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'WarnaDokumenForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new \yii\db\Exception("Data Tidak Di Temukan");
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
        try {
            $request = Yii::$app->request;
            $model = new WarnaDokumen;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    
                    return [
                        'data' => $model->errors,
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

    public function actionListWarna()
    {
        try {
            $model = new WarnaDokumen;
            $model = $model->find()
                ->andWhere(['is_active'=>true]);

            $count = $model->count();
            $data = $model
                ->select('warnadokrm_id,warnadokrm_namawarna')
                ->orderBy('warnadokrm_id')
                ->asArray()
                ->all();

            $results = [
                'data'=>$data,
                'count'=>$count,
            ];
            return $results;
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
