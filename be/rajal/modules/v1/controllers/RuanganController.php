<?php

/**
 * @Author: afil
 * @Date:   2018-01-08 11:44:30
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-08 11:46:51
 * @Description: controller untuk kebutuhan jenis kasus penyakit - ruangan rajal
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Ruangan;

class RuanganController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\ruangan';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }
}