<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Pendidikan;
use Doco\components\DocoActiveController;

class PendidikanController extends \app\components\DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\Pendidikan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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
        try {
            $request = Yii::$app->request;
            $result = $this->getData()
                            ->limit($request->post('length',10))
                            ->offset($request->post('start',0));

            if ($indexing = $request->post('indexing')) {
                $result->andFilterWhere(['ILIKE', 'indexing_m.indexing_nama', $indexing]);
            }

            if ($keyword = $request->post('keyword')) {
                $result->andFilterWhere(['ILIKE','pendidikan_nama',$keyword])
                       ->orFilterWhere(['ILIKE','pendidikan_namalainnya',$keyword]);
            }
            $status = $request->post('is_active');

            $status = $status ? true : false;
            $result->andWhere(['pendidikan_m.is_active' => $status]);

            if ($order = $request->post('orderby')) {
                $dir = (int) $request->post('dir');
                $result->orderby([$order => $dir]);
            }

            return [
                'data' => $result->asArray()->all(),
                'count' => $result->count()
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

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Pendidikan::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanForm');
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

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new Pendidikan;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanForm');
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

    public function actionDelete($id)
    {
        try {
            $result = Pendidikan::updateByPk($id);
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
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $pendidikan = Pendidikan::find()
                        ->select([
                                'pendidikan_m.pendidikan_id',
                                'pendidikan_m.indexing_id',
                                'pendidikan_m.pendidikan_urutan',
                                'pendidikan_m.pendidikan_urutan',
                                'pendidikan_m.pendidikan_nama',
                                'pendidikan_m.pendidikan_namalainnya',
                                'pendidikan_m.is_active',
                        ])->joinWith([
                        'indexing' => function ($query) {
                            $query->select(['indexing_m.indexing_nama','indexing_m.indexing_id']);
                        }]);
        if ($id) {
            $pendidikan->where(['pendidikan_id' => $id]);
        }

        return $pendidikan;
    }
}