<?php

namespace Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap;

use Yii;
use app\components\DocoHelpers;

class DownloadFilePdfAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Pemeriksaan Fisioterapi Rawat Inap.pdf';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $response = $restFisio->get('laporan-pemeriksaan-fisioterapi-ranap/download-pdf-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
