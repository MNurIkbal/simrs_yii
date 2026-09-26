<?php

namespace Doco\rm\actions\LapKunjunganPenunjang;

use Yii;
use app\components\DocoHelpers;

class DownloadFileExcelAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restRm = Yii::$app->docoRest->rm;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Kunjungan Penunjang.xlsx';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $response = $restRm->get('lap-kunjungan-penunjang/download-excel-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
