<?php

namespace Integrasi\Service\Satusehat;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\SatusehatPasienView;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Satusehat\Models\SatusehatPasien;
use Integrasi\Components\DocoHelpers;

use Integrasi\Service\Sirs\Cache\Cache;

class SyncPasienSatusehat extends \Integrasi\Contracts\DocoImplement
{
	const RESOURCE_TYPE = 'Patient';

	public $logType = 'Patient';
	public $organizationId;
	public $defaultProcessLimit = 5;
	protected $data_satu_sehat;
	public $unique_str;

	public function execute()
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '3600');

		$pasien_id = null;
		$customMessage = $res = null;

	    $this->setAttrSatusehat();
	    $this->defaultProcessLimit = ArrayHelper::getValue($this->attributes, 'limit_process', 5);

	    if (isset($this->attributes['result']['data_pasien']) ) {
			// create pasien - rekam medik
	        $pasien_id = isset($this->attributes['result']['data_pasien']['pasien']['pasien_id']) ? $this->attributes['result']['data_pasien']['pasien']['pasien_id'] : null;
	    }

	    if (isset($this->attributes['result']['pendaftaran']) ) {
	        // from pendaftaran
            if (isset($this->attributes['result']['pendaftaran']['status_pasien']) && 
                $this->attributes['result']['pendaftaran']['status_pasien'] == 'Pasien Baru') {
	            $pasien_id = isset($this->attributes['result']['pendaftaran']['pasien_id']) ? $this->attributes['result']['pendaftaran']['pasien_id'] : null;
	        }
	    }

	    // Running handle by create / update pasien
	    if (!empty($pasien_id)) {
	        $pasien = SatusehatPasienView::find()->select(['*'])
            ->where("pasien_id = ". $pasien_id ." AND jenis_identitas <> 'null' AND jenis_identitas <> '' AND nomor_id_pasien <> 'null' AND nomor_id_pasien <> '' AND satusehat_pasien_id IS NULL");
	        $dataPasien = $pasien->asArray()->one();

	        if (ArrayHelper::getValue($dataPasien, 'nomor_id_pasien')) {

						if ($dataPasien['jenis_identitas'] == DocoConstants::IDENTITAS_KTP) {
								$payload = $this->build($dataPasien);
								$res = (new SatusehatService)->createPatient($payload, true);
						} else {
								$payload = $this->build($dataPasien);
								$res = [
										'error' => json_encode([
												'message' => 'Jenis identitas pasien bukan NIK'
										])
								];
						}
						
						$this->setLogs($pasien_id, $payload, $res);
	        }

			$customMessage = empty(ArrayHelper::getValue($dataPasien, 'nomor_id_pasien')) ? "Tidak dapat diproses, identitas pasien tidak ditemukan / tidak valid" : null;
	    }

	    // Running handle by cron job
	    if (empty($pasien_id) && !in_array($this->state, ['new-admission'])) {
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

			$pasien = SatusehatPasienView::find()->select(['*'])
			->where("jenis_identitas <> 'null' AND jenis_identitas <> '' AND nomor_id_pasien <> 'null' AND nomor_id_pasien <> '' AND jenis_identitas = '".DocoConstants::IDENTITAS_KTP."' AND CHAR_LENGTH ( nomor_id_pasien ) = '16' AND (satusehat_pasien_id IS NULL OR satusehat_pasien_id = '--')")
			->orderBy('no_rekam_medik DESC')
			->limit($this->defaultProcessLimit);
			$dataPasien = $pasien->asArray()->all();

			$no = 0;
	        foreach($dataPasien as $key => $value) {
				$no++;
				$pasien_id = isset($value['pasien_id']) ? $value['pasien_id'] : null;
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
	            $payload = $this->build($value);
	            $res = (new SatusehatService)->createPatient($payload, true);
	            $this->setLogs($pasien_id, $payload, $res);
	        }
			if ($this->unique_str) {
				Yii::$app->redis->executeCommand('PUBLISH', [
					'channel' => 'syncDataSatusehat:'.$this->unique_str,
					'message' => json_encode([
						'status' => 'finish', 
						'messageProcess' => 'Data Pasien Berhasil di Sync',
						'progress' => '100'
					]),
				]);
			}
	    }

	    return json_encode([
	        'service' => 'Sirs-SyncPasienSatusehat',
	        'timestamp' => date('Y-m-d H:i:s'),
	        'message' => !empty($customMessage) ? $customMessage : 'Data berhasil di sync',
            'attributes' => $this->attributes,
			'res' => $res,
			'unique_str' => $this->unique_str,
	    ]);
	}

	protected function build($data) {
		return [
			'pasien_nik' => ArrayHelper::getValue($data, 'nomor_id_pasien'),
		];
	}

	/** 
	 * set log transaksi satu sehat dan satu sehat pasien
	 */
	protected function setLogs($patientId, $payload, $result)
	{
		$pendaftaranId = isset($this->attributes['result']['id']) ? DocoHelpers::decrypt($this->attributes['result']['id']) : null;
	    $userIdentity = $this->user_identity;
	    $uidSercon = ArrayHelper::getValue($result, 'uid');
	    $result_sercon = isset($result['response']) ? (is_array($result['response']) ? $result['response'] : json_decode($result['response'], true)) : [];
	    $satusehat_patient_id = isset($result_sercon['entry'][0]['resource']['id']) ? $result_sercon['entry'][0]['resource']['id'] : '--';
	    $error = !empty($result['error']) ? $result['error'] : NULL;

	    if(!empty($error)) {
	        $sync_response = $error;
	    } else { 
	        $sync_response = isset($result['response']) ? (is_array($result['response']) ? json_encode($result['response']) : $result['response']) : json_encode($result);
	    }

	    $logData = [
	        'is_sent' => !empty($error) ? false : true,
			'pendaftaran_id' => $pendaftaranId,
	        'type' => $this->logType,
	        'state' => $this->state,
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
	    $this->saveLogs($logData);
		
		$data_pasien = [
	        'pasien_id' => $patientId,
	        'satusehat_integration_id' => ArrayHelper::getValue($this->data_satu_sehat, 'id'),
	        'satusehat_pasien_id' => ArrayHelper::getValue($this->data_satu_sehat, 'satusehat_id')
	    ];
	    $this->saveSatusehatPasien($data_pasien);
	}

	public function setAttrSatusehat()
    {
        $model = Cache::getLookupByType('satusehat');
		foreach ($model as $value) {
			if (ArrayHelper::getValue($value, 'lookup_name') == 'organization_id') {
				$this->organizationId = ArrayHelper::getValue($value,'lookup_value');
			}
		}
    }

    protected function saveLogs($data)
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

		$this->data_satu_sehat = $insertSatusehat;
    }

	protected function saveSatusehatPasien($data) 
	{
	    $pasienSatusehat = new SatusehatPasien;
	    $pasienSatusehat->pasien_id = ArrayHelper::getValue($data, 'pasien_id');
	    $pasienSatusehat->satusehat_integration_id = ArrayHelper::getValue($data, 'satusehat_integration_id');
	    $pasienSatusehat->satusehat_pasien_id = ArrayHelper::getValue($data, 'satusehat_pasien_id');
	    $pasienSatusehat->save();
	    return $pasienSatusehat;
	}
}
