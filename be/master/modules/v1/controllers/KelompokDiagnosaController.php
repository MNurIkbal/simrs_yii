<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KelompokDiagnosa;

class KelompokDiagnosaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KelompokDiagnosa';

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
        $model = new KelompokDiagnosa;
        $query = $model::find(true);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDelete($id)
    {
        try {
            $result = (new KelompokDiagnosa)->delete($id);
            return $result;
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
            $model = new KelompokDiagnosa;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelompokDiagnosa');
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

    public function actionView($id = null)
    {
      return $this->getData($id)->asArray()->one();
    }

    public function getData($id = null)
    {
      $data = KelompokDiagnosa::find()
                      ->select([
                              'kelompokdiagnosa_m.kelompokdiagnosa_id',
                              'kelompokdiagnosa_m.kelompokdiagnosa_nama',
                              'kelompokdiagnosa_m.kelompokdiagnosa_namalainnya',
                              'kelompokdiagnosa_m.is_active'
                      ]);
      if ($id) {
          $data->where(['kelompokdiagnosa_m.kelompokdiagnosa_id' => $id]);
      }

      return $data;
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = KelompokDiagnosa::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KelompokDiagnosa');
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

}
