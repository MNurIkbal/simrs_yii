<?php

namespace Doco\master\actions\PaketFisio;

use app\components\DocoHelpers;

class GetInstalasiRuanganAction extends BaseCurrentAction
{
    public function run()
    {
        $api = 'allow/get-data-instalasi-ruangan';
        $dataId = 'ruangan_id';
        $dataName = [
            'instalasi_nama',
            'ruangan_nama'
        ];
        $getRest = $this->_restMaster;
        $docoHelpers = new DocoHelpers();
        return $docoHelpers->paginationSelec2($api, $dataId, $dataName, $getRest, null);
    }
}
