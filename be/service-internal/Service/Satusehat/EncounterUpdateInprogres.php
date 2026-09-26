<?php

namespace Integrasi\Service\Satusehat;

use Doco\components\DocoConstansId;
use Integrasi\Components\DocoConstants;
use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\InfoDataKunjunganView;
use Integrasi\Service\Satusehat\Models\InfoKonsulPoliView;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Satusehat\Models\SatusehatInstalasi;
use Integrasi\Service\Satusehat\Models\SatusehatPasien;
use Integrasi\Service\Satusehat\Models\SatusehatPegawai;
use Integrasi\Service\Satusehat\Models\SatusehatRuangan;
use Integrasi\Service\Satusehat\Models\LaporanKunjunganRjView;

class EncounterUpdateInprogres extends \Integrasi\Service\Satusehat\Encounter
{
    public $pendaftaranId;
    public $konsulpoliId;
    public $organizationId;
    public $dataAttr;

    public function execute()
    {
        $this->setAttrSatusehat();
        $state = $this->state;
        $this->logType = 'EncounterUpdateInprogres';
        $this->dataAttr = $this->attributes['result'];
        
        $dataAttr = $this->dataAttr;

        $pendaftaranId = $dataAttr['pendaftaran_id'];
        $this->konsulpoliId = (int)$this->dataAttr['konsulpoli_id'];

        if ($pendaftaranId) {
            $this->masterPayloadUpdate($pendaftaranId);

            $payload = $this->buildUpdateInprogres();
            $res = (new SatusehatService)->updateEncounter($payload);
            $this->setLogsUpdate($pendaftaranId, $state, $payload, $res);
        }

        return json_encode([
            'service' => 'Satusehat-encounterUpdateInprogres',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    public function setLogsUpdate($pendaftaranId, $state, $payload, $result, $additionalId = null)
    {
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;
        $result_sercon = isset($result['response']) ? (is_array($result['response']) ? $result['response'] : json_decode($result['response'], true)) : [];
        $encounter_id = isset($result_sercon['id']) ? $result_sercon['id'] : NULL;

        $error = !empty($result['error']) ? $result['error'] : NULL;

        if (!empty($error)) {
            $sync_response = $error;
        } else {
            $sync_response = isset($result['response']) ? (
            is_array($result['response']) ? json_encode($result['response']) : $result['response']
            ) : json_encode($result);
        }
        
        $logData[] = [
            'pendaftaran_id' => $pendaftaranId,
            'is_sent' => true,
            'type' => $this->logType,
            'state' => $state,
            'id_sync_sercon' => $uidSercon,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
            'payload' => json_encode($payload),
            'is_deleted' => false,
            'is_active' => true,
            'satusehat_id' => $encounter_id,
            'sync_response' => $sync_response,
            'additional_id' => $additionalId
        ];

        $this->saveLogs($logData);
    }

    public function saveLogs($data)
    {
        return Satusehat::batchInsert($data);
    }

    protected function buildUpdateInprogres()
    {
        $satusehat_encounter = Satusehat::find()
            ->select([
                'id',
                'pendaftaran_id',
                'payload',
                'satusehat_id',
                'type'
            ]);

        if (!empty($this->konsulpoliId)) {
            $encounterData = $satusehat_encounter->where("type = '" . self::RESOURCE_TYPE . "'
                and payload LIKE '%". $this->satusehat_pasien_id . "%'
                and payload LIKE '%". $this->satusehat_pegawai_id . "%'
                and payload LIKE '%". $this->satusehat_ruangan_id ."%'
                and pendaftaran_id = " . $this->lap_kunjungan_data['pendaftaran_id'] . "
                and is_active = true 
                and is_deleted = false")
                ->orderBy(['id' => SORT_DESC])
                ->asArray()->one();
        } else {
            $encounterData = $satusehat_encounter->where("type = '" . self::RESOURCE_TYPE . "' 
                and pendaftaran_id = " . $this->lap_kunjungan_data['pendaftaran_id'] . "
                and is_active = true 
                and is_deleted = false")
                ->asArray()->one();
        }
		$gmtTime = (new DocoConstansId)->actionGetAdditional('gmt_time');

        $tanggal_daftar = date('Y-m-d', strtotime($this->lap_kunjungan_data['tgl_pendaftaran'])) . 'T' . date('H:i:s', strtotime($this->lap_kunjungan_data['tgl_pendaftaran'])) . $gmtTime;

        $tgl_progress = date('Y-m-d', strtotime($this->lap_kunjungan_data['tgl_masukperiksa'])) . 'T' . date('H:i:s', strtotime($this->lap_kunjungan_data['tgl_masukperiksa'])) . $gmtTime;

        $payload = $this->build();
        $payload['id'] = !empty($encounterData['satusehat_id']) ? $encounterData['satusehat_id'] : NULL;
        $payload['status'] = 'in-progress';
        $payload['statusHistory'] = [];
        $payload['statusHistory'][] = [
            'status' => 'arrived',
            'period' => [
                'start' => $tanggal_daftar,
                'end' => $tgl_progress
            ]
        ];
        $payload['statusHistory'][] = [
            'status' => 'in-progress',
            'period' => [
                'start' => $tgl_progress,
            ]
        ];

        return $payload;
    }

    
    protected function masterPayloadUpdate($pendaftaran_id) {
        if (!empty($this->konsulpoliId)) {
            $lap_kunjungan = InfoKonsulPoliView::find()->select([
                'pendaftaran_id',
                'no_pendaftaran',
                'pasien_id',
                'nama_pasien',
                'pegawai_id',
                'nama_dokter AS dokter_nama',
                'instalasi_id',
                'instalasi_nama',
                'ruangan_id',
                'ruangan_tujuan AS ruangan_nama',
                'tgl_setujui AS tgl_pendaftaran',
                'tgl_masukperiksa'
            ])
            ->where('pendaftaran_id = '.$pendaftaran_id)
            ->andWhere('konsulpoli_id = '.$this->konsulpoliId)
            ->andWhere('status_konsul_id = '. DocoConstants::STATUS_KONSUL_BLM_DIJAWAB)
            ->asArray()->one();
        } else {
            $lap_kunjungan = InfoDataKunjunganView::find()->select([
                'pendaftaran_id',
                'no_pendaftaran',
                'pasien_id',
                'nama_pasien',
                'dokter_id AS pegawai_id',
                'dokter_nama',
                'instalasi_id',
                'instalasi_nama',
                'ruangan_id',
                'ruangan_nama',
                'tgl_pendaftaran',
                'tgl_masukperiksa'
            ])->where('pendaftaran_id = '.$pendaftaran_id)
            ->asArray()->one();
        }

        $pasien = $pegawai = $instalasi = $ruangan = '';
        if (isset($lap_kunjungan['pasien_id'])) {
            $pasien = SatusehatPasien::find()
                            ->where('pasien_id = '.$lap_kunjungan['pasien_id'].' and satusehat_pasien_id is not null and is_active = true AND is_deleted = false')->asArray()->one();
        }
        if (isset($lap_kunjungan['pegawai_id'])) {
            $pegawai = SatusehatPegawai::find()
                            ->where('pegawai_id = '.$lap_kunjungan['pegawai_id'].' and satusehat_pegawai_id is not null and is_active = true AND is_deleted = false')->asArray()->one();
        }                
        if (isset($lap_kunjungan['instalasi_id'])) {
            $instalasi = SatusehatInstalasi::find()
                            ->where('instalasi_id = '.$lap_kunjungan['instalasi_id'].' and satusehat_instalasi_id is not null  and is_active = true AND is_deleted = false')->asArray()->one();
        }                
        if (isset($lap_kunjungan['ruangan_id'])) {
            $ruangan = SatusehatRuangan::find()
                            ->where('ruangan_id = '.$lap_kunjungan['ruangan_id'].' and satusehat_ruangan_id is not null and is_active = true AND is_deleted = false')->asArray()->one();
        }                

        $this->lap_kunjungan_data = $lap_kunjungan;
        $this->satusehat_pasien_id = isset($pasien['satusehat_pasien_id']) ? $pasien['satusehat_pasien_id'] : '--';
        $this->satusehat_pegawai_id = isset($pegawai['satusehat_pegawai_id']) ? $pegawai['satusehat_pegawai_id'] : null;
        $this->satusehat_instalasi_id = isset($instalasi['satusehat_instalasi_id']) ? $instalasi['satusehat_instalasi_id'] : null;
        $this->satusehat_ruangan_id = isset($ruangan['satusehat_ruangan_id']) ? $ruangan['satusehat_ruangan_id'] : null;

        return $lap_kunjungan;
    }
}
