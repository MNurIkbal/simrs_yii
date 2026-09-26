<?php   

namespace Doco\gudang\controllers;

use app\components\DocoController;

class LaporanAdjustmentBarangController extends DocoController {

    protected $allowAction = ['*'];
    public $_title = "Laporan Adjustment Barang";
    public $_module = '/gudang/laporan-adjustment-barang/';
    public $_namespace = 'Doco\gudang\actions\LaporanAdjustmentBarang';
    public $_endpoint = 'laporan-adjustment-barang/';

    public function init()
    {
        parent::init();
    }

    public function actions()
    {
        return [
            'index' => $this->_namespace . '\IndexAction',
            'export-excel'  => $this->_namespace . '\ExportExcelAction',
            'get-data' => $this->_namespace . '\GetDataAction',
            'download-excel' => $this->_namespace . '\DownloadExcelAction'
        ];
    }
    
}