<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoFormulirStokOpnameView;
use app\modules\v1\models\StokOpnameDetail;

class InfFormulirStokOpnameController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoFormulirStokOpnameView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["detail"] = ["GET"];
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
        $model = new InfoFormulirStokOpnameView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglformulir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglformulir']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglformulir']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tglformulir', $start, $end]);
        // }
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDetail($parent_id)
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'stokopname_t,obatalkes_m');

        $model = new StokOpnameDetail;
        $query = $model::find()
            ->joinWith(['stokOpname' => function($query){
                $query->from('stokopname_t');
            }])
            ->joinWith(['obatAlkes' => function($query){
                $query->from('obatalkes_m');
            }]);

        $query->andWhere(['stokopnamedetail_t.stokopname_id' => $parent_id]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}