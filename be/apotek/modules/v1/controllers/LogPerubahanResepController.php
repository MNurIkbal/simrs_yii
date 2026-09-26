<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LogPerubahanResepR;

class LogPerubahanResepController extends DocoActiveController
{
    public $modelClass = '';

    public function actions() {
        return [
            'get-data'      => 'app\modules\v1\actions\LogPerubahanResep\GetDataAction',
        ];
    }
}
