<?php

/**
 * @author Andri Amirul (amdri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;
use GuzzleHttp\Exception\RequestException;

class LaporanKunjunganFisioterapiRajalController extends DocoController
{
    protected $_module = '/fisioterapi/laporan-kunjungan-fisioterapi-rajal/';
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
            'index' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal\IndexAction',
            'get-data-rajal' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal\GetDataRajalAction',
            'show-popup-pdf' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal\ShowPopupPdfAction',
            'process-sync-pdf' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal\ProcessSyncPdfAction',
            'download-file-pdf' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal\DownloadFilePdfAction',
            'show-popup-excel' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal\ShowPopupExcelAction',
            'process-sync-excel' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal\ProcessSyncExcelAction',
            'download-file-excel' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRajal\DownloadFileExcelAction'
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

}
