<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class LaporanPemakaianBmhpPasienController extends DocoController {
    public $_title = "Laporan Pemakaian BMHP Pasien";
    public $_module = '/gudang/laporan-pemakaian-bmhp-pasien';
    
    protected $allowAction = ['*'];

    public function actions() {
        return [
            'index'         => 'Doco\gudang\actions\LaporanPemakaianBmhpPasien\IndexAction',
            'get-data'      => 'Doco\gudang\actions\LaporanPemakaianBmhpPasien\GetDataAction',
            'export-excel'  => 'Doco\gudang\actions\LaporanPemakaianBmhpPasien\ExportExcelAction'
        ];
    }

}
