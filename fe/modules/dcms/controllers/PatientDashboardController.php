<?php

namespace Doco\dcms\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoConstants;

class PatientDashboardController extends DocoController {
    protected $_restDcms;

    public function init()
    {
        parent::init();
        $this->_restDcms = Yii::$app->docoRest->dcms;
    }

    public function actionIndex()
    {
        $title = 'Dashboard Perhitungan Pasien';

        $listInstalasi = $this->helper->guzzleExec($this->_restDcms, [
            'url' => 'patient-dashboard/list-instalasi',
            'method' => 'GET',
        ]);
        $slug = [
            DocoConstants::INSTALASI_ID_RI => 'IPD_ADMISSIOn',
            DocoConstants::INSTALASI_ID_RJ => 'OPD',
            DocoConstants::INSTALASI_ID_RD => 'EMERGENCY',
            DocoConstants::INSTALASI_ID_RAD => 'RADIOLOGY',
            DocoConstants::INSTALASI_ID_LAB => 'LABORATORY',
            DocoConstants::INSTALASI_ID_BEDAH => 'OPERATING_THEATRE',
            DocoConstants::INSTALASI_GUDANG_FARMASI => 'PHARMACY',
        ];
        return $this->render('index', compact('title', 'listInstalasi', 'slug') );
    }

    public function actionGetRekap()
    {
        return $this->helper->guzzleExec($this->_restDcms, [
            'url' => 'patient-dashboard/get-rekap',
            'method' => 'GET',
            'returnResponse' => true
        ]);
    }
}