<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Yii;

class LaporanAnalisaPurchaseOrderController extends DocoController {
    public $_title = "Laporan Analisa Purchase Order";
    public $_module = '/pengadaan/laporan-analisa-purchase-order/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'export-excel'  => 'Doco\pengadaan\actions\LaporanAnalisaPurchaseOrder\ExportExcelAction',
            'index'         => 'Doco\pengadaan\actions\LaporanAnalisaPurchaseOrder\IndexAction',
            'get-data'      => 'Doco\pengadaan\actions\LaporanAnalisaPurchaseOrder\GetDataLaporanAction'
        ];
    }

    public function getFilter($request) {
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if(!isset($yiiRestfulParams['advanced-filter']['tgl_po'])) {
            $yiiRestfulParams['advanced-filter']['tgl_po'] = date('d-m-Y').' - '.date('d-m-Y');
        }
        return $yiiRestfulParams;
    }

    public function actionShowPopupExcel()
    {
        $title = 'Export Excel Laporan Analisa PO Medis';
        $url = '/pengadaan/laporan-analisa-purchase-order';
        $fileType = 'excel';
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();
        $randString = DocoHelpers::generateRandomString();
        $request = Yii::$app->request;
        if (isset($request->get()['is_prcyto'])) {
            $yiiRestfulParams['advanced-filter']['po_cito'] = $request->get('is_prcyto');
        }
        if (isset($request->get()['is_admin'])) {
            $yiiRestfulParams['advanced-filter']['po_admin'] = $request->get('is_admin');
        }
        if (isset($request->get()['is_consignment'])) {
            $yiiRestfulParams['advanced-filter']['po_consigment'] = $request->get('is_consignment');
        }
        $yiiRestfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_bg-process-popup', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "info-purchase-order/sync-export-excel-analisa-po",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $fileDownloads = $request->get('filename', null);
        $filename = 'laporan_analisa_purchase_order.xlsx';
        if (!empty($fileDownloads)) {
            $fd = explode("/", $fileDownloads);
            $filename = $fd[1];
        }
        $path = Yii::getAlias("@download") . '/' . $filename;
        $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'info-purchase-order/download-file-excel',
            'payload' => [
                'save_to' => $path,
                'query' => [
                    'no_request' => $fileDownloads,
                ]
            ]
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
