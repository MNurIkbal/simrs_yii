<?php

namespace Doco\rm\actions\LapKunjunganPenunjang;

use Yii;
use app\components\DocoHelpers;

class DownloadFilePdfAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restRm = Yii::$app->docoRest->rm;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Kunjungan Penunjang.pdf';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $response = $restRm->get('lap-kunjungan-penunjang/download-pdf-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
