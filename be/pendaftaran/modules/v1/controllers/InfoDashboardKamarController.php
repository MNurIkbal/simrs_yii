<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\DashboardKamarKosongView;
use app\modules\v1\models\KonfigSystem;

class InfoDashboardKamarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\DashboardKamarKosongView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["peserta"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

    public function actionListKamar()
    {
        $data = DashboardKamarKosongView::find()->orderBy(['kamar' => SORT_ASC])->all();

        return $data;
    }

    public function actionGetKonfigSystem()
    {
        $data = KonfigSystem::find()->one();

        return $data;
    }
}