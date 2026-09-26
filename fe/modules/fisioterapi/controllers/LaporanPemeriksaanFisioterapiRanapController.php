<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;

class LaporanPemeriksaanFisioterapiRanapController extends DocoController
{
    protected $_module = '/fisioterapi/laporan-pemeriksaan-fisioterapi-ranap/';
    protected $restFisioterapi;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->restFisioterapi = Yii::$app->docoRest->fisioterapi;
    }

    public function actions()
    {
        $actions = parent::actions();
        $newActions = [
            'index' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap\IndexAction',
            'get-data' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap\GetDataAction',
            'show-popup-pdf' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap\ShowPopupPdfAction',
            'process-sync-pdf' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap\ProcessSyncPdfAction',
            'download-file-pdf' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap\DownloadFilePdfAction',
            'show-popup-excel' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap\ShowPopupExcelAction',
            'process-sync-excel' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap\ProcessSyncExcelAction',
            'download-file-excel' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRanap\DownloadFileExcelAction'
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

}
