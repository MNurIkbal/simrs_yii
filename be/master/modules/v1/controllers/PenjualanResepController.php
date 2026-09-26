<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PenjualanResep;

class PenjualanResepController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PenjualanResep';

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
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'carabayar_m,penjamin_m,pasien_m,pendaftaran_t');

        $model = new PenjualanResep;
        $query = $model::find()
            ->joinWith(['caraBayar' => function($query){
                $query->from('carabayar_m');
            }])
            ->joinWith(['penjamin' => function($query){
                $query->from('penjamin_m');
            }])
            ->joinWith(['pasien' => function($query){
                $query->from('pasien_m');
            }])
            ->joinWith(['pendaftaran' => function($query){
                $query->from('pendaftaran_t');
            }]);
            
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

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
        // if($between) {
            $query->andWhere(['between', 'tglpenjualan', $start, $end]);
        // }
        /**
         * End Special Condition date range
        **/
            
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}