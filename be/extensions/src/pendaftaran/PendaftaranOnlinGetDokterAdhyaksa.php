<?php

namespace Extensions\pendaftaran;

use Doco\models\pendaftaran\InfoJadwalDokterView;

class PendaftaranOnlinGetDokterAdhyaksa extends \Doco\processes\PendaftaranOnlineGetDokterProcess
{
    protected function getJadwalDokter()
    {
        return InfoJadwalDokterView::find()->select([
            'pegawai_id', 'nama_pegawai'
        ])->where([
            'is_active'       => true,
            'ruangan_id'      => $this->ruanganId,
            'hari_jadwalbuka' => $this->hariId,
            'ruangan_online' => true,
            'pegawai_online' => true
        ]);
    }
}