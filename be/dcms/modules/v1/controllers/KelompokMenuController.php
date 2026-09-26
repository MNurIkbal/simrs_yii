<?php
namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\KelompokMenu;
use app\components\DocoHelpers;

class KelompokMenuController extends \app\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KelompokMenu';

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

            if ($indexing = $request->post('kelompok_menu')) {
                $result->andFilterWhere(['ILIKE', 'kelmenu_nama', $indexing]);
            }

            $status = $request->post('is_active');

            $status = $status ? true : false;
            $result->andWhere(['is_active' => $status]);

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
            $model = KelompokMenu::findOne($id);
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
            $model = new KelompokMenu;
            if ($request->post()) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'status' => 201,
                        'message' => 'Data Berhasil di simpan'
                    ];
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
            $result = KelompokMenu::find()->joinWith([
                'item'
            ])->where([
                'kelompokmenu_k.kelmenu_id' => $id
            ])->one();
            
            if (empty($result->item)) {
                $result->delete($id);
                return [
                    "title" => "Proses Berhasil !",
                    "message" => "Data berhasil dihapus",
                ];
            }
            return [
                'status' => 422,
                'message' => 'Tidak dapat menghapus ' . $result->kelmenu_nama,
                "title" => "Proses Gagal !",
            ];
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
        $pendidikan = KelompokMenu::find()->select([
                            'kelmenu_nama',
                            'kelmenu_id',
                            'is_active'
                        ]);
        if ($id) {
            $pendidikan->where(['kelmenu_id' => $id]);
        }

        return $pendidikan;
    }
}
