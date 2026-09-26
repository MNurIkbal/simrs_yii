<?php

namespace Extensions\pendaftaran;

use Doco\components\DocoConstants;
use Doco\models\Ruangan;

class PendaftaranOnlinGetRuanganAdhyaksa extends \Doco\processes\PendaftaranOnlineGetRuanganProcess
{
    protected function getRuangan()
    {
        return Ruangan::find()->where([
            'instalasi_id' => DocoConstants::VAR_I_RJ, //VAR_I_RJ = 1
            'is_active'    => true,
            'is_deleted'   => false,
            'is_online' => true
        ]);
    }
}