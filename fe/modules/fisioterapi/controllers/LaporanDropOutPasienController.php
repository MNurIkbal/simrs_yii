<?php

/**
 * @author Andri Amirul (amdri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\controllers;

use app\components\DocoController;

class LaporanDropOutPasienController extends DocoController
{
    protected $_module = '/fisioterapi/laporan-drop-out-pasien/';

    public function init()
    {
        parent::init();
    }

    public function actions()
    {
        $actions = parent::actions();
        $newActions = [
            'index' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\IndexAction',
            'get-data' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\GetDataAction',
            'get-data-chart' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\GetDataChartAction',
            'export-pdf-chart' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\ExportPdfChartAction',
            'show-popup-pdf' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\ShowPopupPdfAction',
            'process-sync-pdf' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\ProcessSyncPdfAction',
            'download-file-pdf' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\DownloadFilePdfAction',
            'show-popup-excel' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\ShowPopupExcelAction',
            'process-sync-excel' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\ProcessSyncExcelAction',
            'download-file-excel' => 'Doco\fisioterapi\actions\LaporanDropOutPasien\DownloadFileExcelAction'
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

}
