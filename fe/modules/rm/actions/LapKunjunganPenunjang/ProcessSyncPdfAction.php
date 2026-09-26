<?php

namespace Doco\rm\actions\LapKunjunganPenunjang;

use Yii;
use yii\web\Response;

class ProcessSyncPdfAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $restRm = Yii::$app->docoRest->rm;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($restRm, [
            'url' => 'lap-kunjungan-penunjang/process-sync-pdf-bgprocess',
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }
}
