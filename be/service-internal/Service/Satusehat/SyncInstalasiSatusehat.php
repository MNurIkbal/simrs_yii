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
use Integrasi\Service\Satusehat\Models\Instalasi;
use Integrasi\Service\Satusehat\Models\Profilrumahsakit;
use Integrasi\Service\Satusehat\Models\Propinsi;
use Integrasi\Service\Satusehat\Models\Kabupaten;
use Integrasi\Service\Satusehat\Models\Kelurahan;
use Integrasi\Service\Satusehat\Models\Kecamatan;
use Integrasi\Service\Satusehat\Models\SatusehatInstalasi;
use Integrasi\Service\Sirs\Cache\Cache;

class SyncInstalasiSatusehat extends \Integrasi\Service\Satusehat\SyncPegawaiSatusehat
{
	const RESOURCE_TYPE = 'Organization';
	public $logType = 'Organization';
	public $defaultProcessLimit = 5;
	public $instalasiId;
	public $organizationId;
	public $unique_str;

	public function execute()
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '3600');

	    $this->setAttrSatusehat();
	    $this->defaultProcessLimit = ArrayHelper::getValue($this->attributes, 'limit_process', 5);
	    $instalasiId = isset($this->attributes['result']['id']) ? DocoHelpers::decrypt($this->attributes['result']['id']) : NULL; 
	    $state = $this->state;

	    if(isset($this->attributes['result']['state_input']) && $this->attributes['result']['state_input'] == 'update') {
	    	return json_encode([
	    	    'service' => 'Sirs-SyncInstalasiSatusehat',
	    	    'timestamp' => date('Y-m-d H:i:s'),
	    	    'message' => 'Data diproses pada bagian sync update instalasi satusehat',
	    	]);
	    }

	    $profile_rs = Profilrumahsakit::find()->asArray()->one();
	    if(!empty($profile_rs['propinsi_id']) && 
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

		    if(!empty($wilayah_rs)) {
		    	// Running handle by create / update ruangan
		    	if(!empty($instalasiId)) {
		    	    $instalasi = Instalasi::find()->where(['instalasi_id' => $instalasiId]);
		    	    $dataInstalasi = $instalasi->asArray()->one();

		    	    $payload = $this->build($dataInstalasi, $wilayah_rs, $profile_rs);
		    	    $res = (new SatusehatService)->createOrganization($payload, true);
		    	    $this->setLogs($instalasiId, $state, $payload, $res);
		    	}

		    	// Running handle by cron job
		    	if(empty($instalasiId)) {
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

		    	    $instalasi = Instalasi::find();
		    	    $dataInstalasi = $instalasi->select([
		    	        'instalasi_m.instalasi_id', 
		    	        'instalasi_m.instalasi_nama',
		    	        'instalasi_m.is_pelayanan',
		    	        'satusehat_instalasi.satusehat_instalasi_id' 
		    	    ]);
		    	    $dataInstalasi = $instalasi->leftJoin('(
		    	    	SELECT 
		    	    		i_satusehat.instalasi_id, 
		    	    		i_satusehat.satusehat_instalasi_id,
		    	    		i_satusehat.satusehat_integration_id
		    	    	FROM 
		    	    		instalasi_satusehat_m i_satusehat 
		    	    	WHERE 
		    	    		i_satusehat.satusehat_instalasi_id IS NOT NULL 
		    	    		AND is_deleted = FALSE
		    	    		AND is_active = TRUE
		    	    ) satusehat_instalasi', 'satusehat_instalasi.instalasi_id = instalasi_m.instalasi_id');
		    	    $dataInstalasi = $instalasi->where('satusehat_instalasi.satusehat_instalasi_id IS NULL AND instalasi_m.is_active = TRUE AND instalasi_m.is_deleted = FALSE AND instalasi_m.is_pelayanan = TRUE');
		    	    $dataInstalasi = $instalasi->orderBy('instalasi_m.instalasi_id ASC')->limit($this->defaultProcessLimit)->asArray()->all();

					$no = 0;
		    	    foreach($dataInstalasi as $key => $value) {
						$no++;
						$instalasi_id = isset($value['instalasi_id']) ? $value['instalasi_id'] : null;
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
		    	        $res = (new SatusehatService)->createOrganization($payload, true);
		    	        $this->setLogs($instalasi_id, $state, $payload, $res);
		    	    }
		    	}
				if ($this->unique_str) {
					Yii::$app->redis->executeCommand('PUBLISH', [
						'channel' => 'syncDataSatusehat:'.$this->unique_str,
						'message' => json_encode([
							'status' => 'finish', 
							'messageProcess' => 'Data Instalasi Berhasil di Sync',
							'progress' => '100'
						]),
					]);
				}

		    	return json_encode([
		    	    'service' => 'Sirs-SyncInstalasiSatusehat',
		    	    'timestamp' => date('Y-m-d H:i:s'),
		    	    'message' => 'Data berhasil di sync',
					'res' => isset($res) ? $res : [],
            		'unique_str' => $this->unique_str,
            		'attr' => $this->attributes,
		    	]);
		    } else {
		    	return json_encode([
		    	    'service' => 'Sirs-SyncInstalasiSatusehat',
		    	    'timestamp' => date('Y-m-d H:i:s'),
		    	    'message' => 'Data gagal di sync, Kode Wilayah Rumah Sakit tidak ditemukan',
		    	]);
		    }
		    
	    } else {
	    	return json_encode([
	    	    'service' => 'Sirs-SyncInstalasiSatusehat',
	    	    'timestamp' => date('Y-m-d H:i:s'),
	    	    'message' => 'Data gagal di sync, Profile Rumah Sakit harus di isi dahulu',
	    	]);
	    }
	}

	
	protected function build($data, $wilayah_rs = NULL, $profile_rs = NULL) {
		$kode_provinsi = trim($wilayah_rs['kode_propinsi']);
		$kode_kabupaten = $kode_provinsi.trim($wilayah_rs['kode_kabupaten']);
		$kode_kecamatan = $kode_kabupaten.trim($wilayah_rs['kode_kecamatan']);
		$kode_kelurahan = $kode_kecamatan.trim($wilayah_rs['kode_kelurahan']);

		$email_rs = !empty($profile_rs['email']) ? $profile_rs['email'] : '-';

		$arrayVar = [
		    "resourceType" => $this->logType,
		    "active" => true,
		    "identifier" => [
		        [
		            "use" => "official",
		            "system" => "http://sys-ids.kemkes.go.id/organization/" . $this->organizationId,
		            "value" => $data['instalasi_id'].'-'.strtolower(str_replace(' ', '-', $data['instalasi_nama'])),
		        ],
		    ],
		    "type" => [
		        [
		            "coding" => [
		                [
		                    "system" =>
		                        "http://terminology.hl7.org/CodeSystem/organization-type",
		                    "code" => "dept",
		                    "display" => "Hospital Department",
		                ],
		            ],
		        ],
		    ],
		    "name" => $data['instalasi_nama'],
		    "telecom" => [
		    	["system" => "phone", "value" => $profile_rs['no_telp_profilrs'], "use" => "work"],
		    	["system" => "email", "value" => $email_rs, "use" => "work"],
		    	[
		    	    "system" => "url",
		    	    "value" => $profile_rs['website'],
		    	    "use" => "work",
		    	],
		    ],
		    "address" => [
		        [
		            "use" => "work",
		            "type" => "both",
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
		                    ],
		                ],
		            ],
		        ],
		    ],
		    "partOf" => ["reference" => "Organization/" . $this->organizationId],
		];

	    return $arrayVar;
	}
	

	protected function setLogs($instalasiId, $state, $payload, $result, $additionalId = null)
	{
	    $userIdentity = $this->user_identity;
	    $uidSercon = isset($result['uid']) ? $result['uid'] : null;
	    $result_sercon = isset($result['response']) ? (is_array($result['response']) ? $result['response'] : json_decode($result['response'], true)) : [];
	    $organization_id = isset($result_sercon['id']) ? $result_sercon['id'] : NULL;

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
	        'type' => $this->logType,
	        'state' => $state,
	        'id_sync_sercon' => $uidSercon,
	        'created_date' => date('Y-m-d H:i:s'),
	        'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
	        'payload' => json_encode($payload),
	        'is_deleted' => false,
	        'is_active' => true,
	        'additional_id' => $instalasiId,
	        'satusehat_id' => $organization_id,
	        'sync_response' => $sync_response
	    ];

	    $satusehat = $this->saveLogs($logData);

	    $data_instalasi = [
	        'instalasi_id' => $instalasiId,
	        'satusehat_integration_id' => $satusehat['id'],
	        'satusehat_instalasi_id' => $organization_id
	    ];

	    $this->saveSatusehatInstalasi($data_instalasi);
	}

	protected function saveSatusehatInstalasi($data) 
	{
	    $instalasiSatusehat = new SatusehatInstalasi;
	    $instalasiSatusehat->instalasi_id = $data['instalasi_id'];
	    $instalasiSatusehat->satusehat_integration_id = $data['satusehat_integration_id'];
	    $instalasiSatusehat->satusehat_instalasi_id = $data['satusehat_instalasi_id'];
	    $instalasiSatusehat->save();

	    return $instalasiSatusehat;
	}
}
