<?php

namespace Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap;

use Yii;
use app\components\DocoHelpers;

class DownloadFilePdfAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Pasien Fisioterapi Ranap.pdf';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $response = $restFisio->get('laporan-pasien-fisioterapi-ranap/download-pdf-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
