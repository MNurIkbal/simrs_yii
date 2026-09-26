<?php

namespace Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap;

use Yii;
use app\components\DocoHelpers;

class DownloadFileExcelAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Pemeriksaan Fisioterapi Rawat Inap.xlsx';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $restFisio->get('laporan-pemeriksaan-fisioterapi-ranap/download-excel-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
