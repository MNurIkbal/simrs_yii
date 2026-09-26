<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\GtLayananRadView;

class InfPasienGtController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\GtLayananRadView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
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
        $model = new GtLayananRadView();
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter(
            $model,
            DocoRestActiveFilter::filterMutation(
                $model,
                $query,
                [
                    'tgl_pendaftaran' => 'date',
                    'hasil_lab.tgl_hasilpemeriksaanlab' => 'json_array_date',
                    'hasil_lab.dokter_lab' => 'json_array_like',
                    'hasil_lab.nama_sample' => 'json_array_like',
                    'hasil_lab.nama_rujukan' => 'json_array_like',
                    'hasil_lab.nilai_rujukan' => 'json_array_like',
                    'hasil_lab.hasil' => 'json_array_like',
                ]
            )
        );
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
