<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanAdjustment;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\Traits\ControllerHelperTrait;

class DownloadFileExcelAction extends Action
{
    use ControllerHelperTrait;
    public function run()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Adjustment Obat Alkes.xlsx';

        $path = Yii::getAlias("@download") . '/' . $fileDownloads;
        Yii::$app->docoRest->gudang->get('laporan-adjustment/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path, true);
    }
}
