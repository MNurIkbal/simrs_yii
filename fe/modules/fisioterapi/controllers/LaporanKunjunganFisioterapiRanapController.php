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

class LaporanKunjunganFisioterapiRanapController extends DocoController
{
    protected $_module = '/fisioterapi/laporan-kunjungan-fisioterapi-ranap/';
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
            'index' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap\IndexAction',
            'get-data-ranap' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap\GetDataRanapAction',
            'show-popup-pdf' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap\ShowPopupPdfAction',
            'process-sync-pdf' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap\ProcessSyncPdfAction',
            'download-file-pdf' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap\DownloadFilePdfAction',
            'show-popup-excel' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap\ShowPopupExcelAction',
            'process-sync-excel' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap\ProcessSyncExcelAction',
            'download-file-excel' => 'Doco\fisioterapi\actions\LaporanKunjunganFisioterapiRanap\DownloadFileExcelAction'
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }
}
