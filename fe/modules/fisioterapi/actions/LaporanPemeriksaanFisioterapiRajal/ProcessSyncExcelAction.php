<?php

namespace Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal;

use Yii;
use yii\web\Response;

class ProcessSyncExcelAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $restFisio = Yii::$app->docoRest->fisioterapi;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($restFisio, [
            'url' => 'laporan-pemeriksaan-fisioterapi-rajal/process-sync-excel-bgprocess',
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }
}
