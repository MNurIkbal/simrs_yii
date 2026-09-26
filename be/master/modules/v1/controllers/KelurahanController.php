<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Kelurahan;
use yii\helpers\ArrayHelper;


class KelurahanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Kelurahan';

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
        $_GET['expand'] = $request->get('expand', 'kecamatan_m');

        $model = new Kelurahan;
        $query = $model::find()
            ->joinWith(['kecamatan' => function($query){
                $query->from('kecamatan_m');
            }]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListKelurahan($kecamatan) {
        $data = Kelurahan::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
        $data = $data->andWhere(['kecamatan_id'=>$kecamatan]);

        // $items = ArrayHelper::map($data->all(), 'kelurahan_id', 'kelurahan_nama');

        return $data->asArray()->all();
    }

    public function actionListKelurahanAll($kelurahan) {
        $data = Kelurahan::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
        $data = $data->andWhere(['in', 'kecamatan_id', $kelurahan]);
        $items = ArrayHelper::map($data->all(), 'kelurahan_id', 'kelurahan_nama');

        return $items;
    }
}
