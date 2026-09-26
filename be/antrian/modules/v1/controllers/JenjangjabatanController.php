<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Jenjangjabatan;

class JenjangjabatanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Jenjangjabatan';

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
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'jenisjabatan_m,indexing_m');
        
        $model = new Jenjangjabatan;
        $query = $model::find()
            ->joinWith(['jenisjabatan' => function($query){
                $query->from('jenisjabatan_m');
            }])
            ->joinWith(['indexing' => function($query){
                $query->from('indexing_m');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}