<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Kabupaten;
use yii\helpers\ArrayHelper;

class KabupatenController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Kabupaten';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["list-kabupaten"] = ["POST", "GET"];
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
        $_GET['expand'] = $request->get('expand', 'propinsi_m');

        $model = new Kabupaten;
        $query = $model::find()
            ->joinWith(['propinsi' => function($query){
                $query->from('propinsi_m');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListKabupaten($propinsi) {
        $data = Kabupaten::find()->where(['is_active' => 't', 'is_deleted' => 'f', 'propinsi_id' => $propinsi ]);
        // $items = ArrayHelper::map($data->all(), 'kabupaten_id', 'kabupaten_nama');
        return $data->asArray()->all();
    }

    public function actionListKabupatenAll($kabupaten) {
        $data = Kabupaten::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
        $data = $data->andWhere(['in', 'propinsi_id', $kabupaten]);
        $items = ArrayHelper::map($data->all(), 'kabupaten_id', 'kabupaten_nama');
        return $items;
    }
}
