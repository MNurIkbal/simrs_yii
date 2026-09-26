<?php

/**
 * @author Andri Amirul (amdri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;

class LaporanPemeriksaanFisioterapiRajalController extends DocoController
{
    protected $_module = '/fisioterapi/laporan-pemeriksaan-fisioterapi-rajal/';
    protected $_restFisioterapi;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restFisioterapi = Yii::$app->docoRest->fisioterapi;
    }

    public function actions()
    {
        $actions = parent::actions();
        $newActions = [
            'index' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal\IndexAction',
            'get-data-rajal' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal\GetDataRajalAction',
            'show-popup-pdf' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal\ShowPopupPdfAction',
            'process-sync-pdf' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal\ProcessSyncPdfAction',
            'download-file-pdf' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal\DownloadFilePdfAction',
            'show-popup-excel' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal\ShowPopupExcelAction',
            'process-sync-excel' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal\ProcessSyncExcelAction',
            'download-file-excel' => 'Doco\fisioterapi\actions\LaporanPemeriksaanFisioterapiRajal\DownloadFileExcelAction'
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

}
