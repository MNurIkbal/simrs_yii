<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PesanBarang;

class PesanBarangController extends DocoActiveController
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
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new PesanBarang;
        $query = $model::find(true);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pesanbarang'])) {
                $tanggal_pesan = $_GET['advanced-filter']['tgl_pesanbarang'];
                $start = date('Y-m-d 00:00:00', strtotime($tanggal_pesan));
                $end = date('Y-m-d 23:59:59', strtotime($tanggal_pesan));

                $query->andWhere(['between', 'tgl_pesanbarang', $start, $end]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}