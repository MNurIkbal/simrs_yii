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
use app\modules\v1\models\PelayananJasaDokter;

class TraJasaDokterController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PelayananJasaDokter';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionCreate()
    {
        $result = [];
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            if($post) {
                $model = new PelayananJasaDokter;
                $model->attributes = $post;
                $model->tgl_transaksi =  date('Y-m-d', strtotime($model->tgl_transaksi));
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

    public function actionDelete($id)
    {
        try {
            $result = (new PelayananJasaDokter)->delete($id);
            return $result;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}

