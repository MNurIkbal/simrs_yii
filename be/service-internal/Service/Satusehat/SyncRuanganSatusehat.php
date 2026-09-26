<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use DateInterval;
use DateTime;
use Integrasi\Service\Sirs\Models\JadwalDokter;
use Integrasi\Service\Sirs\Models\SlotJadwalDokter;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use yii\db\Expression;

use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Satusehat\Models\Ruangan;
use Integrasi\Service\Satusehat\Models\Profilrumahsakit;
use Integrasi\Service\Satusehat\Models\Propinsi;
use Integrasi\Service\Satusehat\Models\Kabupaten;
use Integrasi\Service\Satusehat\Models\Kelurahan;
use Integrasi\Service\Satusehat\Models\Kecamatan;
use Integrasi\Service\Satusehat\Models\SatusehatRuangan;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Satusehat\Models\JenisAntrianDetail;
use Integrasi\Service\Satusehat\Models\JenisAntrianRuanganMp;

class SyncRuanganSatusehat extends \Integrasi\Service\Satusehat\SyncPegawaiSatusehat
{
    const RESOURCE_TYPE = 'Location';
    public $logType = 'Location';
    public $defaultProcessLimit = 5;
    public $ruanganId;
    public $organizationId;
    public $unique_str;

    public function execute()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '3600');

        $this->setAttrSatusehat();
        $this->defaultProcessLimit = ArrayHelper::getValue($this->attributes, 'limit_process', 5);
        $ruanganId = isset($this->attributes['result']['id']) ? DocoHelpers::decrypt($this->attributes['result']['id']) : NULL;
        $state = $this->state;

        if (isset($this->attributes['result']['state_input']) && $this->attributes['result']['state_input'] == 'update') {
            return json_encode([
                'service' => 'Sirs-SyncRuanganSatusehat',
                'timestamp' => date('Y-m-d H:i:s'),
                'message' => 'Data diproses pada bagian sync update ruangan satusehat',
            ]);
        }

        $profile_rs = Profilrumahsakit::find()->asArray()->one();
        if (
            !empty($profile_rs['propinsi_id']) &&
            !empty($profile_rs['kabupaten_id']) &&
            !empty($profile_rs['kecamatan_id']) &&
            !empty($profile_rs['kelurahan_id'])
        ) {
            $wilayah_rs = Propinsi::find()
                ->select([
                    'propinsi_m.propinsi_id',
                    'propinsi_m.propinsi_nama',
                    'propinsi_m.kode_propinsi',
                    'kabupaten_m.kabupaten_id',
                    'kabupaten_m.kabupaten_nama',
                    'kabupaten_m.kode_kabupaten',
                    'kecamatan_m.kecamatan_id',
                    'kecamatan_m.kecamatan_nama',
                    'kecamatan_m.kode_kecamatan',
                    'kelurahan_m.kelurahan_id',
                    'kelurahan_m.kelurahan_nama',
                    'kelurahan_m.kode_kelurahan'
                ])
                ->join('JOIN', 'kabupaten_m', 'propinsi_m.propinsi_id = kabupaten_m.propinsi_id')
                ->join('JOIN', 'kecamatan_m', 'kecamatan_m.kabupaten_id = kabupaten_m.kabupaten_id')
                ->join('JOIN', 'kelurahan_m', 'kelurahan_m.kecamatan_id = kecamatan_m.kecamatan_id')
                ->andWhere(['propinsi_m.propinsi_id' => $profile_rs['propinsi_id']])
                ->andWhere(['kabupaten_m.kabupaten_id' => $profile_rs['kabupaten_id']])
                ->andWhere(['kecamatan_m.kecamatan_id' => $profile_rs['kecamatan_id']])
                ->andWhere(['kelurahan_m.kelurahan_id' => $profile_rs['kelurahan_id']])
                ->asArray()->one();

            if (!empty($wilayah_rs)) {
                // Running handle by create / update ruangan
                if (!empty($ruanganId)) {
                    $ruangan = Ruangan::find();
                    $dataRuangan = $ruangan->select([
                        'ruangan_m.ruangan_id',
                        'ruangan_m.instalasi_id',
                        'ruangan_m.ruangan_nama',
                        'instalasi_m.instalasi_id',
                        'instalasi_m.instalasi_nama',
                        'instalasi_m.is_pelayanan',
                        'instalasi_m.satusehat_instalasi_id'
                    ]);

                    $dataRuangan = $ruangan->join('LEFT JOIN', '(
	    	        	SELECT
	    	        		a.instalasi_id,
	    	        		a.instalasi_nama,
	    	        		a.is_pelayanan,
	    	        		b.satusehat_instalasi_id
	    	        	FROM 
	    	        		instalasi_m a
	    	        	JOIN (
	    	        		SELECT
	    	        			ism.instalasi_id,
	    	        			ism.satusehat_instalasi_id
	    	        		FROM
	    	        			instalasi_satusehat_m ism
	    	        		WHERE 
	    	        			ism.satusehat_instalasi_id IS NOT NULL
	    	        			AND ism.is_active = true
	    	        			AND ism.is_deleted = false
	    	        	) b ON b.instalasi_id = a.instalasi_id
	    	        	WHERE
	    	        		a.is_active = TRUE
	    	        		AND a.is_deleted = FALSE
	    	    	) instalasi_m', 'ruangan_m.instalasi_id = instalasi_m.instalasi_id');

                    $dataRuangan = $ruangan->where(['ruangan_m.ruangan_id' => $ruanganId]);
                    $dataRuangan = $ruangan->asArray()->one();
                    
                    $antrian_lantai = JenisAntrianRuanganMp::find()->where(['ruangan_id' => $ruanganId])->one();
                    $lantai_nama = null;
                    if ($antrian_lantai) {
                        $lantai_detail = JenisAntrianDetail::find()->Where(['jenisantriandetail_id' => $antrian_lantai->jenisantriandetail_id])->one();
                        $lantai_nama = $lantai_detail->nama;
                    }

                    $payload = $this->build($dataRuangan, $wilayah_rs, $profile_rs, $lantai_nama);
                    $res = (new SatusehatService)->createLocation($payload, true);
                    $this->setLogs($ruanganId, $state, $payload, $res);
                }

                // Running handle by cron job
                if (empty($ruanganId)) {
                    $this->unique_str = isset($this->attributes['result']['unique_str']) ? $this->attributes['result']['unique_str'] : NULL;
                    $this->defaultProcessLimit = isset($this->attributes['result']['defaultProcessLimit']) ? $this->attributes['result']['defaultProcessLimit'] : $this->defaultProcessLimit;

                    /** di gunakan hanya untuk sinkronisasi app / bukan dari postman */
                    if ($this->unique_str) {
                        Yii::$app->redis->executeCommand('PUBLISH', [
                            'channel' => 'syncDataSatusehat:'.$this->unique_str,
                            'message' => json_encode([
                                'unique_process' => $this->unique_str,
                                'messageProcess' => 'menyiapkan data.',
                                'progress' => '....'
                            ]),
                        ]);
                    }

                    $ruangan = Ruangan::find();
                    $dataRuangan = $ruangan->select([
                        'ruangan_m.ruangan_id',
                        'ruangan_m.instalasi_id',
                        'ruangan_m.ruangan_nama',
                        'instalasi_m.instalasi_id',
                        'instalasi_m.instalasi_nama',
                        'instalasi_m.is_pelayanan',
                        'instalasi_m.satusehat_instalasi_id',
                        'satusehat_ruangan.satusehat_ruangan_id'
                    ]);
                    $dataRuangan = $ruangan->join('JOIN', '(
		    	    	SELECT
		    	    		a.instalasi_id,
		    	    		a.instalasi_nama,
		    	    		a.is_pelayanan,
		    	    		b.satusehat_instalasi_id
		    	    	FROM 
		    	    		instalasi_m a
		    	    	JOIN (
		    	    		SELECT
		    	    			ism.instalasi_id,
		    	    			ism.satusehat_instalasi_id
		    	    		FROM
		    	    			instalasi_satusehat_m ism
		    	    		WHERE 
		    	    			ism.satusehat_instalasi_id IS NOT NULL
		    	    			AND ism.is_active = true
		    	    			AND ism.is_deleted = false
		    	    	) b ON b.instalasi_id = a.instalasi_id
		    	    	WHERE
		    	    		a.is_pelayanan = TRUE
		    	    		AND a.is_active = TRUE
		    	    		AND a.is_deleted = FALSE
		    		) instalasi_m', 'ruangan_m.instalasi_id = instalasi_m.instalasi_id');
                    $dataRuangan = $ruangan->leftJoin('(
		    	    	SELECT 
		    	    		r_satusehat.ruangan_id, 
		    	    		r_satusehat.satusehat_ruangan_id,
		    	    		r_satusehat.satusehat_integration_id
		    	    	FROM 
		    	    		ruangan_satusehat_m r_satusehat 
		    	    	WHERE 
		    	    		r_satusehat.satusehat_ruangan_id IS NOT NULL 
		    	    		AND is_deleted = FALSE
		    	    		AND is_active = TRUE
		    	    ) satusehat_ruangan', 'satusehat_ruangan.ruangan_id = ruangan_m.ruangan_id');
                    $dataRuangan = $ruangan->where('satusehat_ruangan.satusehat_ruangan_id IS NULL AND ruangan_m.is_active = TRUE AND ruangan_m.is_deleted = FALSE');
                    $dataRuangan = $ruangan->orderBy('ruangan_m.ruangan_id ASC')->limit($this->defaultProcessLimit)->asArray()->all();

                    $no = 0;
                    foreach ($dataRuangan as $key => $value) {
                        $no++;
                        $ruangan_id = isset($value['ruangan_id']) ? $value['ruangan_id'] : null;
                        if ($this->unique_str) {
                            Yii::$app->redis->executeCommand('PUBLISH', [
                                'channel' => 'syncDataSatusehat:'.$this->unique_str,
                                'message' => json_encode([
                                    'status' => 'finish', 
                                    'messageProcess' => 'Memproses Data ke Satu Sehat ke-'. $no,
                                    'progress' => 80
                                ]),
                            ]);
                        }
                        $payload = $this->build($value, $wilayah_rs, $profile_rs);
                        $res = (new SatusehatService)->createLocation($payload, true);
                        $this->setLogs($ruangan_id, $state, $payload, $res);
                    }

                    if ($this->unique_str) {
                        Yii::$app->redis->executeCommand('PUBLISH', [
                            'channel' => 'syncDataSatusehat:'.$this->unique_str,
                            'message' => json_encode([
                                'status' => 'finish', 
                                'messageProcess' => 'Data Ruangan Berhasil di Sync',
                                'progress' => '100'
                            ]),
                        ]);
                    }
                }

                return json_encode([
                    'service' => 'Sirs-SyncRuanganSatusehat',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'message' => 'Data berhasil di sync',
                    'res' => isset($res) ? $res : [],
            		'unique_str' => $this->unique_str,
            		'attr' => $this->attributes,
                ]);
            } else {
                return json_encode([
                    'service' => 'Sirs-SyncRuanganSatusehat',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'message' => 'Data gagal di sync, Kode Wilayah Rumah Sakit tidak ditemukan',
                ]);
            }
        } else {
            return json_encode([
                'service' => 'Sirs-SyncRuanganSatusehat',
                'timestamp' => date('Y-m-d H:i:s'),
                'message' => 'Data gagal di sync, Profile Rumah Sakit harus di isi dahulu',
            ]);
        }
    }

    protected function build($data, $wilayah_rs = NULL, $profile_rs = NULL, $lantai_nama = NULL)
    {
        $kode_provinsi = trim($wilayah_rs['kode_propinsi']);
        $kode_kabupaten = $kode_provinsi . trim($wilayah_rs['kode_kabupaten']);
        $kode_kecamatan = $kode_kabupaten . trim($wilayah_rs['kode_kecamatan']);
        $kode_kelurahan = $kode_kecamatan . trim($wilayah_rs['kode_kelurahan']);

        $instalasi_satusehat_id = isset($data['satusehat_instalasi_id']) ? $data['satusehat_instalasi_id'] : "";
        $email_rs = !empty($profile_rs['email']) ? $profile_rs['email'] : '-';

        $arrayVar = [
            "resourceType" => self::RESOURCE_TYPE,
            "identifier" => [
                [
                    "system" => "http://sys-ids.kemkes.go.id/location/" . $this->organizationId,
                    "value" => $data['ruangan_id'] . '-' . strtolower(str_replace(' ', '-', $data['ruangan_nama'])),
                ],
            ],
            "status" => "active",
            "name" => $data['ruangan_nama'],
            "description" => $data['ruangan_nama'] . ($lantai_nama ? ', ' . $lantai_nama : ''),
            "mode" => "instance",
            "telecom" => [
                ["system" => "phone", "value" => $profile_rs['no_telp_profilrs'], "use" => "work"],
                ["system" => "fax", "value" => $profile_rs['no_faksimili'], "use" => "work"],
                ["system" => "email", "value" => $email_rs],
                [
                    "system" => "url",
                    "value" => $profile_rs['website'],
                    "use" => "work",
                ],
            ],
            "address" => [
                "use" => "work",
                "line" => [
                    $profile_rs['alamatlokasi_rumahsakit'],
                ],
                "city" => $wilayah_rs['kabupaten_nama'],
                "postalCode" => $profile_rs['kode_pos'],
                "country" => "ID",
                "extension" => [
                    [
                        "url" =>
                        "https://fhir.kemkes.go.id/r4/StructureDefinition/administrativeCode",
                        "extension" => [
                            ["url" => "province", "valueCode" => $kode_provinsi],
                            ["url" => "city", "valueCode" => $kode_kabupaten],
                            ["url" => "district", "valueCode" => $kode_kecamatan],
                            ["url" => "village", "valueCode" => $kode_kelurahan],
                            ["url" => "rt", "valueCode" => "-"],
                            ["url" => "rw", "valueCode" => "-"],
                        ],
                    ],
                ],
            ],
            "physicalType" => [
                "coding" => [
                    [
                        "system" =>
                        "http://terminology.hl7.org/CodeSystem/location-physical-type",
                        "code" => "ro",
                        "display" => "Room",
                    ],
                ],
            ],
            "position" => [
                "longitude" => (float) $profile_rs['longitude'],
                "latitude" => (float) $profile_rs['latitude'],
                "altitude" => 0,
            ],
            "managingOrganization" => ["reference" => "Organization/" . $instalasi_satusehat_id],
        ];

        return $arrayVar;
    }

    protected function setLogs($ruanganId, $state, $payload, $result, $additionalId = null)
    {
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;
        $result_sercon = isset($result['response']) ? (is_array($result['response']) ? $result['response'] : json_decode($result['response'], true)) : [];
        $location_id = isset($result_sercon['id']) ? $result_sercon['id'] : NULL;

        $error = !empty($result['error']) ? $result['error'] : NULL;

        if (!empty($error)) {
            $sync_response = $error;
        } else {
            $sync_response = isset($result['response']) ? (
                is_array($result['response']) ? json_encode($result['response']) : $result['response']
            ) : json_encode($result);
        }

        $logData = [
            'is_sent' => !empty($error) ? false : true,
            'type' => $this->logType,
            'state' => $state,
            'id_sync_sercon' => $uidSercon,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
            'payload' => json_encode($payload),
            'is_deleted' => false,
            'is_active' => true,
            'additional_id' => $ruanganId,
            'satusehat_id' => $location_id,
            'sync_response' => $sync_response
        ];

        $satusehat = $this->saveLogs($logData);

        $data_ruangan = [
            'ruangan_id' => $ruanganId,
            'satusehat_integration_id' => $satusehat['id'],
            'satusehat_ruangan_id' => $location_id
        ];

        $this->saveSatusehatRuangan($data_ruangan);
    }

    protected function saveSatusehatRuangan($data)
    {
        $ruanganSatusehat = new SatusehatRuangan;
        $ruanganSatusehat->ruangan_id = $data['ruangan_id'];
        $ruanganSatusehat->satusehat_integration_id = $data['satusehat_integration_id'];
        $ruanganSatusehat->satusehat_ruangan_id = $data['satusehat_ruangan_id'];
        $ruanganSatusehat->save();

        return $ruanganSatusehat;
    }
}
