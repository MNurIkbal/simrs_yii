<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\Services\ExportExcelEngine;
use app\modules\v1\models\LaporanDataJurnalMaterializeView;

class LaporanDataJurnalController extends DocoActiveController {
    public $modelClass = '';
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $range_tanggal = $request->get('range_tanggal');


        $date = explode(' - ', $range_tanggal);
        $start = date('Y-m-d', strtotime($date[0]));
        $end = date('Y-m-d', strtotime($date[1]));

        $query =  LaporanDataJurnalMaterializeView::queryReport($start, $end);

        try {
            $engine = new ExportExcelEngine();
            $response = $engine->sendRequest($randString, $query);
            return ['status' => 200, 'response' => $response];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}