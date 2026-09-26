<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoMutasiBarangView;

class InfMutasiBarangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoMutasiBarangView';

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
        // $request = Yii::$app->request;
        // $_GET['expand'] = $request->get('expand', 'ruangan_m,instalasi_m');
        $model = new InfoMutasiBarangView;
        $query = $model::find(true);
            // ->joinWith(['ruangan' => function($query){
                // $query->from('ruangan_m');
            // }])
            // ->joinWith(['ruangan.instalasi' => function($query){
                // $query->from('instalasi_m');
            // }]);
        // var_dump($query->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql);
        // exit();

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_mutasibarang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_mutasibarang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_mutasibarang']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tgl_mutasibarang', $start, $end]);
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