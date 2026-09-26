<?php

namespace Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal;

use Yii;
use app\components\DocoHelpers;

class DownloadFileExcelAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Pemeriksaan Fisioterapi Rawat Jalan.xlsx';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $restFisio->get('laporan-pemeriksaan-fisioterapi-rajal/download-excel-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
