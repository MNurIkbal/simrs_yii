<?php

namespace app\modules\v1\controllers;

use app\modules\v1\models\Ruangan;
use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Instalasi;
use yii\helpers\ArrayHelper;

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
        return [
            'data' => $query->asArray()->all()
        ];
    }

    public function actionListRuangan() {
        try {
            return ArrayHelper::map(Ruangan::find()->where(['is_active'=> true, 'is_deleted' => false])->orderBy(["ruangan_nama" => SORT_ASC])->all(), 'ruangan_id', 'ruangan_nama');
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionListInstalasi() 
    {
        $data = Instalasi::find()->where(['is_active' => 't'])->orderBy('instalasi_id');
        $items = ArrayHelper::map($data->all(), 'instalasi_id', 'instalasi_nama');

        return $items;
    }
}