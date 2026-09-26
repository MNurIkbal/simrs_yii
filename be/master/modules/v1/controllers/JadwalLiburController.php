<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;

use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\components\DocoHelpers;


class JadwalLiburController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JadwalLibur';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
        $verbs["create"] = ["POST"];
        $verbs["view"] = ["GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["delete"] = ["DELETE"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
        ];

        return $behaviors;
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

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new $this->modelClass;
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');

            if($request->get('start')) {
                $start = date('Y-m-d', strtotime($request->get('start')));
            }

            if($request->get('end')) {
                $end = date('Y-m-d', strtotime($request->get('end')));
            }
            $query = $model->find();

            $query = $query->where(['between', 'tgl_libur', $start, $end]);
            return $query->asArray()->all();
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
            $post = $request->post();
            $model = new $this->modelClass;
            $model->attributes = $post;
            if ($model->validate()) {
                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'JadwalLiburForm');
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors,'JadwalLiburForm');
            }
            return [
                'data' => $errors,
                'status' => 422
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
            $model = new $this->modelClass;
            $model = $model::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di ubah',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'JadwalLiburForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
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

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $jadwalLiburId = ArrayHelper::getValue($post, 'jadwallibur_id');
        $password = ArrayHelper::getValue($post, 'password');
        $result = [];

        try {
            if (!$jadwalLiburId) {
                return $this->helper->response([
                    'title' => 'Proses Gagal!',
                    'text' => 'Jadwal Libur ID tidak boleh kosong.',
                ], 422);
            }

            $valid = $this->helper->validatePassword($password);
            if (!$valid) {
                return $this->helper->response([
                    'title' => 'Proses Gagal!',
                    'text' => 'Password salah.',
                ], 422);
            }
            $model = new $this->modelClass;
            $model = $model::find()
                ->where(['jadwallibur_id' => $jadwalLiburId])
                ->one();

            if ($model->delete()) {
                return $this->helper->response([
                    'text' => 'Data berhasil Hapus',
                    'jadwallibur_id' => $jadwalLiburId,
                ]);
            }

            return $this->helper->response($model->errors,422,'HapusJadwalLiburForm');
        } catch (\yii\db\Exception $e) {
            Yii::error($e->getMessage());
            return $this->helper->response(['text' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return $this->helper->response(['text' => $e->getMessage()], 500);
        }
    }

    public function actionView($id)
    {
        try {
            $model = new $this->modelClass;
            $model = $model::findOne($id);
            if(!$model) {
                $errorMessage = "Data tidak ditemukan";
                return $this->responseJson(200, $errorMessage, $model);
            }
            return $model;
        } catch (\yii\db\Exception $e) {
            Yii::error($e->getMessage());
            return $this->helper->response(['text' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return $this->helper->response(['text' => $e->getMessage()], 500);
        }
    }
}