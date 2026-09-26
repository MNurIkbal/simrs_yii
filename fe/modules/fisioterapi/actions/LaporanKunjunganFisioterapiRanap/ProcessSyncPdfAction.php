<?php

namespace Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap;

use app\components\DocoConstants;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use yii\base\DynamicModel;

class ProcessSyncPdfAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $restFisio = Yii::$app->docoRest->fisioterapi;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($restFisio, [
            'url' => 'laporan-kunjungan-fisioterapi-ranap/process-sync-pdf-bgprocess',
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }
}
