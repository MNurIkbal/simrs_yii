<?php

namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\widgets\DocoGlobalModalWidget;

class LaporanStockInventoryController extends DocoController
{
    public $_title = "Laporan Stock Inventory";
    public $_module = '/gudang/laporan-stock-inventory/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index'         => 'Doco\gudang\actions\LaporanStockInventory\IndexAction',
            'get-list-data' => 'Doco\gudang\actions\LaporanStockInventory\GetListDataAction'
        ];
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        try {
            $path = Yii::getAlias("@download") . "/laporan-stock-inventory.xlsx";
            $response = Yii::$app->docoRest->gudang->get('lap-stock-inventory/export-excel', [
                'save_to' => $path,
                'query' => $filter
            ]);  
            
            return DocoHelpers::downloadFile($path,true);
       } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
       } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
       }
    }

    public function actionShowPopupExcel()
    {
        $request      = Yii::$app->request;
        $randString   = DocoHelpers::generateRandomString();
        $request      = Yii::$app->request;
        $type         = $request->get('type', 'excel');
        $title        = $this->_title;
        $url          = '/gudang/laporan-stock-inventory/';
        $url_sync     = 'process-sync-excel';
        $url_download = 'download-file-excel';
        $randString   = DocoHelpers::generateRandomString();

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        $yiiRestfulParams = array_merge($yiiRestfulParams, [
            'url'          => $url,
            'url_sync'     => $url_sync,
            'url_download' => $url_download,
            'randString'   => $randString,
        ]);

        $advancedFilterKeys = [
            'tanggal_invoice' => 'tanggal_invoice', 
            'ruangan_nama'    => 'ruangan_nama', 
            'jenis_obat'      => 'jenis_obat'
        ];

        foreach ($advancedFilterKeys as $reqKey => $paramKey) {
            if ($request->get($reqKey) !== null) {
                $yiiRestfulParams['advanced-filter'][$paramKey] = $request->get($reqKey);
            }
        }

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);

        return DocoGlobalModalWidget::widget([
            'type'          => $type,
            'title'         => $title,
            'randString'    => $randString,
            'url'           => $url,
            'url_sync'      => $url_sync,
            'url_download'  => $url_download,
            'params'        => $yiiRestfulParams,
        ]);
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $request    = Yii::$app->request;
        $randString = $request->get('randString');

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => "lap-stock-inventory/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan-stock-inventory.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = Yii::$app->docoRest->gudang->get('lap-stock-inventory/download-file-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }
}