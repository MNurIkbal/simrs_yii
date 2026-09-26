<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoConstants;
use Doco\models\Instalasi;
use app\modules\v1\models\RekapThroughputView;

class PatientDashboardController extends \Doco\components\DocoActiveController {
    public $modelClass = '';

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

    public function actionListInstalasi()
    {
        return Instalasi::find()->select([
            'instalasi_id',
            'instalasi_nama'
        ])->where([
            'instalasi_id' => [
                DocoConstants::INST_ID_RAD,
                DocoConstants::INST_ID_LAB,
                DocoConstants::INST_ID_BEDAH,
                DocoConstants::INST_ID_RI,
                DocoConstants::INST_ID_RJ,
                DocoConstants::INST_ID_RD,
                DocoConstants::INSTALASI_GUDANG_FARMASI,
            ]
        ])->orderBy([
            'instalasi_id' => SORT_ASC
        ])->asArray()->all();
    }

    public function actionGetRekap()
    {
        return RekapThroughputView::find()->select([
            'tipe', 
            'total'
        ])->asArray()->all();
    }
}