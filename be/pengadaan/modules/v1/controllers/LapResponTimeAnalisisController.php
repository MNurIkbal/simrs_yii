<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
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
use Doco\components\DocoPrint;

class LapResponTimeAnalisisController extends DocoActiveController {
    public $modelClass = '';

    public function verbs() {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions() {
        return [
            'export-excel' => 'app\modules\v1\actions\LapResponTimeAnalisis\ExportExcelAction'
        ];
    }
}
