<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\ObatAlkes;

class ObatAlkesController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ObatAlkes';

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
        $model = new ObatAlkes;
        $query = $model::find(true);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetObatAlkes()
    {
        
        $model = new ObatAlkes;
        $query = $model::find()->joinWith(['supplier']);

        if(isset($_GET['advanced-filter']['obatalkes_nama'])) {
            $obatalkes_nama = $_GET['advanced-filter']['obatalkes_nama'];
            $query->andWhere(['ILIKE', 'obatalkes_nama', $obatalkes_nama]);
        }
        return $query->asArray()->all();
    }
}