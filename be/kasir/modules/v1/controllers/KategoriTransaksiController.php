<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\KategoriTransaksi;
use Doco\components\DocoHelpers;

class KategoriTransaksiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KategoriTransaksi';

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
        return $actions;
    }

    public function actionIndex()
    {
        $model = new KategoriTransaksi;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new KategoriTransaksi;
            $post = $request->post();
            $model->attributes = $post;
            if ($model->validate() && $model->save()) {
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil',
                    'text' => 'Data Berhasil Tersimpan'
                ];
            }else{
                $errors = DocoHelpers::parseError($model->errors,'KategoriTransaksiForm');
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
            $model = KategoriTransaksi::findOne($id);
            if($request->post() && !empty($model) ){
                $post = $request->post();
                $model->attributes = $post;
                if($model->validate() && $model->save()){
                    return ['message' => 'Data Berhasil di simpan'];
                }else{
                    $errors = DocoHelpers::parseError($model->errors,'KategoriTransaksiForm');
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
}