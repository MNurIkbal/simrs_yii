<?php
/**
 * @Author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoMessages;
use app\modules\v1\models\JasaDokter;
use app\modules\v1\models\PelayananJasaDokter;

class MasterTraJasaDokterController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JasaDokter';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new JasaDokter;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        $result = [];
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            if($post) {
                $model = new JasaDokter;
                $model->attributes = $post;
                $model->jasadokter_kode = trim($model->jasadokter_kode);
                $model->jasadokter_nama = trim($model->jasadokter_nama);
                if($model->validate() && $model->save()) {
                    return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA);
                }
                else {
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
    
    public function actionUpdate()
    {
        $result = [];
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            if($post) {
                $model = JasaDokter::findOne($request->get('id'));
                if(!empty($model)) {
                    $model->attributes = $post;
                    $model->jasadokter_kode = trim($model->jasadokter_kode);
                    $model->jasadokter_nama = trim($model->jasadokter_nama);
                    if($model->validate() && $model->save()) {
                        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM_DATA);
                    }
                    else {
                        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                            'data' => $model->errors
                        ]);
                    }
                }
                else{
                    throw new \Exception('Data Tidak Di Temukan');
                }
            }
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $request = Yii::$app->request;
            $model = JasaDokter::findOne($id);
            $modelTransaksi = new PelayananJasaDokter;
            $getDataTransaksi = $modelTransaksi::find()->where(['jasadokter_id'=>$id])->count();
            if($getDataTransaksi > 0){
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Master '.$model->jasadokter_nama.' tidak dapat dihapus karena sudah digunakan dalam transaksi jasa dokter',
                            'status' => 422
                       ];
            }else{
                if ($model->delete()) {
                    return $response['response'] = [
                            'title' => 'Proses Berhasil !',
                            'text' => 'Data berhasil dihapus'
                       ];
                } else {
                    return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Data Gagal di hapus',
                            'status' => 422
                       ];
                }                
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

