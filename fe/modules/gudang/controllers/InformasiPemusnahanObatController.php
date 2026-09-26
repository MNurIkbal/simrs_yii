<?php

/**
 * @Author: DOCOTEL
 * @Date:   2018-12-26 10:49:08
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2020-05-29 00:12:19
 */

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class InformasiPemusnahanObatController extends DocoController {
    protected $_module = '/gudang/informasi-pemusnahan-obat/';

    public function actions() {
        return [
            'delete'                => 'Doco\gudang\actions\InformasiPemusnahanObatAlkes\DeleteAction',
            'get-data'              => 'Doco\gudang\actions\InformasiPemusnahanObatAlkes\GetDataAction',
            'get-data-pemusnahan'   => 'Doco\gudang\actions\InformasiPemusnahanObatAlkes\GetDataPemusnahanAction',
            'index'                 => 'Doco\gudang\actions\InformasiPemusnahanObatAlkes\IndexAction',
            'print-pemusnahan'      => 'Doco\gudang\actions\InformasiPemusnahanObatAlkes\PrintPemusnahanAction',
            'verifikasi-pemusnahan' => 'Doco\gudang\actions\InformasiPemusnahanObatAlkes\VerifikasiPemusnahanAction',
            'view'                  => 'Doco\gudang\actions\InformasiPemusnahanObatAlkes\ViewAction',
            'batal-pemusnahan' => 'Doco\gudang\actions\InformasiPemusnahanObatAlkes\BatalPemusnahanAction'
        ];
    }
}