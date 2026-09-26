<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\CaraBayar;

class CaraBayarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\CaraBayar';

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
        $_GET['expand'] = $request->get('expand', 'penjamin_m');

        $model = new CaraBayar;
        $query = $model::find()->joinWith(['penjamin' => function($query){
            $query->from('penjamin_m');
        }]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}