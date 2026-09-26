<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Propinsi;
use yii\helpers\ArrayHelper;

class PropinsiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Propinsi';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["list-propinsi"] = ["POST", "GET"];
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
        $model = new Propinsi;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListPropinsi() {
        $data = Propinsi::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
        $items = ArrayHelper::map($data->all(), 'propinsi_id', 'propinsi_nama');

        return $items;
    }
}