<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PembayaranPelayanan;

class PembayaranPelayananController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PembayaranPelayanan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["view"] = ["GET"];
        $verbs["create"] = ["POST"];
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
        $_GET['expand'] = $request->get('expand', 'pasien_m,pendaftaran_t,tandabuktibayar_t');

        $model = new PembayaranPelayanan;
        $query = $model::find()
            ->joinWith(['pasien' => function($query){
                $query->from('pasien_m');
            }])
            ->joinWith(['pendaftaran' => function($query){
                $query->from('pendaftaran_t');
            }])
            ->joinWith(['tandaBuktiBayar' => function($query){
                $query->from('tandabuktibayar_t');
            }]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}