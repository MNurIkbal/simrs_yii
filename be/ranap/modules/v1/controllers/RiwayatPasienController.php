<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use Doco\Traits\HistoryPatientTrait;
use Doco\Traits\HistoryFisioTrait;
use Doco\Traits\TerraMedikTrait;

// model
use app\modules\v1\models\InfoRiwayatPasienView;

class RiwayatPasienController extends DocoActiveController
{
    use HistoryPatientTrait;
    use HistoryFisioTrait;
    use TerraMedikTrait;
    
    public $modelClass = 'app\modules\v1\models\InfoRiwayatPasienView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }
}