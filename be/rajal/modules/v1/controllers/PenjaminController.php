<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 11:43:03
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-15 11:50:46
 * @Description: 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Penjamin;

class PenjaminController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Penjamin';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["ajax"] = ["GET"];


        return $verbs;
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

    /**
    *
    * @see Fungsi get list data penjamin untuk ajax request
    * @return array
    *
    */
    public function actionAjax()
    {
        try {
            $request = Yii::$app->request;
            $find = $this->getPenjamin();

            $data_penjamin = $find->asArray()->all();

            return [
                'data-penjamin' => $data_penjamin
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

    /**
    *
    * @see Fungsi get data penjamin
    * @return array, activeQueryRecords
    *
    */
    private function getPenjamin()
    {
        $penjamin = Penjamin::find()->select([
                "penjamin_id",
                "penjamin_nama",
            ]);
        
        return $penjamin;

    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'carabayar_m');
        
        $model = new Penjamin;
        $query = $model::find()
            ->joinWith(['caraBayar' => function($query){
                $query->from('carabayar_m');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}