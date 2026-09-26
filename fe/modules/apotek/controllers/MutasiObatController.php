<?php

/**
 * @author : Ardi Pratama (ardi@docotel.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\controllers;

use Yii;
use app\components\DocoController;

class MutasiObatController extends DocoController
{

    protected $_title = "Mutasi Obat";
    protected $_module = '/apotek/mutasi-obat';
    protected $allowAction = [
        '*'
    ];

    public function actions() {
        return [
            'index' => 'Doco\apotek\actions\MutasiObat\IndexAction',
            'save' => 'Doco\apotek\actions\MutasiObat\SaveAction'
        ];
    }
}