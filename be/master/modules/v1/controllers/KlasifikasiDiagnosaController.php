<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KlasifikasiDiagnosa;

class KlasifikasiDiagnosaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KlasifikasiDiagnosa';

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
        $model = new KlasifikasiDiagnosa;
        $query = $model::find(true);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDelete($id)
    {
        try {
            $result = (new KlasifikasiDiagnosa)->delete($id);
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
            $model = new KlasifikasiDiagnosa;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KlasifikasiDiagnosa');
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
      $data = KlasifikasiDiagnosa::find()
                      ->select([
                              'klasifikasidiagnosa_m.klasifikasidiagnosa_id',
                              'klasifikasidiagnosa_m.klasifikasidiagnosa_kode',
                              'klasifikasidiagnosa_m.klasifikasidiagnosa_nama',
                              'klasifikasidiagnosa_m.klasifikasidiagnosa_namalain',
                              'klasifikasidiagnosa_m.klasifikasidiagnosa_desc',
                              'klasifikasidiagnosa_m.dtd_id',
                              'klasifikasidiagnosa_m.is_active'
                      ]);
      if ($id) {
          $data->where(['klasifikasidiagnosa_m.klasifikasidiagnosa_id' => $id]);
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
