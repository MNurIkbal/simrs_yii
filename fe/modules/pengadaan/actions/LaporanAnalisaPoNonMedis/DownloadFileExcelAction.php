<?php

/**
 * @author : iqbal.rukmana@docotel.com
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPoNonMedis;

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
        $fileDownloads = 'Laporan PO Analisa Non Medis.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->controller->_restPengadaan->get('laporan-analisa-po-non-medis/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
