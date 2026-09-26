<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\InfoTagihanObatView;
use Doco\components\NoCountDataProvider;

class PenjualanResepController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PenjualanResep';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-options"] = ["GET"];
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

    public function actionIndex()
    {
        $request = Yii::$app->request;

        $model = new InfoTagihanObatView;
        $query = $model::find();

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpenjualan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpenjualan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpenjualan']); // Unset Advanced Filter  date range
                $between = true;
            }
        }

        $query->andWhere(['between', 'tglpenjualan', $start, $end]);
        $query->andWhere(['status_bayar' => 349]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new NoCountDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetOptions()
    {
        $caraBayar = CaraBayar::find()->where([
            'is_active' => true
        ])->all();

        $penjamin = Penjamin::find()->where([
            'is_active' => true
        ])->all();
        return  [
            'cara_bayar' => $caraBayar,
            'penjamin' => $penjamin
        ];
    }
}