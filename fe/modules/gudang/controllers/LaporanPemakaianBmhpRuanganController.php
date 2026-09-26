<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class LaporanPemakaianBmhpRuanganController extends DocoController {
    public $_title = "Laporan Pemakaian BMHP Ruangan";
    public $_module = '/gudang/laporan-pemakaian-bmhp-ruangan';

    public function actions() {
        return [
            'index'         => 'Doco\gudang\actions\LaporanPemakaianBmhpRuangan\IndexAction',
            'get-data'      => 'Doco\gudang\actions\LaporanPemakaianBmhpRuangan\GetDataAction',
            'export-excel'  => 'Doco\gudang\actions\LaporanPemakaianBmhpRuangan\ExportExcelAction'
        ];
    }

    public function actionShowPopupExcel()
    {
        $title = 'Cetak Laporan Pemakaian BMHP Ruangan';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');

        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => "lap-pemakaian-bmhp-ruangan/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
        return $response;
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Laporan Pemakaian Bmhp Ruangan.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->guzzleExec(Yii::$app->docoRest->gudang,[
            'url' => 'lap-pemakaian-bmhp-ruangan/download-file',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);
        return DocoHelpers::downloadFile($path,true);
    }
}
