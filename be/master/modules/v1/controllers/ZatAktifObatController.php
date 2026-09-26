<?php

/**
 * @author: Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;

class ZatAktifObatController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\InfoZatAktifObatView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions() {
        $path = 'app\modules\v1\actions\ZatAktifObat';

        return [
            'filters'            => $path . '\FiltersAction',
            'get-list'           => $path . '\GetListAction',
            'detail'             => $path . '\DetailAction',
            'get-list-detail'    => $path . '\GetListDetailAction',
            'create'             => $path . '\CreateAction',
            'remove'             => $path . '\RemoveAction',
            'assign-primary'     => $path . '\AssignPrimaryAction',
        ];
    }
}
