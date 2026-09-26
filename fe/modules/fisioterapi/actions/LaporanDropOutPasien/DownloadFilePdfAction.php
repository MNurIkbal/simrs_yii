<?php

namespace Doco\fisioterapi\actions\LaporanDropOutPasien;

use Yii;
use app\components\DocoHelpers;

class DownloadFilePdfAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Drop Out Pasien Fisioterapi.pdf';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $restFisio->get('laporan-drop-out-pasien/download-pdf-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
