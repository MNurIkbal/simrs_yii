<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Doco\components\DocoActiveController;

class LapPenerimaanBarangController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-data"] = ["GET"];
        $verbs["export-excel"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        return [
            'get-data' => 'app\modules\v1\actions\LapPenerimaanBarang\GetDataAction',
            'export-excel' => 'app\modules\v1\actions\LapPenerimaanBarang\ExportExcelAction'
        ];
    }
}
