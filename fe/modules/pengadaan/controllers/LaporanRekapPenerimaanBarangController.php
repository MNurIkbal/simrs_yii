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

class LaporanRekapPenerimaanBarangController extends DocoController {
    public $_module = '/pengadaan/laporan-rekap-penerimaan-barang/';

    public function init() {
        parent::init();
    }

    public function actions()
    {
        return [
            'index' => 'Doco\pengadaan\actions\LaporanRekapPenerimaanBarang\IndexAction',
            'get-data' => 'Doco\pengadaan\actions\LaporanRekapPenerimaanBarang\GetDataAction',
            'export-excel' => 'Doco\pengadaan\actions\LaporanRekapPenerimaanBarang\ExportExcelAction',
        ];
    }


}
