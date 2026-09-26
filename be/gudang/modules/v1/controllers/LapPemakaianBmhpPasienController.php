<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;

class LapPemakaianBmhpPasienController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\LaporanPemakaianBmhpPasienView';

    public function actions() {
        return [
            'index' => 'app\modules\v1\actions\LapPemakaianBmhpPasien\IndexAction',
            'export-excel' => 'app\modules\v1\actions\LapPemakaianBmhpPasien\ExportExcelAction',
        ];
    }
}

