<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-19 13:29:06\
 */

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Cron;
use app\modules\v1\models\KonfigSystem;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;

class CronController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Cron';

    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    public function actionIndex()
    {
        try {
            $model = new Cron;

            $query = $model::find()->select(['*']);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
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

    public function actionSync($id)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        try {
            $request = Yii::$app->request;
            $model = Cron::findOne(['cron_id'=>$id]);
            $modelKonfig = KonfigSystem::find()->one();

            if ($model && $modelKonfig) {
                $return = DocoHelpers::curl($modelKonfig->sync_url.$model->url);
                // sleep(1);

                return $response['response'] = [
                    'title' => 'Proses Selesai !',
                    'text' => $return
                ];
            } else {
                return $response['response'] = [
                    'title' => 'Proses Gagal !',
                    'text' => ''
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
}
