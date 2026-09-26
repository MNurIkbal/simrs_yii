<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\apotek\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LaporanSummaryStokOpnameController extends DocoController {
    protected $allowAction = ['*']; // remove when done
    public $_title = "Laporan Summary SO";
    public $_module = '/apotek/laporan-summary-stok-opname/';
    public $_restApotek;
    public $_restMaster;

    public function init() {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions() {
        $path = 'Doco\apotek\actions\LaporanSummaryStokOpname';
        return [
            'index'                 => $path . '\IndexAction',
            'get-data'              => $path . '\GetDataAction',
            'export-excel'          => $path . '\ExportExcelAction',
            'show-popup-excel'      => $path . '\ShowPopupExcelAction',
            'process-sync-excel'    => $path . '\ProcessSyncExcelAction',
            'download-file-excel'   => $path . '\DownloadFileExcelAction'
        ];
    }
}
