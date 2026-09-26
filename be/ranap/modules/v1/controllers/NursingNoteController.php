<?php

namespace app\modules\v1\controllers;

use Doco\Traits\NursingNoteTrait;

class NursingNoteController extends \Doco\components\DocoActiveController
{
    use NursingNoteTrait;
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
