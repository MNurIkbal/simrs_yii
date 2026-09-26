<?php

/**
 * @author : iqbal.rukmana@docotel.com
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPoNonMedis;

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
        return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "laporan-analisa-po-non-medis/sync-export-excel",
            'payload' => [
                'query' => $params
            ],
        ]);
    }
}
