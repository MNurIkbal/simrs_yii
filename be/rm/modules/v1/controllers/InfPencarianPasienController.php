<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-02-14 16:53
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfPencarianPasien;
use app\modules\v1\models\RiwayatkunjunganR;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\PasiencetakanV;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\PasienUbahData;
use app\modules\v1\models\InfoKunjunganRsView;
use app\modules\v1\models\AksesForm;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use yii\helpers\Url;

class InfPencarianPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfPencarianPasien';
    const DEFAULT_LIMIT = 100;

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }


    public function actionGetDataPasien($pasien_id){
        $data['akses'] = [];

        try {
            $data['pasien'] = PasienV::find()->where(['pasien_id' => $pasien_id])->one();
            $data['lookup'] = Lookup::find()->where(['lookup_type' => DocoConstants::FITUR_AKSES])->all();
            $cekAkses = AksesForm::find()->where(['pasien_id' => $pasien_id])->one();
            if (!empty($cekAkses)){
                $data['akses'] = $cekAkses;
            }
            return $data;
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

    public function actionUbahAkses(){
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $request = Yii::$app->request;
            $post = $request->post();
            if (!empty($post['aksesform_id'])){
                $model = AksesForm::findOne($post['aksesform_id']);
            }else{
                $model = new AksesForm;        
            }
            $model->attributes = $post;
            
            if($model->validate()){
                if ($post) {
                    if ($model->save()) {
                        $transaction->commit();
                        $responseMessage =  ['message' => 'Data Berhasil di simpan'];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'AksesForm');
                        $responseMessage = ['data' => $errors,'status' => 422];
                    }
                    return $responseMessage;
                }
            }else{
                $result['status'] = 422;
                $result['data'] = $model->errors;
            }
            return $result;
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

    public function actionCekAksesForm($pasien_id){
        $data = [];
        try {
            $cekAkses = AksesForm::find()->where(['pasien_id' => $pasien_id])->one();
            if (!empty($cekAkses)){
                $data = $cekAkses;
            }
            return $data;
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