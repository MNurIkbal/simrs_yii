<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\InfoPendaftaranOl;
use Integrasi\Service\Sirs\Models\StokKuotaDoktor;
use Integrasi\Service\Sirs\Models\SlotJadwalDokter;
use Integrasi\Service\Sirs\Models\KuotaDokter;
use Integrasi\Service\Sirs\Models\KonfigSystem;

class CancelReservasi extends \Integrasi\Contracts\DocoImplement
{
    CONST BATAL = 566;
    CONST BPJS = 6;

    public function execute()
    {
        $id = $this->id;
        $state = $this->state;
        $user = $this->user_identity;
        $currDate = date('Y-m-d', strtotime('NOW'));
        $update = null;

        if(empty($id)) {
            $condition = [
                'no_pendaftaranol' => $this->no_pendaftaranol
            ];
        } else {
            $condition = [
                'pendaftaranol_id' => $id
            ];
        }

        $data = InfoPendaftaranOl::find()
        ->where($condition)
        ->asArray()
        ->one();
        
        if($data['slot_sequence']) {
            if($data['status_daftar_ol'] == self::BATAL) {
                $tglDaftar = date('Y-m-d', strtotime($data['tgl_kunjungan']));
                if($tglDaftar == $currDate) {
                    $currTime = date('H:i:s', strtotime('NOW'));
                    $getSlot = SlotJadwalDokter::find()->where([
                        'jadwaldokter_id' => $data['jadwaldokter_id'],
                        'slot_sequence' => $data['slot_sequence']
                    ])
                    ->asArray()
                    ->one();
                    if($getSlot && $getSlot['jam_mulai'] < $currTime) {
                        $update = $this->insertKuota($data, $user);
                    }
                } else if ($tglDaftar > $currDate) {
                    $update = $this->insertKuota($data, $user);
                }
            }
        } else if(!$this->is_extensionmhg) {
            if($data['status_daftar_ol'] == self::BATAL) {
                $update = $this->insertKuota($data, $user);
            }
        }

        return json_encode([
            'service' => 'Cancel-reservasi',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
            'attributes' => $update,
            'status' => $data
        ]);
    }

    private function insertKuota($data, $user)
    {   
        $update = KuotaDokter::find()
        ->where([
            'jadwaldokter_id' => $data['jadwaldokter_id'],
            'is_online' => true
        ])->one();

        $kuotaBpjs = $update->kuota_bpjs_online;
        $kuotaNonBpjs = $update->kuota_nonbpjs_online;

        $update->kuota_tersedia = $update->kuota_tersedia + 1;
        $update->kuota_keluar = $update->kuota_keluar == 0 ? 0 : $update->kuota_keluar - 1;
        if($data['carabayar_id'] == self::BPJS) {
            $update->kuota_bpjs_online = (float) $kuotaBpjs + 1;
        } else {
            $update->kuota_nonbpjs_online = (float) $kuotaNonBpjs + 1;
        }
        $update->save(false); 
        
        return $update;
    }
}