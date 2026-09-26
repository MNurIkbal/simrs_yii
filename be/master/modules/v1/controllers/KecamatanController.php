<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Kecamatan;
use yii\helpers\ArrayHelper;

class KecamatanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Kecamatan';

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
        $_GET['expand'] = $request->get('expand', 'kabupaten_m');

        $model = new Kecamatan;
        $query = $model::find()
            ->joinWith(['kabupaten' => function($query){
                $query->from('kabupaten_m');
            }]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListKecamatan($kabupaten) {
        $data = Kecamatan::find()->where(['is_active' => 't', 'is_deleted' => 'f', 'kabupaten_id' => $kabupaten ]);

        // $items = ArrayHelper::map($data->all(), 'kecamatan_id', 'kecamatan_nama');

        return $data->asArray()->all();
    }

    public function actionListKecamatanAll($kecamatan) {
        $data = Kecamatan::find()->where(['is_active' => 't', 'is_deleted' => 'f']);
        $data = $data->andWhere(['in', 'kabupaten_id', $kecamatan]);
        $items = ArrayHelper::map($data->all(), 'kecamatan_id', 'kecamatan_nama');

        return $items;
    }
}
