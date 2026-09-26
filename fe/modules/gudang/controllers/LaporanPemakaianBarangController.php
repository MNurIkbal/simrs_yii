<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-22 10:38:16
 * @Last Modified by:   Asri Nurul
 * @Last Modified time: 2022-06-03 17:22:34
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LaporanPemakaianBarangController extends DocoController
{
    public $_title = "Laporan Pemakaian barang ";
    public $_module = '/gudang/laporan-pemakaian-barang/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index'         => 'Doco\gudang\actions\LaporanPemakaianBarang\IndexAction',
            'get-list-data' => 'Doco\gudang\actions\LaporanPemakaianBarang\GetListDataAction',
            'process-sync-excel' => 'Doco\gudang\actions\LaporanPemakaianBarang\ProcessSyncExcelAction'
        ];
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/laporan-pemakaian-barang.xlsx";
        $response =  $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => "lap-pemakaian-barang/export-excel",
            'payload' => [
                'save_to' => $path,
                'query' => $filter
            ],
        ]);
        
        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Pemakaian Barang Excel';
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
        $fileDownloads = 'laporan-pemakaian-barang.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response =  $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => "lap-pemakaian-barang/download-file",
            'payload' => [
                'query' => [
                    'no_request' => $filename,
                ],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if(!isset($yiiRestfulParams['advanced-filter']['ruangan_id'])){
            $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }
        $path = Yii::getAlias("@download") . "/laporan-pemakaian-barang.pdf";
        $query = [
            'instalasi_id' => Yii::$app->docoVars->workspace("instalasi_id"),
        ];
        $query = array_merge($query,$yiiRestfulParams);

        try {
            $response = $this->_restGudang->get('lap-pemakaian-barang/export-pdf',[
                'query' => $query,
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
