<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PenjualanResep;

class PenjualanResepController extends DocoActiveController
{
    public $modelClass = '';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        $action = [
            'save' => 'app\modules\v1\actions\PenjualanResep\SaveAction'
        ];
        $actions = array_merge($actions, $action);

        return $actions;
    }

    public function actionIndex()
    {
        $model = new PenjualanResep;
        $query = $model::find(true);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpenjualan'])) {
                $tglpenjualan = $_GET['advanced-filter']['tglpenjualan'];
                $start = date('Y-m-d 00:00:00', strtotime($tglpenjualan));
                $end = date('Y-m-d 23:59:59', strtotime($tglpenjualan));

                $query->andWhere(['between', 'tglpenjualan', $start, $end]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}