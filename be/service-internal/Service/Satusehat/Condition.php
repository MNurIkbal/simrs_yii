<?php

namespace Integrasi\Service\Satusehat;

use Doco\components\DocoConstansId;
use Integrasi\Components\DocoConstants;
use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Satusehat\Models\SatusehatPasien;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Satusehat\Models\InfoKonsulPoliView;
use Integrasi\Service\Satusehat\Models\LaporanKunjunganRjView;
use Integrasi\Service\Satusehat\Models\SatusehatInstalasi;
use Integrasi\Service\Satusehat\Models\SatusehatPegawai;
use Integrasi\Service\Satusehat\Models\SatusehatRuangan;

class Condition extends \Integrasi\Contracts\DocoImplement
{
    const RESOURCE_TYPE = 'Condition';
    protected $logType = 'Condition';
    public $pendaftaranId;
    public $konsulpoliId;
    public $organizationId;
    public $dataAttr;
    public $encounterDataNow;
    
    public $lap_kunjungan_data = NULL;
    public $satusehat_pasien_id = NULL;
    public $satusehat_pegawai_id = NULL;
    public $satusehat_instalasi_id = NULL;
    public $satusehat_ruangan_id = NULL;

    public function execute()
    {
        $this->setAttrSatusehat();
        $state = $this->state;
        $this->dataAttr = $this->attributes['result']['data'];
        $dataAttr = $this->dataAttr;
        $pendaftaranId = $this->dataAttr['result']['pendaftaran_id'];
        $this->konsulpoliId = (int)$this->dataAttr['konsulpoli_id'];
        if($pendaftaranId) {
            $this->masterPayloadUpdate($pendaftaranId);

            $payload = $this->build();
            $res = (new SatusehatService)->createCondition($payload, true);
            $this->setLogs($pendaftaranId, $state, $payload, $res);
        }
        
        return json_encode([
            'service' => 'Satusehat-condition',
            'state' => $state,
            'test' => '$test',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    public function setLogs($pendaftaranId, $state, $payload, $result, $additionalId = null)
    {
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;
        $result_sercon = isset($result['response']) ? (is_array($result['response']) ? $result['response'] : json_decode($result['response'], true)) : [];
        $condition_id = isset($result_sercon['id']) ? $result_sercon['id'] : NULL;

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
            'satusehat_id' => $condition_id,
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
        $dataBuild = $this->dataAttr;
        $codeCoding = $displayCoding = '';
        if(!empty($dataBuild['a_diag_utama'])){
            $codeCoding = !empty($dataBuild['a_diag_utama']['kode']) ? $dataBuild['a_diag_utama']['kode'] : '';
            $displayCoding = !empty($dataBuild['a_diag_utama']['nama']) ? $dataBuild['a_diag_utama']['nama'] : '';
            
        };
        if( !empty($dataBuild['result']['pasien_id']) ) {
            $satusehatPasien = SatusehatPasien::find()
                    ->select(['id', 'pasien_id', 'satusehat_pasien_id'])
                    ->where('pasien_id = '.$dataBuild['result']['pasien_id']. ' and is_active = true and is_deleted = false')
                    ->asArray()->one();

            $subjectReference = !empty($satusehatPasien['satusehat_pasien_id']) ? $satusehatPasien['satusehat_pasien_id'] : '';
        };
        if( !empty($dataBuild['result']['pendaftaran_id']) ) {
            $satusehat_encounter = Satusehat::find()
                ->select([
                    'id',
                    'pendaftaran_id',
                    'payload',
                    'satusehat_id',
                    'type'
                ]);

            if (!empty($this->konsulpoliId)) {
                $encounterData = $satusehat_encounter->where("type = 'Encounter'
                    and payload LIKE '%". $this->satusehat_pasien_id . "%'
                    and payload LIKE '%". $this->satusehat_pegawai_id . "%'
                    and payload LIKE '%". $this->satusehat_ruangan_id ."%'
                    and pendaftaran_id = " . $this->lap_kunjungan_data['pendaftaran_id'] . "
                    and is_active = true 
                    and is_deleted = false")
                    ->orderBy(['id' => SORT_DESC])
                    ->asArray()->one();
            } else {
                $encounterData = $satusehat_encounter->where("type = 'Encounter' 
                    and pendaftaran_id = " . $this->lap_kunjungan_data['pendaftaran_id'] . "
                    and is_active = true 
                    and is_deleted = false")
                    ->asArray()->one();
            }

            $referenceEncounter = !empty($encounterData['satusehat_id']) ? $encounterData['satusehat_id'] : NULL;

            // $satusehatData = Satusehat::find()
            //         ->select(['id', 'pendaftaran_id', 'satusehat_id', 'type'])
            //         ->where('pendaftaran_id = '.$dataBuild['result']['pendaftaran_id']. ' and type = \'Encounter\' and is_active = true and is_deleted = false')
            //         ->orderBy(['id' => SORT_DESC])
            //         ->asArray()->one();

            // $referenceEncounter = !empty($satusehatData['satusehat_id']) ? $satusehatData['satusehat_id'] : '';
        };
        $namaPasien = !empty($dataBuild['nama_pasien']) ? $dataBuild['nama_pasien'] : '';
        $dateTime = !empty($dataBuild['result']['tgl_soaprj']) ? date("Y-m-d", strtotime($dataBuild['result']['tgl_soaprj'])).'T'.date('H:i:s', strtotime($dataBuild['result']['tgl_soaprj'])).$gmtTime : date("Y-m-d").'T'.date('H:i:s', strtotime($dataBuild['result']['tgl_soaprj'])).$gmtTime;

        $res = [
            "resourceType" => self::RESOURCE_TYPE,
            "clinicalStatus" => [
                "coding" => [
                    [
                        "system" =>
                            "http://terminology.hl7.org/CodeSystem/condition-clinical",
                        "code" => "active",
                        "display" => "Active",
                    ],
                ],
            ],
            "category" => [
                [
                    "coding" => [
                        [
                            "system" =>
                                "http://terminology.hl7.org/CodeSystem/condition-category",
                            "code" => "encounter-diagnosis",
                            "display" => "Encounter Diagnosis",
                        ],
                    ],
                ],
            ],
            "code" => [
                "coding" => [
                    [
                        "system" => "http://hl7.org/fhir/sid/icd-10",
                        "code" => trim($codeCoding),
                        "display" => $displayCoding,
                    ],
                ],
            ],
            "subject" => [
                "reference" => "Patient/" . $subjectReference,
                "display" => $namaPasien,
            ],
            "encounter" => [
                "reference" => "Encounter/" . $referenceEncounter,
            ],
            "onsetDateTime" => $dateTime,
            "recordedDate" => $dateTime,
        ];

        return $res;
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
                'tgl_pendaftaran'
            ])
            ->where('pendaftaran_id = '.$pendaftaran_id)
            ->andWhere('konsulpoli_id = '.$this->konsulpoliId)
            ->andWhere('status_konsul_id = '. DocoConstants::STATUS_KONSUL_BLM_DIJAWAB)
            ->asArray()->one();
        } else {
            $lap_kunjungan = LaporanKunjunganRjView::find()->select([
                'pendaftaran_id',
                'no_pendaftaran',
                'pasien_id',
                'nama_pasien',
                'pegawai_id',
                'nama_pegawai AS dokter_nama',
                'instalasi_id',
                'instalasi_nama',
                'ruangan_id',
                'ruangan_nama',
                'tgl_pendaftaran',
                'tgl_antrian',
                'tglpasienpulang'
            ])
            ->where('pendaftaran_id = '.$pendaftaran_id)
            ->andWhere('konsulpoli_id IS NULL')
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
}