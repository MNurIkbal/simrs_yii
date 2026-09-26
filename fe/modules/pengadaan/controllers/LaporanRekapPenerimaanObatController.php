<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LaporanRekapPenerimaanObatController extends DocoController {
    public $_module = '/pengadaan/laporan-rekap-penerimaan-obat/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index' => 'Doco\pengadaan\actions\LaporanRekapPenerimaanObat\IndexAction',
            'get-data' => 'Doco\pengadaan\actions\LaporanRekapPenerimaanObat\GetDataAction',
            'export-excel'  => 'Doco\pengadaan\actions\LaporanRekapPenerimaanObat\ExportExcelAction',
        ];
    }
}
