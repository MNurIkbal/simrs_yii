<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;

class LaporanPasienFisioterapiRanapController extends DocoController
{
    protected $_module = '/fisioterapi/laporan-pasien-fisioterapi-ranap/';
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
            'index' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap\IndexAction',
            'get-data' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap\GetDataAction',
            'show-popup-pdf' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap\ShowPopupPdfAction',
            'process-sync-pdf' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap\ProcessSyncPdfAction',
            'download-file-pdf' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap\DownloadFilePdfAction',
            'show-popup-excel' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap\ShowPopupExcelAction',
            'process-sync-excel' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap\ProcessSyncExcelAction',
            'download-file-excel' => 'Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap\DownloadFileExcelAction'
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

}
