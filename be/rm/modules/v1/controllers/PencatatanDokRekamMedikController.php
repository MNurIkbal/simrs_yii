<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DokRekamMedis;

class PencatatanDokRekamMedikController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\DokRekamMedis';

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

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pasien_m');
        
        $model = new DokRekamMedis;
        $query = $model::find()
            ->where(['pasien_m.is_deleted' => 'f'])
            ->andWhere(['dokrekammedis_m.is_deleted' => 'f'])
            ->joinWith(['pasien' => function($query){
                $query->from('pasien_m');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetList($id)
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pasien_m');
        $query = DokRekamMedis::find()
                ->where(['pasien_id' => $id,'is_deleted'=>false])
                ->one();
        return $query;
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pasien_m');
        
        $model = new DokRekamMedis;
        $query = DokRekamMedis::findOne($id);
        return $query;
    }

    public function actionSimpanPembaharuan($id)
    {
        try {
            $request = Yii::$app->request;
            // $model = DokRekamMedis::findOne($id);
            $model = DokRekamMedis::find()
                ->where(['pasien_id' => $id])
                ->one();
            if ($request->post()) {
                $model->attributes = $request->post();
                // $mPasien = Pasien::findOne($model->pasien_id);
                // $model->warnadokrm_id = $this->setWarnaDokumen($mPasien->no_rekam_medik);
                if ($model->update()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'DokrekammedisForm');
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

    public function actionListPasien()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pasien_m');
        
        $model = new DokRekamMedis;
        $query = $model::find()
            ->where(['dokrekammedis_m.is_deleted' => false])
            ->joinWith(['pasien' => function($query){
                $query->from('pasien_m');
            }]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}