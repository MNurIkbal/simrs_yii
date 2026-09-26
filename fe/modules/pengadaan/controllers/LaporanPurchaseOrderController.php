<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use Yii;

use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoController;
use app\components\DocoDatatableHelper;

class LaporanPurchaseOrderController extends DocoController {
    public $_title = "Laporan Purchase Order";
    public $_module = '/pengadaan/laporan-purchase-order/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index' => [
                'class' => 'app\components\actions\ExtensionAction',
                'key' => 'laporan_purchase_order_index'
            ],
            'barang' => [
                'class' => 'app\components\actions\ExtensionAction',
                'key' => 'laporan_purchase_order_index'
            ],
            'get-data' => [
                'class' => 'app\components\actions\ExtensionAction',
                'key' => 'laporan_purchase_order_getdata'
            ],
            'export-excel'  => 'Doco\pengadaan\actions\LaporanPurchaseOrder\ExportExcelAction'
        ];
    }

    public function getFilter($request) {
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if(isset($yiiRestfulParams['advanced-filter']['tgl_create_po'])) {
            $yiiRestfulParams['advanced-filter']['tanggal_po'] = $yiiRestfulParams['advanced-filter']['tgl_create_po'];
        }
        return $yiiRestfulParams;
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/laporan_purchase_order.xlsx";
        $response =  $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "lap-purchase-order/export-excel",
            'payload' => [
                'save_to' => $path,
                'query' => $filter
            ],
        ]);
        
        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Purchase Order';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        $actionId = $request->get('actionId','BARANG');
        $tipe = $actionId == 'index' ? 'OBAT' : 'BARANG';

        if (isset($request->get()['is_prcyto'])) {
            $yiiRestfulParams['advanced-filter']['is_cito'] = $request->get('is_prcyto');
        }

        if (isset($request->get()['is_admin'])) {
            $yiiRestfulParams['advanced-filter']['is_admin'] = $request->get('is_admin');
        }

        if (isset($request->get()['is_consignment'])) {
            $yiiRestfulParams['advanced-filter']['is_consigment'] = $request->get('is_consignment');
        }

        if (!isset($yiiRestfulParams['advanced-filter']['type'])) {
            $yiiRestfulParams['advanced-filter']['type'] = $tipe;
        }

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('@app/extensions/purchasing/views/_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "lap-purchase-order/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan_purchase_order.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response =  $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "lap-purchase-order/download-file",
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
