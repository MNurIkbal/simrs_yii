<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\apotek\actions\LaporanSummaryStokOpname;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class DownloadFileExcelAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Summary Stock Opname.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->controller->_restApotek->get('laporan-summary-stok-opname/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
