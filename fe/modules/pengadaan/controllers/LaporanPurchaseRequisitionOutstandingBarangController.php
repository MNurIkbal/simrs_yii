<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

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
use app\components\DHtml;

class LaporanPurchaseRequisitionOutstandingBarangController extends DocoController {
    public $_title = "Laporan Purchase Requisition Outstanding Non-Medis";
    public $_module = '/pengadaan/laporan-purchase-requisition-outstanding-barang/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index' => 'Doco\pengadaan\actions\LaporanPurchaseRequisitionOutstandingBarang\IndexAction',
            'get-data' => 'Doco\pengadaan\actions\LaporanPurchaseRequisitionOutstandingBarang\GetDataAction',
            'export-excel'  => 'Doco\pengadaan\actions\LaporanPurchaseRequisitionOutstandingBarang\ExportExcelAction',
        ];
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/laporan_purchase_requisition_outstanding_barang.xlsx";
        $response =  $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "lap-purchase-requisition-outstanding-barang/export-excel",
            'payload' => [
                'save_to' => $path,
                'query' => $filter
            ],
        ]);
        
        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Purchase Requisition Outstanding Non-Medis';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        if (isset($request->get()['is_prcyto'])) {
            $yiiRestfulParams['advanced-filter']['is_cyto'] = $request->get('is_prcyto');
        }

        if (isset($request->get()['is_admin'])) {
            $yiiRestfulParams['advanced-filter']['is_admin'] = $request->get('is_admin');
        }

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "lap-purchase-requisition-outstanding-barang/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan_purchase_requisition_outstanding_barang.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response =  $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "lap-purchase-requisition-outstanding-barang/download-file",
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
