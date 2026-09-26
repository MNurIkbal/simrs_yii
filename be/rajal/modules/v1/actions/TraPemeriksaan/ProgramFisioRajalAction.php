<?php

namespace app\modules\v1\actions\TraPemeriksaan;

use Doco\Repositories\LookUpTransaksiRepositories;
use Yii;

class ProgramFisioRajalAction extends BaseCurrentAction
{
    /**
     * @method setStatusprogram
     * @param Integer $pasienId (pasien_id)
     * @return Object
     */
    public static function setStatusProgram($pasienId)
    {
        $lookUpTransaksi = new LookUpTransaksiRepositories;
        $statusProgramClose = $lookUpTransaksi->getStatusCloseFisio();
        $restFisio = Yii::$app->docoRest->fisioterapi;
        $payload = [
            'pasien_id' => $pasienId,
            'status_program_fisio' => $statusProgramClose
        ];
        $restFisio->post('informasi-program-fisioterapi-rajal/set-status-program', [
            'form_params' => $payload
        ]);
    }
}
