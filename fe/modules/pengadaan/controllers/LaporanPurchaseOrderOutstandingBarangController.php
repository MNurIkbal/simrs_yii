<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\components\helpers\HandlingValueHelper as SetValue;


class LaporanPurchaseOrderOutstandingBarangController extends DocoController
{
    public $_title = "Laporan Purchase Order Outstanding Non-Medis";
    public $_module = '/pengadaan/laporan-purchase-order-outstanding-barang/';

    public function init()
    {
        parent::init();
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $module = $this->_module;

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = $this->getFilter($request);

        if (isset($request->get()['is_prcyto'])) {
            $yiiRestfulParams['advanced-filter']['is_cito'] = $request->get('is_prcyto');
        }

        if (isset($request->get()['is_admin'])) {
            $yiiRestfulParams['advanced-filter']['is_admin'] = $request->get('is_admin');
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        $laporanPOOutstanding = $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'info-purchase-order/laporan-po-outstanding',
            'method' => 'GET',
            'payload' => [
                'query' => $yiiRestfulParams
            ],
            'returnResponse' => true
        ]);
        
        $no = $request->get('start',1);
        foreach ($laporanPOOutstanding['data']['data'] as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $value['no_pr'] = SetValue::nullValue($value['no_pr']);
            $value['tanggal_verifikasi_pr'] = SetValue::dateTimeValue($value['tanggal_verifikasi_pr']);
            $value['no_po'] = SetValue::nullValue($value['no_po']);
            $value['tanggal_po'] = SetValue::dateTimeValue($value['tanggal_po']);
            $value['tanggal_verifikasi_po'] = SetValue::dateTimeValue($value['tanggal_verifikasi_po']);
            $value['supplier_kode'] = SetValue::nullValue($value['supplier_kode']);
            $value['supplier_nama'] = SetValue::nullValue($value['supplier_nama']);
            $value['manufacturer'] = SetValue::nullValue($value['manufacturer']);
            $value['item_code'] = SetValue::nullValue($value['item_code']);
            $value['item_name'] = SetValue::nullValue($value['item_name']);
            $value['qty_po'] = SetValue::nullValue($value['qty_po']);
            $value['uom'] = SetValue::nullValue($value['uom']);
            $value['from_uom'] = SetValue::nullValue($value['from_uom']);
            $value['factor'] = SetValue::nullValue($value['factor']);
            $value['to_uom'] = SetValue::nullValue($value['to_uom']);
            $value['price'] = DocoHelpers::formatNumber($value['price']);
            $value['deduction_percent'] = DocoHelpers::formatNumber($value['deduction_percent']);
            $value['addition_percent'] = DocoHelpers::formatNumber($value['addition_percent']);
            $value['gross_amount'] = DocoHelpers::formatNumber($value['gross_amount']);
            $value['nett_amount'] = DocoHelpers::formatNumber($value['nett_amount']);
            $value['remarks'] = SetValue::nullValue($value['remarks']);
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $laporanPOOutstanding['data']['_meta']['totalCount'];
        $result['recordsFiltered'] = $laporanPOOutstanding['data']['_meta']['totalCount'];

        return $result;
    }

    public function getFilter($request) {
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['type'] = DocoConstants::JENIS_BARANG;
        if(!isset($yiiRestfulParams['advanced-filter']['tanggal_po'])) {
            $yiiRestfulParams['advanced-filter']['tanggal_po'] = date('d-m-Y').' - '.date('d-m-Y');
        }
        return $yiiRestfulParams;
    }

    public function setDocName($params) {
        if(isset($params['tanggal_po'])) {
            $exp = explode(' - ', $params['tanggal_po']);
            $tgl_awal = $exp[0];
            $tgl_akhir = $exp[1];
            $tanggal_po = "_".date('dMY', strtotime($tgl_awal))." - ".date('dMY', strtotime($tgl_akhir));
        } else {
            $tanggal_po = "_".date('dMY');
        }

        return "/laporan_purchase_order_outstanding_barang".$tanggal_po.".xlsx";
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $path = Yii::getAlias("@download") . "/laporan_purchase_order_outstanding_barang.xlsx";
        $response =  $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "info-purchase-order-barang/export-excel",
            'payload' => [
                'save_to' => $path,
                'query' => $filter
            ],
        ]);
        
        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Purchase Order Outstanding Non-Medis';
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

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "info-purchase-order-barang/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'laporan_purchase_order_outstanding_barang.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response =  $this->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => "info-purchase-order-barang/download-file",
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
