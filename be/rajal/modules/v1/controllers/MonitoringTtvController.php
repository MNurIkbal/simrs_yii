<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\Traits\MonitoringTtvTrait;

class MonitoringTtvController extends DocoActiveController
{
    use MonitoringTtvTrait;
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["ajax"] = ["GET"];
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
}
