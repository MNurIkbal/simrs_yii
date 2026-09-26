<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\Traits\RincianTerapiTrait;

class RincianTerapiController extends DocoActiveController
{
    use RincianTerapiTrait;

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['create'] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        return $actions;
    }

}
