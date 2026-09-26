<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-02-26 08:14:47
 * @Last Modified by:   afil
 * @Last Modified time: 2018-02-26 08:19:14
 * @Description: 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\LapKunjunganRawatDarurat;
use yii\helpers\ArrayHelper;

class TraPasienIgdController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET", 'PUT'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }

    public function actionView($id) {
        // $data = Pendaftaran::findOne($id);
        $data = LapKunjunganRawatDarurat::find()
        ->where(['pendaftaran_id'=>$id])
        ->asArray()
        ->one();

        return $data;
    }

    public function actionUpdate($id) 
    {
        try {
            $request = Yii::$app->request;
            $model = Pendaftaran::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendaftaranForm');
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
}