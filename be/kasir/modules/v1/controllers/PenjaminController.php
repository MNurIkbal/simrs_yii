<?php

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
        $_GET['expand'] = $request->get('expand', 'carabayar_m');
        
        $model = new Penjamin;
        $query = $model::find()
            ->joinWith(['caraBayar' => function($query){
                $query->from('carabayar_m');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return [
            'data' => $query->asArray()->all()
        ];
    }
}