<?php

namespace Integrasi\Service\Satusehat;

use Doco\components\DocoConstansId;
use Doco\models\ResumeMedisRi;
use Yii;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Satusehat\Models\SatusehatPasien;
use Integrasi\Service\Satusehat\Models\SatusehatPegawai;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Satusehat\Models\InfoDataKunjunganView;
use Integrasi\Service\Satusehat\Models\InfoKonsulPoliView;
use Integrasi\Service\Satusehat\Models\PemeriksaanFisik;
use Integrasi\Service\Satusehat\Models\SatusehatRuangan;

// use Integrasi\Service\Satusehat\Models\ResumeMedisRi;

class Observation extends \Integrasi\Contracts\DocoImplement
{
    const RESOURCE_TYPE = 'Observation';
    protected $logType = 'Observation';

    public $resourceStatus;

    public $pendaftaranId;
    public $konsulpoliId;

    public $resumeMedis;
    public $asesmenMedis;

    public $lap_kunjungan_data = NULL;
    public $satusehat_pasien_id = NULL;
    public $satusehat_pegawai_id = NULL;
    public $satusehat_ruangan_id = NULL;
    public $satusehat_encounter_id = NULL;

    public $codeObservation = [
        "detaknadi" => [
            "code" =>  "8867-4", "display" => "Heart rate", "unit" => "beats/minute", "unit_code" => "/min"
        ],
        "pernapasan" => [
            "code" => "9279-1", "display" => "Respiratory rate", "unit" => "breaths/minute", "unit_code" => "/min"
        ],
        "systolic" => [
            "code" => "8480-6", "display" => "Systolic blood pressure", "unit" => "mm[Hg]", "unit_code" => "mm[Hg]"
        ],
        "diastolic" => [
            "code" => "8462-4", "display" => "Diastolic blood pressure", "unit" => "mm[Hg]", "unit_code" => "mm[Hg]"
        ],
        "suhutubuh" => [
            "code" => "8310-5", "display" => "Body temperature", "unit" => "C", "unit_code" => "Cel"
        ]
    ];

    
    public function execute()
    {
        $state = $this->state;
        $this->logType = 'Observation';
        $this->setAttrSatusehat();

        if ($state == DocoConstants::STATE_CREATE) {
            $pendaftaranId = isset($this->attributes['result']['pendaftaran_id']) ? $this->attributes['result']['pendaftaran_id'] : NULL;
            $konsulpoliId = isset($this->attributes['result']['konsulpoli_id']) ? $this->attributes['result']['konsulpoli_id'] : NULL;
            $this->resourceStatus = 'final';
        }
        if ($state == DocoConstants::STATE_UPDATE) {
            $pendaftaranId = isset($this->attributes['result']['data']['pendaftaran_id']) ? $this->attributes['result']['data']['pendaftaran_id'] : NULL;
            $this->resourceStatus = 'amended';
        }
        $this->pendaftaranId = $pendaftaranId;
        $this->konsulpoliId = $konsulpoliId;

        
        if ($pendaftaranId) {
            $masterPayload = $this->masterPayload($pendaftaranId);
            if (!$masterPayload) {
                return json_encode([
                    'service' => 'Satusehat-Observation',
                    'status' => 'cannot hit to satu sehat',
                    'message' => 'update resume medis & status kunjungan bukan pulang/rujuk ranap',
                    'timestamp' => date('Y-m-d H:i:s'),
                ]);
            }

            $subjects = $this->getDataSubjects();

            foreach ($subjects as $subject => $value) {
                $payload = $this->build($subject, $value);
                $res = (new SatusehatService)->createObservation($payload, true);
                $this->setLogs($this->pendaftaranId, $state, $payload, $res);
            }

        }
        
        return json_encode([
            'service' => 'Satusehat-Observation',
            'state' => $state,
            'payload' => $pendaftaranId,
            'konsul' => $konsulpoliId,
            'satusehat_pasien_id' => $this->satusehat_pasien_id,
            'satusehat_pegawai_id' => $this->satusehat_pegawai_id,
            'satusehat_ruangan_id' => $this->satusehat_ruangan_id,
            'lap_kunjungan_data' => $this->lap_kunjungan_data,
            'timestamp' => date('Y-m-d H:i:s'),
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

    protected function build($subject, $value)
    {
        $gmtTime = (new DocoConstansId)->actionGetAdditional('gmt_time');
        $recent_time = date('Y-m-d') . 'T' . date('H:i:s') . $gmtTime;

        $res = [
            'resourceType' => self::RESOURCE_TYPE,
            'status' => $this->resourceStatus,
            'category' => [
                [
                    "coding" => [
                        [
                            "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
                            "code" => "vital-signs",
                            "display" => "Vital Signs"
                        ]
                    ]
                ]
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://loinc.org",
                        "code" => $this->codeObservation[$subject]["code"],
                        "display" => $this->codeObservation[$subject]["display"]
                    ]
                ]
            ],
            "subject" => [
                "reference" => "Patient/" . $this->satusehat_pasien_id,
                "display" =>  $this->lap_kunjungan_data['nama_pasien']
            ],
            "performer" => [
                [
                    "reference" => "Practitioner/" . $this->satusehat_pegawai_id,
                    "display" => $this->lap_kunjungan_data['dokter_nama']
                ]
            ],
            "encounter" => [
                "reference" => "Encounter/" . $this->satusehat_encounter_id
            ],
            "effectiveDateTime" => $recent_time,
            "issued" => $recent_time,
            "valueQuantity" => [
                "value" => (int)$value,
                "unit" => $this->codeObservation[$subject]["unit"],
                "system" => "http://unitsofmeasure.org",
                "code" => $this->codeObservation[$subject]["unit_code"]
            ]
        ];

        return $res;
    }

    protected function masterPayload($pendaftaran_id) {
        if (!empty($this->konsulpoliId)) {
            $lap_kunjungan = InfoKonsulPoliView::find()->select([
                    'konsulpoli_id',
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasien_id',
                    'nama_pasien',
                    'pegawai_id AS dokter_id',
                    'nama_dokter AS dokter_nama',
                    'ruangan_id',
                    'ruangan_tujuan AS ruangan_nama',
                    'status_periksa'
                ])
                ->where('konsulpoli_id = '.$this->konsulpoliId)
                ->asArray()->one();
        } else {
            $lap_kunjungan = InfoDataKunjunganView::find()->select([
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'pasien_id',
                    'nama_pasien',
                    'dokter_id',
                    'dokter_nama',
                    'ruangan_id',
                    'ruangan_nama',
                    'status_periksa'
                ])
                ->where('pendaftaran_id = '.$pendaftaran_id)
                ->asArray()->one();
        }

        if ($this->state == DocoConstants::STATE_UPDATE && $lap_kunjungan['status_periksa'] != DocoConstants::STATUS_PULANG && $lap_kunjungan['status_periksa'] != DocoConstants::STATUS_RUJUK_RAWAT_INAP) {
            return false;
        }

        $pasien = SatusehatPasien::find()
                        ->where('pasien_id = '.$lap_kunjungan['pasien_id'].' and satusehat_pasien_id is not null and is_active = true AND is_deleted = false')->asArray()->one();
        $pegawai = SatusehatPegawai::find()
                        ->where('pegawai_id = '.$lap_kunjungan['dokter_id'].' and satusehat_pegawai_id is not null and is_active = true AND is_deleted = false')->asArray()->one();
        $ruangan = SatusehatRuangan::find()
                        ->where('ruangan_id = '.$lap_kunjungan['ruangan_id'].' and satusehat_ruangan_id is not null and is_active = true AND is_deleted = false')->asArray()->one();

        $this->lap_kunjungan_data = $lap_kunjungan;
        $this->satusehat_pasien_id = isset($pasien['satusehat_pasien_id']) ? $pasien['satusehat_pasien_id'] : '--';
        $this->satusehat_pegawai_id = isset($pegawai['satusehat_pegawai_id']) ? $pegawai['satusehat_pegawai_id'] : null;
        $this->satusehat_ruangan_id = isset($ruangan['satusehat_ruangan_id']) ? $ruangan['satusehat_ruangan_id'] : null;

        // $resume_medis = ResumeMedisRi::find()->where('pendaftaran_id', $pendaftaran_id)->asArray()->one();
        $resume_medis = ResumeMedisRi::find()->select([
            'nadi AS detaknadi',
            'rr AS pernapasan',
            'td AS tekanandarah',
            'suhu AS suhutubuh'
        ])->andWhere(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
        
        if (!empty($resume_medis) && !empty($resume_medis['tekanandarah'])) {
            $systolDiastol = explode('/',$resume_medis['tekanandarah']);
            $resume_medis['systolic'] = isset($systolDiastol[0]) && !empty($systolDiastol) ? $systolDiastol[0] : 0;
            $resume_medis['diastolic'] = isset($systolDiastol[1]) && !empty($systolDiastol) ? $systolDiastol[1] : 0;
        }

        $this->resumeMedis = !empty($resume_medis) ? $resume_medis : NULL;

        if ($this->state == DocoConstants::STATE_CREATE) {
            $asesmen_medis = PemeriksaanFisik::find()->select([
                'detaknadi',
                'pernapasan',
                'tekanandarah',
                'td_systolic AS systolic',
                'td_diastolic AS diastolic',
                'suhutubuh'
            ])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            $this->asesmenMedis = !empty($asesmen_medis) ? $asesmen_medis : NULL;
        } else $this->asesmenMedis = NULL;

        
        if (!empty($this->konsulpoliId)) {
            $satusehat_encounter = Satusehat::find()
                ->select([
                    'satusehat_id'
                ])->where("type = '" . DocoConstants::SATU_SEHAT_TYPE_ENCOUNTER. "' 
                    and payload LIKE '%". $this->satusehat_pasien_id . "%'
                    and payload LIKE '%". $this->satusehat_pegawai_id . "%'
                    and payload LIKE '%". $this->satusehat_ruangan_id ."%'
                    and pendaftaran_id = " . $pendaftaran_id . "
                    and is_active = true 
                    and is_deleted = false")
                ->orderBy(['id' => SORT_DESC])
                ->asArray()->one();
            $this->satusehat_encounter_id = $satusehat_encounter['satusehat_id'];
        } else {
            $satusehat_encounter = Satusehat::find()
                ->select([
                    'satusehat_id'
                ])->where("type = '" . DocoConstants::SATU_SEHAT_TYPE_ENCOUNTER. "' 
                    and pendaftaran_id = " . $pendaftaran_id . "
                    and is_active = true 
                    and is_deleted = false")
                ->asArray()->one();
            $this->satusehat_encounter_id = $satusehat_encounter['satusehat_id'];
        }

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
    protected function getDataSubjects()
    {
        $subjects = [];
        if (isset($this->resumeMedis['detaknadi']) && !empty($this->resumeMedis['detaknadi'])) {
            $subjects['detaknadi'] = $this->resumeMedis['detaknadi'];
        } elseif (isset($this->asesmenMedis['detaknadi']) && !empty($this->asesmenMedis['detaknadi'])) {
            $subjects['detaknadi'] = $this->asesmenMedis['detaknadi'];
        }

        if (isset($this->resumeMedis['pernapasan']) && !empty($this->resumeMedis['pernapasan'])) {
            $subjects['pernapasan'] = $this->resumeMedis['pernapasan'];
        } elseif (isset($this->asesmenMedis['pernapasan']) && !empty($this->asesmenMedis['pernapasan'])) {
            $subjects['pernapasan'] = $this->asesmenMedis['pernapasan'];
        }

        if (isset($this->resumeMedis['systolic']) && !empty($this->resumeMedis['systolic'])) {
            $subjects['systolic'] = $this->resumeMedis['systolic'];
        } elseif (isset($this->asesmenMedis['systolic']) && !empty($this->asesmenMedis['systolic'])) {
            $subjects['systolic'] = $this->asesmenMedis['systolic'];
        }

        if (isset($this->resumeMedis['diastolic']) && !empty($this->resumeMedis['diastolic'])) {
            $subjects['diastolic'] = $this->resumeMedis['diastolic'];
        } elseif (isset($this->asesmenMedis['diastolic']) && !empty($this->asesmenMedis['diastolic'])) {
            $subjects['diastolic'] = $this->asesmenMedis['diastolic'];
        }

        if (isset($this->resumeMedis['suhutubuh']) && !empty($this->resumeMedis['suhutubuh'])) {
            $subjects['suhutubuh'] = $this->resumeMedis['suhutubuh'];
        } elseif (isset($this->asesmenMedis['suhutubuh']) && !empty($this->asesmenMedis['suhutubuh'])) {
            $subjects['suhutubuh'] = $this->asesmenMedis['suhutubuh'];
        }
        return $subjects;
    }
}
