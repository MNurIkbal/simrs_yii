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
use Integrasi\Service\Satusehat\Models\Instalasi;
use Integrasi\Service\Satusehat\Models\Profilrumahsakit;
use Integrasi\Service\Satusehat\Models\Propinsi;
use Integrasi\Service\Satusehat\Models\Kabupaten;
use Integrasi\Service\Satusehat\Models\Kelurahan;
use Integrasi\Service\Satusehat\Models\Kecamatan;
use Integrasi\Service\Satusehat\Models\SatusehatInstalasi;
use Integrasi\Service\Sirs\Cache\Cache;

class SyncUpdateInstalasiSatusehat extends \Integrasi\Service\Satusehat\SyncInstalasiSatusehat
{
	const RESOURCE_TYPE = 'Organization';
	public $logType = 'Organization';
	public $instalasiId;
	public $organizationId;

	public function execute()
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '3600');
		
	    $this->setAttrSatusehat();
	    $instalasiId = isset($this->attributes['result']['id']) ? DocoHelpers::decrypt($this->attributes['result']['id']) : NULL; 
	    $state = $this->state;

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
		    	if(!empty($instalasiId) && $this->attributes['result']['state_input'] == 'update') {
		    	    $instalasi = Instalasi::find()->where(['instalasi_id' => $instalasiId]);
		    	    $dataInstalasi = $instalasi->asArray()->one();

		    	    $payload = $this->buildUpdate($dataInstalasi, $wilayah_rs, $profile_rs);
		    	    $res = (new SatusehatService)->updateOrganization($payload, true);
		    	    $this->setLogs($instalasiId, $state, $payload, $res);
		    	}

		    	return json_encode([
		    	    'service' => 'Sirs-SyncUpdateInstalasiSatusehat',
		    	    'timestamp' => date('Y-m-d H:i:s'),
		    	    'message' => 'Data berhasil di sync'
		    	]);
		    } else {
		    	return json_encode([
		    	    'service' => 'Sirs-SyncUpdateInstalasiSatusehat',
		    	    'timestamp' => date('Y-m-d H:i:s'),
		    	    'message' => 'Data gagal di sync, Kode Wilayah Rumah Sakit tidak ditemukan',
		    	]);
		    }
		    
	    } else {
	    	return json_encode([
	    	    'service' => 'Sirs-SyncUpdateInstalasiSatusehat',
	    	    'timestamp' => date('Y-m-d H:i:s'),
	    	    'message' => 'Data gagal di sync, Profile Rumah Sakit harus di isi dahulu',
	    	]);
	    }
	}

	protected function buildUpdate($data, $wilayah_rs, $profile_rs) {
		$satusehat_instalasi = SatusehatInstalasi::find()
									->where('instalasi_id = '.$data['instalasi_id']. ' 
										AND satusehat_instalasi_id IS NOT NULL
										AND is_active = true
										AND is_deleted = false')
									->asArray()->one();

		$payload = $this->build($data, $wilayah_rs, $profile_rs);
		$payload['id'] = $satusehat_instalasi['satusehat_instalasi_id'];
		$payload['active'] = $data['is_active'];
		$payload['name'] = $data['instalasi_nama'];

		return $payload;
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
	}
}
