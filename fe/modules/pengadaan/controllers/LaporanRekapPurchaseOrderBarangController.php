<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LaporanRekapPurchaseOrderBarangController extends DocoController {
    public $_module = '/pengadaan/laporan-rekap-purchase-order-barang/';

    public function init() {
        parent::init();
    }

    public function actions()
    {
        return [
            'index' => 'Doco\pengadaan\actions\LaporanRekapPurchaseOrderBarang\IndexAction',
            'get-data' => 'Doco\pengadaan\actions\LaporanRekapPurchaseOrderBarang\GetDataAction',
            'export-excel' => 'Doco\pengadaan\actions\LaporanRekapPurchaseOrderBarang\ExportExcelAction',
        ];
    }


}