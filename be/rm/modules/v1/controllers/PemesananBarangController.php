<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PesanBarang;

class PemesananBarangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PesanBarang';

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
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pesanbarangdetail_t,barang_m');
        
        $model = new PesanBarang;
        $query = $model::find()
            ->joinWith(['pesanbarangdetail'])
            ->joinWith(['barang']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pesanbarangdetail_t,barang_m');
        
        $model = new PesanBarang;
        $query = $model::find()
            ->joinWith(['pesanbarangdetail'])
            ->joinWith(['barang'])->one();
        return $query;
    }
}