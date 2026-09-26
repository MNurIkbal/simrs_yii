<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class LaporanResponTimeAnalisisController extends DocoController {
    public $_title = "Laporan Respon Time Analisis";
    public $_module = '/pengadaan/laporan-respon-time-analisis/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'export-excel'  => 'Doco\pengadaan\actions\LaporanResponTimeAnalisis\ExportExcelAction',
            'medis'         => 'Doco\pengadaan\actions\LaporanResponTimeAnalisis\IndexMedisAction',
            'non-medis'     => 'Doco\pengadaan\actions\LaporanResponTimeAnalisis\IndexNonMedisAction'
        ];
    }
}
