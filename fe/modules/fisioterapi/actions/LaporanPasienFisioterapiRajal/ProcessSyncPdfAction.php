<?php

namespace Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal;

use Yii;
use yii\web\Response;

class ProcessSyncPdfAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $restFisio = Yii::$app->docoRest->fisioterapi;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($restFisio, [
            'url' => 'laporan-pasien-fisioterapi-rajal/process-sync-pdf-bgprocess',
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }
}
