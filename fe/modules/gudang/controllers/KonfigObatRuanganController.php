<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class KonfigObatRuanganController extends DocoController {
    public $_title = "Konfigurasi Obat Ruangan";
    public $_module = '/gudang/konfig-obat-ruangan/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'index'             => 'Doco\gudang\actions\KonfigObatRuangan\IndexAction',
            'mapping'           => 'Doco\gudang\actions\KonfigObatRuangan\MappingAction',
            'get-by-ruangan'    => 'Doco\gudang\actions\KonfigObatRuangan\GetByRuanganAction'
        ];
    }
}
