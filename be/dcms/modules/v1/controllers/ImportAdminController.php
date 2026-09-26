<?php

namespace app\modules\v1\controllers;

use Yii;

class ImportAdminController extends \Doco\components\DocoActiveController
{
    public $modelClass = '';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionManajemenImport()
    {
        //
    }
}