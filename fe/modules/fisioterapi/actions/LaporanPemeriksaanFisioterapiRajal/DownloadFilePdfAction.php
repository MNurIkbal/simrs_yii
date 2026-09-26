<?php

namespace Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal;

use Yii;
use app\components\DocoHelpers;

class DownloadFilePdfAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Pemeriksaan Fisioterapi Rawat Jalan.pdf';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $restFisio->get('laporan-pemeriksaan-fisioterapi-rajal/download-pdf-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
