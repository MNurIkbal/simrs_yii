<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Yii;

class LaporanPenerimaanObatAlkesController extends DocoController {
    public $_title = "Laporan Penerimaan Obat Alkes";
    public $_module = '/pengadaan/laporan-penerimaan-obat-alkes/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index' => 'Doco\pengadaan\actions\LaporanPenerimaanObatAlkes\IndexAction',
            'get-data' => 'Doco\pengadaan\actions\LaporanPenerimaanObatAlkes\GetDataAction',
            'export-excel'  => 'Doco\pengadaan\actions\LaporanPenerimaanObatAlkes\ExportExcelAction'
        ];
    }

    public function actionShowPopupExcel()
    {
        $request = Yii::$app->request;
        $title = 'Laporan Penerimaan Obat Alkes';
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam($request->get());
        $yiiRestfulParams['randString'] = $randString;
        if (!isset($yiiRestfulParams['advanced-filter']) || empty($yiiRestfulParams['advanced-filter'])) {
            $yiiRestfulParams['advanced-filter']['tgl_penerimaan'] = date('d-M-Y'). ' - '.date('d-M-Y'); // solusi sementara jika first initiate datatable, default filter tidak terapply dan
        } 
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);

        return $this->renderAjax('_modal_excel_bg', [
            'title' => $title,
            'randString' => $randString
        ]);
    }

    public function actionProcessSyncExcel($randString)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "lap-penerimaan-obat-alkes/sync-export-excel",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan-penerimaan-obat-alkes.xlsx';
        $path = Yii::getAlias("@download") . '/' . $fileDownloads;
        $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'lap-penerimaan-obat-alkes/download-file',
            'payload' => [
                'save_to' => $path,
                'query' => [
                    'filename' => $filename,
                ]
            ]
        ]);
        return DocoHelpers::downloadFile($path, true);
    }
}
