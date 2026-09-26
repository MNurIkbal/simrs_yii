<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\JenisObatAlkes;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\JenisObatAlkesView;
use app\modules\v1\cache\Cache;

class ServiceGroupController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\ServiceGroup';

    public function init() {
        parent::init();
    }

    public function verbs() {
        $verbs = parent::verbs();
        $verbs["delete"] = ["DELETE", "POST"];
        return $verbs;
    }

    public function actions() {
        return [
            'delete'        => 'app\modules\v1\actions\ServiceGroup\DeleteAction',
            'get-by-id'     => 'app\modules\v1\actions\ServiceGroup\GetByIdAction',
            'index'         => 'app\modules\v1\actions\ServiceGroup\IndexAction',
            'save'          => 'app\modules\v1\actions\ServiceGroup\SaveAction',
            'update-data'   => 'app\modules\v1\actions\ServiceGroup\UpdateAction'
        ];
    }
}