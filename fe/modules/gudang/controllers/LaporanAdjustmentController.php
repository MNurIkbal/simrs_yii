<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\gudang\controllers;

use app\components\DocoController;

class LaporanAdjustmentController extends DocoController
{
    public $_title = "Laporan Adjustment ";
    public $_module = 'gudang/laporan-adjustment/';
    public $_namespace = 'Doco\gudang\actions\LaporanAdjustment';
    public $_endpoint = 'laporan-adjustment/';

    public function init()
    {
        parent::init();
    }

    public function actions()
    {
        return [
            'obat-alkes' => $this->_namespace . '\IndexObatAlkesAction',
            'get-data' => $this->_namespace . '\GetDataAction',
            'show-popup-excel' => $this->_namespace . '\ShowPopupExcelAction',
            'process-sync-excel' => $this->_namespace . '\ProcessSyncExcelAction',
            'download-file-excel' => $this->_namespace . '\DownloadFileExcelAction',
        ];
    }
}
