<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-31 13:25
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\TraRencanaKontrol;
use Doco\components\DocoHelpers;

class TraRencanaKontrolController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TraRencanaKontrol';

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
        $model = new TraRencanaKontrol;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);

        if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $query->andWhere(['between', 'tgl_jadwal', $tgl_awal, $tgl_akhir]);
        }

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}

