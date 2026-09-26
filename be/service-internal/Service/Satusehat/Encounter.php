<?php

namespace Integrasi\Service\Satusehat;

use Doco\components\DocoConstansId;
use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Satusehat\Models\LaporanKunjunganRjView;
use Integrasi\Service\Satusehat\Models\SatusehatPasien;
use Integrasi\Service\Satusehat\Models\SatusehatPegawai;
use Integrasi\Service\Satusehat\Models\SatusehatInstalasi;
use Integrasi\Service\Satusehat\Models\SatusehatRuangan;
use Integrasi\Service\Satusehat\Models\SatusehatPasienView;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Satusehat\Models\InfoDataKunjunganView;
use Integrasi\Service\Satusehat\Models\InfoKonsulPoliView;
use yii\helpers\ArrayHelper;

class Encounter extends \Integrasi\Contracts\DocoImplement
{
    const RESOURCE_TYPE = 'Encounter';
    protected $logType = 'Encounter';
    public $pendaftaranId;
    public $organizationId;

    public $lap_kunjungan_data = NULL;
    public $satusehat_pasien_id = NULL;
    public $satusehat_pegawai_id = NULL;
    public $satusehat_instalasi_id = NULL;
    public $satusehat_ruangan_id = NULL;

    public function execute()
    {
        // ini_set('memory_limit', '-1');
        // ini_set('max_execution_time', '3600');

        $this->setAttrSatusehat();
        $state = $this->state;
        
        $pendaftaranId = isset($this->attributes['result']['pendaftaran']['pendaftaran_id']) ? $this->attributes['result']['pendaftaran']['pendaftaran_id'] : null;
        $konsulpoliId = isset($this->attributes['result']['konsulpoli_id']) ? $this->attributes['result']['konsulpoli_id'] : NULL;

        if (isset($this->attributes['result']['pendaftaran']) ) {
            // from pendaftaran
            $status_pasien = isset($this->attributes['result']['pendaftaran']['status_pasien']) ? $this->attributes['result']['pendaftaran']['status_pasien'] : null;
            $instalasi_pendaftaran = isset($this->attributes['result']['pendaftaran']['instalasi_id']) ? $this->attributes['result']['pendaftaran']['instalasi_id'] : null;

            $pasien_id = isset($this->attributes['result']['pendaftaran']['pasien_id']) ? $this->attributes['result']['pendaftaran']['pasien_id'] : null;

            $pasien = SatusehatPasienView::find()->select(['*'])
                ->where("pasien_id = ". $pasien_id ." AND jenis_identitas <> 'null' AND jenis_identitas <> '' AND nomor_id_pasien <> 'null' AND nomor_id_pasien <> '' AND (satusehat_pasien_id IS NULL OR satusehat_pasien_id = '--')");
            $dataPasien = $pasien->asArray()->one();

            if(!empty($dataPasien)) {
                if ($dataPasien['jenis_identitas'] == DocoConstants::IDENTITAS_KTP) {
                    $payloadPasien = $this->buildPasien($dataPasien);
                    $resPasien = (new SatusehatService)->createPatient($payloadPasien, true);
                } else {
                    $payloadPasien = $this->buildPasien($dataPasien);
                    $resPasien = [
                        'error' => json_encode([
                            'message' => 'Jenis identitas pasien bukan NIK'
                        ])
                    ];
                }
                $this->setLogsPatient($pasien_id, $state, $payloadPasien, $resPasien);
            }
        }

        if($pendaftaranId) {
            $masterPayload = $this->masterPayload($pendaftaranId, $konsulpoliId);

            $payload = $this->build();
            $res = (new SatusehatService)->createEncounter($payload, true);
            $this->setLogs($pendaftaranId, $state, $payload, $res);
        }

        return json_encode([
            'service' => 'Satusehat-encounter',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
            'pendaftaran_id' => $pendaftaranId,
            'pasien_id' => $this->satusehat_pasien_id,
            'pegawai_id' => $this->satusehat_pegawai_id,
            'instalasi_id' => $this->satusehat_instalasi_id,
            'ruangan_id' => $this->satusehat_ruangan_id,
            'organizationId' => $this->organizationId, 
            'result_pasien' => isset($resPasien) ? $resPasien : NULL
        ]);
    }

    public function setLogs($pendaftaranId, $state, $payload, $result, $additionalId = null)
    {
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;
        $result_sercon = isset($result['response']) ? (is_array($result['response']) ? $result['response'] : $result['response']) : [];
        $encounter_id = isset($result_sercon['id']) ? $result_sercon['id'] : NULL;

        $error = !empty($result['error']) ? $result['error'] : NULL;

        if(!empty($error)) {
            $sync_response = $error;
        } else {                    
            $sync_response = isset($result['response']) ? (
                        is_array($result['response']) ? json_encode($result['response']) : $result['response']
                    ) : json_encode($result);
        }

        $logData[] = [
            'pendaftaran_id' => $pendaftaranId,
            'is_sent' => !empty($error) ? false : true,
            'type' => $this->logType,
            'state' => $state,
            'id_sync_sercon' => $uidSercon,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
            'payload' => json_encode($payload),
            'is_deleted' => false,
            'is_active' => true,
            'additional_id' => $additionalId,
            'satusehat_id' => $encounter_id,
            'sync_response' => $sync_response
        ];

        $this->saveLogs($logData);
    }

