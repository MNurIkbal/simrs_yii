<?php


namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LaporanStockMutasiController extends DocoController
{
    public $_title = "Laporan Summary Mutasi Stok";
    public $_module = '/gudang/laporan-stock-mutasi/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index'         => 'Doco\gudang\actions\LaporanStockMutasi\IndexAction',
            'get-list-data' => 'Doco\gudang\actions\LaporanStockMutasi\GetListDataAction',
            'process-sync-excel' => 'Doco\gudang\actions\LaporanStockMutasi\ProcessSyncExcelAction'
        ];
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/laporan-stock-mutasi.xlsx";
        $response =  $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => "lap-stock-mutasi/export-excel",
            'payload' => [
                'save_to' => $path,
                'query' => $filter
            ],
        ]);
        
        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Summary Mutasi Stok Excel';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan-stock-mutasi.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response =  $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => "lap-stock-mutasi/download-file",
            'payload' => [
                'query' => [
                    'no_request' => $filename,
                ],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}
