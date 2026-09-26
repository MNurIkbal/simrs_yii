<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\JenisNonTunai;
use Doco\components\DocoHelpers;

class JenisPembayaranNonTunaiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JenisNonTunai';

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
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new JenisNonTunai;
        $query = $model::find()->joinWith('bank')
            ->select(['jenisnontunai_m.*', 'bank_m.nama_bank']);
        
        if($request->get('advanced-filter')) {
            $advancedFilter = $request->get('advanced-filter');
            if(isset($advancedFilter['nama_bank'])) {
                $bank_id = $advancedFilter['nama_bank'];
                $query->andWhere(['jenisnontunai_m.bank_id' => $bank_id]);
                unset($_GET['advanced-filter']['nama_bank']);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new JenisNonTunai;
            $post = $request->post();
            $model->attributes = $post;
            if ($model->validate() && $model->save()) {
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil',
                    'text' => 'Data Berhasil Tersimpan'
                ];
            }else{
                $errors = DocoHelpers::parseError($model->errors,'JenisNonTunaiForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }            
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = JenisNonTunai::findOne($id);
            if($request->post() && !empty($model) ){
                $post = $request->post();
                $model->attributes = $post;
                if($model->validate() && $model->save()){
                    return ['message' => 'Data Berhasil di simpan'];
                }else{
                    $errors = DocoHelpers::parseError($model->errors,'JenisNonTunaiForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }else{
                $result = [
                    'status' => 422,
                    'title' => 'Proses Gagal!',
                    'text' => 'Data Tidak Di Temukan'
                ];                        
                return $result;
            }          
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
           \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id)
    {
        try {
            if($id) {
                $model = JenisNonTunai::find()
                    ->joinWith('bank')
                    ->select(['jenisnontunai_m.*', 'bank_m.nama_bank'])
                    ->where(['jenisnontunai_id' => $id])
                    ->asArray()->one();

                return $model;
            }
        } catch (Exception $e) {
            return [];
        }
    }
}