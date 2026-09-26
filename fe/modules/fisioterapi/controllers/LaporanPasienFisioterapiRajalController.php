<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;

class LaporanPasienFisioterapiRajalController extends DocoController
{
    protected $_module = '/fisioterapi/laporan-pasien-fisioterapi-rajal/';
    protected $_restFisioterapi;

    public function init()
    {
        parent::init();
        $this->_restFisioterapi = Yii::$app->docoRest->fisioterapi;
    }

    public function actions()
    {
        $actions = parent::actions();
        $newActions = [
            'index' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal\IndexAction',
            'get-data-rajal' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal\GetDataRajalAction',
            'show-popup-pdf' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal\ShowPopupPdfAction',
            'process-sync-pdf' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal\ProcessSyncPdfAction',
            'download-file-pdf' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal\DownloadFilePdfAction',
            'show-popup-excel' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal\ShowPopupExcelAction',
            'process-sync-excel' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal\ProcessSyncExcelAction',
            'download-file-excel' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRajal\DownloadFileExcelAction'
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

}
