<?php

namespace Doco\fisioterapi\actions\LaporanDropOutPasien;

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
            'url' => 'laporan-drop-out-pasien/process-sync-pdf-bgprocess',
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }
}
