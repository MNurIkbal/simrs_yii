<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\DHtml;

class LaporanPurchaseOrderOutstandingController extends DocoController {
    public $_title = "Laporan Purchase Order Outstanding";
    public $_module = '/pengadaan/laporan-purchase-order-outstanding/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'export-excel'  => 'Doco\pengadaan\actions\LaporanPOOutstanding\ExportExcelAction',
            'index'         => 'Doco\pengadaan\actions\LaporanPOOutstanding\IndexAction',
            'get-data'      => 'Doco\pengadaan\actions\LaporanPOOutstanding\GetDataLaporanAction'
        ];
    }

    public function getFilter($request) {
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['type'] = DocoConstants::JENIS_OBAT;
        if(!isset($yiiRestfulParams['advanced-filter']['tgl_po_dibuat'])) {
            $yiiRestfulParams['advanced-filter']['tgl_po_dibuat'] = date('d-m-Y').' - '.date('d-m-Y');
        }
        return $yiiRestfulParams;
    }
    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/laporan_purchase_order_outstanding.xlsx";
        $response =  $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "info-purchase-order/export-excel",
            'payload' => [
                'save_to' => $path,
                'query' => $filter
            ],
        ]);
        
        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Purchase Order Outstanding';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        if (isset($request->get()['is_prcyto'])) {
            $yiiRestfulParams['advanced-filter']['is_cito'] = $request->get('is_prcyto');
        }

        if (isset($request->get()['is_admin'])) {
            $yiiRestfulParams['advanced-filter']['is_admin'] = $request->get('is_admin');
        }

        if (isset($request->get()['is_consignment'])) {
            $yiiRestfulParams['advanced-filter']['is_consigment'] = $request->get('is_consignment');
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
            'url' => "info-purchase-order/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan_purchase_order_outstanding.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response =  $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "info-purchase-order/download-file",
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
