<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use app\components\DocoController;

class LaporanPenerimaanBarangController extends DocoController {
    public $_module = '/pengadaan/laporan-penerimaan-barang/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index' => 'Doco\pengadaan\actions\LaporanPenerimaanBarang\IndexAction',
            'get-data' => 'Doco\pengadaan\actions\LaporanPenerimaanBarang\GetDataAction',
            'export-excel'  => 'Doco\pengadaan\actions\LaporanPenerimaanBarang\ExportExcelAction'
        ];
    }
}