    public function saveLogs($data)
    {
        return Satusehat::batchInsert($data);
    }

    protected function build()
    {
        $gmtTime = (new DocoConstansId)->actionGetAdditional('gmt_time');
        $res = [
            'resourceType' => self::RESOURCE_TYPE,
            'status' => 'arrived',
            'class' => [
                'system' => 'http://terminology.hl7.org/CodeSystem/v3-ActCode',
                'code' => 'AMB',
                'display' => 'ambulatory'
            ],
            'subject' => [
                'reference' => 'Patient/'.$this->satusehat_pasien_id,
                'display' => $this->lap_kunjungan_data['nama_pasien']
            ],
            'participant' => [
                [
                    'type' => [
                        [
                            'coding' => [
                                [
                                    'system' => 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType',
                                    'code' => 'ATND',
                                    'display' => 'attender'
                                ]
                            ]
                        ],
                    ],
                    'individual' => [
                        'reference' => 'Practitioner/'.$this->satusehat_pegawai_id,
                        'display' => $this->lap_kunjungan_data['dokter_nama']
                    ]
                ],
            ],
            'period' => [
                'start' => date('Y-m-d', strtotime($this->lap_kunjungan_data['tgl_pendaftaran'])).'T'.date('H:i:s', strtotime($this->lap_kunjungan_data['tgl_pendaftaran'])).$gmtTime
            ],
            'location' => [
                [
                    'location' => [
                        'reference' => 'Location/'.$this->satusehat_ruangan_id,
                        'display' => $this->lap_kunjungan_data['ruangan_nama']
                    ]
                ]
            ],
            'statusHistory' => [
                [
                    'status' => 'arrived',
                    'period' => [
                        'start' => date('Y-m-d', strtotime($this->lap_kunjungan_data['tgl_pendaftaran'])).'T'.date('H:i:s', strtotime($this->lap_kunjungan_data['tgl_pendaftaran'])).$gmtTime
                    ]
                ]
            ],
            'serviceProvider' => [
                'reference' => 'Organization/'.$this->organizationId
            ],
            'identifier' => [
                [
                    'system' => 'http://sys-ids.kemkes.go.id/encounter/' . $this->organizationId,
                    'value' => $this->satusehat_pasien_id
                ]
            ]
        ];

        return $res;
    }

    protected function masterPayload($pendaftaran_id, $konsulpoliId = null) {
        if (!empty($konsulpoliId)) {
            $lap_kunjungan = InfoKonsulPoliView::find()->select([
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasien_id',
                    'nama_pasien',
                    'pegawai_id AS dokter_id',
                    'nama_dokter AS dokter_nama',
                    'instalasi_id',
                    'instalasi_nama',
                    'ruangan_id',
                    'ruangan_tujuan AS ruangan_nama',
                    'tgl_setujui AS tgl_pendaftaran',
                    'tgl_masukperiksa',
                    'tgl_selesaikonsul AS tgl_selesai'
                ])
                ->where('konsulpoli_id = '.$konsulpoliId)
                ->asArray()->one();
        } else {
            $lap_kunjungan = InfoDataKunjunganView::find()->select([
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasien_id',
                    'nama_pasien',
                    'dokter_id',
                    'dokter_nama',
                    'instalasi_id',
                    'instalasi_nama',
                    'ruangan_id',
                    'ruangan_nama',
                    'tgl_pendaftaran',
                    'tgl_masukperiksa',
                    'tglpasienpulang AS tgl_selesai'
                ])
                ->where('pendaftaran_id = '.$pendaftaran_id)
                ->asArray()->one();
        }

        $pasien = SatusehatPasien::find()
                        ->where('pasien_id = '.$lap_kunjungan['pasien_id'].' and satusehat_pasien_id is not null and is_active = true AND is_deleted = false')->asArray()->one();
        $pegawai = SatusehatPegawai::find()
                        ->where('pegawai_id = '.$lap_kunjungan['dokter_id'].' and satusehat_pegawai_id is not null and is_active = true AND is_deleted = false')->asArray()->one();
        $instalasi = SatusehatInstalasi::find()
                        ->where('instalasi_id = '.$lap_kunjungan['instalasi_id'].' and satusehat_instalasi_id is not null  and is_active = true AND is_deleted = false')->asArray()->one();
        $ruangan = SatusehatRuangan::find()
                        ->where('ruangan_id = '.$lap_kunjungan['ruangan_id'].' and satusehat_ruangan_id is not null and is_active = true AND is_deleted = false')->asArray()->one();

        $this->lap_kunjungan_data = $lap_kunjungan;
        $this->satusehat_pasien_id = isset($pasien['satusehat_pasien_id']) ? $pasien['satusehat_pasien_id'] : '--';
        $this->satusehat_pegawai_id = isset($pegawai['satusehat_pegawai_id']) ? $pegawai['satusehat_pegawai_id'] : null;
        $this->satusehat_instalasi_id = isset($instalasi['satusehat_instalasi_id']) ? $instalasi['satusehat_instalasi_id'] : null;
        $this->satusehat_ruangan_id = isset($ruangan['satusehat_ruangan_id']) ? $ruangan['satusehat_ruangan_id'] : null;

        return $lap_kunjungan;
    }

