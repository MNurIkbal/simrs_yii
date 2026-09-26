<?php

namespace app\modules\v1\controllers;

use Yii;

use app\modules\v1\components\BpjsController;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoMessages;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Bpjs;

class RujukanKhususController extends BpjsController
{
    public $modelClass = 'app\modules\v1\models\RujukanKhusus';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["hapus-rujukan"] = ["DELETE"];
        $verbs["create-rujukan-khusus"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);

        return $actions;
    }

    public function actionIndex()
    {
        return null;
    }

    public function actionHapusRujukan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new Bpjs;
        $result =[];
        
        try {
            $password = DocoHelpers::decrypt($post['password']);
            $valid = $this->cekValidasiPassword($password);
            if (!$valid) {
                return [
                    'message' => 'Password salah.',
                    'code' => 422
                ];
            }
            $kodeDokter = ArrayHelper::getValue($post, 'idrujukan', null);
            $data = [
                'request' => [
                    't_rujukan' => [
                        'idRujukan' => ArrayHelper::getValue($post,'idrujukan',null),
                        'noRujukan' => ArrayHelper::getValue($post,'norujukan',null),
                        'user' =>  ArrayHelper::getValue($post,'username',null),
                    ]
                ]
            ];
            $result = $model->hapusRujukanKhusus($data);
            return $result['metaData'];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionCreateRujukanKhusus()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $model = new Bpjs;
        $result = $t_rujukan_khusus = $response = [];
        
        try {
            $t_rujukan_khusus["noRujukan"] = ArrayHelper::getValue($post,'noRujukan',null);
            $t_rujukan_khusus["diagnosa"] = ArrayHelper::getValue($post,'temp_diagnosa',null);
            $t_rujukan_khusus["procedure"] = ArrayHelper::getValue($post,'temp_procedure',null);
            $t_rujukan_khusus["user"] = ArrayHelper::getValue($post,'user',null);
            $model->t_rujukan_khusus = $t_rujukan_khusus;
            $response = $model->createRujukanKhusus();
            if ($response['metaData']['code'] != 200 || $response['metaData']['code'] != '200') {
                $result = [
                    'status' => 422,
                    'title' => 'Create Rujukan Khusus Gagal!',
                    'text' => $response['metaData']['message'],
                    'data' => $response,
                ]; 
            } else {
                $result = [
                    'status' => $response['metaData']['code'],
                    'title' => 'Create Rujukan Khusus Berhasil!',
                    'text' => $response['metaData']['message'],
                    'data' => $response,
                ]; 
            }
            return $result;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}
