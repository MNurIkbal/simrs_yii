<?php
namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Modul;
use Doco\components\DocoHelpers;
use yii\web\HttpException;

class ModulController extends \Doco\components\DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\Modul';

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
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionCreate()
    {
        throw new HttpException(404, 'The requested Item could not be found.');
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Modul::findOne($id);
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
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete($id)
    {
        throw new HttpException(404, 'The requested Item could not be found.');
    }

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $pendidikan = Modul::find()
                        ->select([
                                'modul_id',
                                'modul_nama',
                                'modul_namalainnya',
                                'modul_fungsi',
                                'url_modul',
                                'icon_modul',
                                'modul_key',
                                'modul_urutan',
                                'modul_kategori',
                                'imagemodul',
                                'is_active'
                        ]);
        if ($id) {
            $pendidikan->where(['modul_id' => $id]);
        }

        return $pendidikan;
    }
}