<?php

namespace Doco\fisioterapi\controllers;

use app\components\DocoController;

class PemeriksaanRanapController extends DocoController
{
    protected $_title = 'Pemeriksaan Fisioterapi Rawat Inap';
    protected $_module = '/fisioterapi/pemeriksaan-ranap';
    protected $allowAction = ['*'];

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        $newActions = [
            'index' => 'Doco\fisioterapi\actions\PemeriksaanRanap\IndexAction',
            'is-create-soap' => 'Doco\fisioterapi\actions\PemeriksaanRanap\IsCreateSoapAction',
            'get-data-cppt' => 'Doco\fisioterapi\actions\PemeriksaanRanap\GetDataCpptAction',
            'store-soap' => 'Doco\fisioterapi\actions\PemeriksaanRanap\StoreSoapAction',
            'update-soap' => 'Doco\fisioterapi\actions\PemeriksaanRanap\UpdateSoapAction',
            'modal-hasil-radiologi' => 'Doco\fisioterapi\actions\PemeriksaanRanap\ModalHasilRadiologiAction',
            'cetak-cppt' => 'Doco\fisioterapi\actions\PemeriksaanRanap\CetakCpptAction',
            'cppt' => 'Doco\fisioterapi\actions\PemeriksaanRanap\CpptAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }
}

?>