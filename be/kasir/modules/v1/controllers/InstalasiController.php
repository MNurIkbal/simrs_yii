<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Instalasi;

class InstalasiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Instalasi';

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
        $_GET['expand'] = $request->get('expand', 'profilrumahsakit_m, ruangan_m');

        $model = new Instalasi;
        $query = $model::find()
            ->joinWith(['profilRumahSakit' => function($query){
                $query->from('profilrumahsakit_m');
            }])
            ->joinWith(['ruangan' => function($query){
                $query->from('ruangan_m');
            }]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
    }
}