    protected function setAttrSatusehat()
    {
        $model = Cache::getLookupByType('satusehat');
        if ($model) {
            foreach ($model as $value) {
                if ($value['lookup_name'] == 'organization_id') {
                    $this->organizationId = $value['lookup_value'];
                }
            }
        }
    }

    protected function buildPasien($data) {
        return [
            'pasien_nik' => $data['nomor_id_pasien']
        ];
    }
    
    protected function setLogsPatient($patientId, $state, $payload, $result, $additionalId = null)
    {
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;
        $result_sercon = isset($result['response']) ? (is_array($result['response']) ? $result['response'] : json_decode($result['response'], true)) : [];
        $satusehat_patient_id = isset($result_sercon['entry'][0]['resource']['id']) ? $result_sercon['entry'][0]['resource']['id'] : '--';

        $error = !empty($result['error']) ? $result['error'] : NULL;

        if(!empty($error)) {
            $sync_response = $error;
        } else {        
            $sync_response = isset($result['response']) ? (
                        is_array($result['response']) ? json_encode($result['response']) : $result['response']
                    ) : json_encode($result);
        }

        $logData = [
            'is_sent' => !empty($error) ? false : true,
            'pendaftaran_id' => isset($this->attributes['result']['id']) ? DocoHelpers::decrypt($this->attributes['result']['id']) : null,
            'type' => 'Patient',
            'state' => $state,
            'id_sync_sercon' => $uidSercon,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
            'payload' => json_encode($payload),
            'is_deleted' => false,
            'is_active' => true,
            'additional_id' => $patientId,
            'satusehat_id' => $satusehat_patient_id,
            'sync_response' => $sync_response
        ];

        $satusehat = $this->saveLogsPasien($logData);

        $data_pasien = [
            'pasien_id' => $patientId,
            'satusehat_integration_id' => ArrayHelper::getValue($satusehat, 'id'),
            'satusehat_pasien_id' => $satusehat_patient_id
        ];

        $this->saveSatusehatPasien($data_pasien);
    }

    protected function saveLogsPasien($data)
    {
        $insertSatusehat = new Satusehat;
        $insertSatusehat->pendaftaran_id = ArrayHelper::getValue($data, 'pendaftaran_id');
        $insertSatusehat->is_sent = ArrayHelper::getValue($data, 'is_sent');
        $insertSatusehat->type = ArrayHelper::getValue($data, 'type');
        $insertSatusehat->state = ArrayHelper::getValue($data, 'state');
        $insertSatusehat->id_sync_sercon = ArrayHelper::getValue($data, 'id_sync_sercon');
        $insertSatusehat->created_date = ArrayHelper::getValue($data, 'created_date');
        $insertSatusehat->created_by = ArrayHelper::getValue($data, 'created_by');
        $insertSatusehat->payload = ArrayHelper::getValue($data, 'payload');
        $insertSatusehat->is_deleted = ArrayHelper::getValue($data, 'is_deleted');
        $insertSatusehat->is_active = ArrayHelper::getValue($data, 'is_active');
        $insertSatusehat->satusehat_id = ArrayHelper::getValue($data, 'satusehat_id');
        $insertSatusehat->sync_response = ArrayHelper::getValue($data, 'sync_response');
        $insertSatusehat->additional_id = ArrayHelper::getValue($data, 'additional_id');
        $insertSatusehat->save(false);
        return $insertSatusehat;
    }

    protected function saveSatusehatPasien($data) 
    {
        $pasienSatusehat= SatusehatPasien::find()
                    ->where(['pasien_id' => ArrayHelper::getValue($data, 'pasien_id')])
                    ->one();
        if (empty($pasienSatusehat)) {
            $pasienSatusehat = new SatusehatPasien;
            $pasienSatusehat->pasien_id = ArrayHelper::getValue($data, 'pasien_id');
            $pasienSatusehat->satusehat_integration_id = ArrayHelper::getValue($data, 'satusehat_integration_id');
            $pasienSatusehat->satusehat_pasien_id = ArrayHelper::getValue($data, 'satusehat_pasien_id');
            $pasienSatusehat->save();
        } else {
            $pasienSatusehat->satusehat_integration_id = ArrayHelper::getValue($data, 'satusehat_integration_id');
            $pasienSatusehat->satusehat_pasien_id = ArrayHelper::getValue($data, 'satusehat_pasien_id');
            $pasienSatusehat->save();
        }

        return $pasienSatusehat;
    }
}
