<?php

/**
 * @author Andri Amirul (amdri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\controllers;

use app\components\DocoController;

class InformasiJadwalTerapiController extends DocoController
{
    protected $_module = '/fisioterapi/informasi-jadwal-terapi/';

    public function init()
    {
        parent::init();
    }

    public function actions()
    {
        $actions = parent::actions();
        $newActions = [
            'index' => 'Doco\fisioterapi\actions\InformasiJadwalTerapi\IndexAction',
            'get-data' => 'Doco\fisioterapi\actions\InformasiJadwalTerapi\GetDataAction',
            'get-jam-terapi' => 'Doco\fisioterapi\actions\InformasiJadwalTerapi\GetJamTerapiAction',
            'jadwal-fisioterapi' => 'Doco\fisioterapi\actions\InformasiJadwalTerapi\JadwalFisioterapiAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

}
