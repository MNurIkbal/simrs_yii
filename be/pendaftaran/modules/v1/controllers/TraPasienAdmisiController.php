<?php

/**
 * @author Rizal
 * @description backend transaksi pasien inap
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\PasienAdmisi;
use yii\helpers\ArrayHelper;

class TraPasienAdmisiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Pendaftaran';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET", 'PUT'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        return $actions;
    }
    
    public function actionView($id) {
        // $data = Pendaftaran::findOne($id);
        $data = PasienAdmisi::find()
        ->where(['pasienadmisi_id'=>$id])
        ->asArray()
        ->one();

        return $data;
    }
}