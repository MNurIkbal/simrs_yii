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
use GuzzleHttp\Exception\RequestException;

class LaporanPurchaseRequisitionController extends DocoController {
    protected $allowAction = ['*']; // remove when done
    public $_title = "Laporan Purchase Requisition";
    public $_module = '/pengadaan/laporan-purchase-requisition/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'export-excel'  => 'Doco\pengadaan\actions\LaporanPurchaseRequisition\ExportExcelAction',
            'index'         => 'Doco\pengadaan\actions\LaporanPurchaseRequisition\IndexAction',
            'get-data'      => 'Doco\pengadaan\actions\LaporanPurchaseRequisition\GetDataLaporanAction'
        ];
    }

    public function getFilter($request) {
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if(!isset($yiiRestfulParams['advanced-filter']['tanggal_pr'])) {
            $yiiRestfulParams['advanced-filter']['tanggal_pr'] = date('d-m-Y').' - '.date('d-m-Y');
        }
        return $yiiRestfulParams;
    }
}
