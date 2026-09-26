<?php

namespace Doco\fisioterapi\actions\LaporanDropOutPasien;

use Yii;
use app\components\DocoHelpers;

class DownloadFileExcelAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Drop Out Pasien Fisioterapi.xlsx';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $response = $restFisio->get('laporan-drop-out-pasien/download-excel-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
