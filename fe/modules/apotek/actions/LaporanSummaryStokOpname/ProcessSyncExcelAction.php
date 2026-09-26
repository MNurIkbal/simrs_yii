<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanSummaryStokOpname;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class ProcessSyncExcelAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $session = Yii::$app->session;
        $params = $session->getFlash($randString);
        return $this->controller->guzzleExec($this->controller->_restApotek, [
            'url' => "laporan-summary-stok-opname/sync-export-excel",
            'payload' => [
                'query' => $params
            ],
        ]);
    }
}
