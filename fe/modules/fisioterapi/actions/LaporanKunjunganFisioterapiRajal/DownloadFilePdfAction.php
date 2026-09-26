<?php

namespace Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal;

use app\components\DocoConstants;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use yii\base\DynamicModel;

class DownloadFilePdfAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Kunjungan Fisio Rajal.pdf';
        $path = Yii::getAlias('@download') . '/' . $fileDownloads;
        $response = $restFisio->get('laporan-kunjungan-fisioterapi-rajal/download-pdf-bgprocess', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
