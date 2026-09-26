<?php 

namespace Integrasi\Service\Satusehat;

use Doco\components\DocoConstansId;
use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Satusehat\Models\Satusehat;
use Integrasi\Service\Satusehat\Models\SoapRj;
use Integrasi\Components\Services\SatusehatService;
use Integrasi\Service\Satusehat\Models\InfoDataKunjunganView;
use Integrasi\Service\Satusehat\Models\InfoKonsulPoliView;
use Integrasi\Service\Satusehat\Models\LaporanKunjunganRjView;

class EncounterFinish extends \Integrasi\Service\Satusehat\Encounter {

	protected $logType = 'EncounterFinish';
	
	protected $data_diagnosa;

	public $konsulpoliId;

    public function execute()
    {
    	$this->setAttrSatusehat();
        $state = $this->state;
        $pendaftaranId = isset($this->attributes['result']['pendaftaran_id']) ? $this->attributes['result']['pendaftaran_id'] : NULL;
		$konsulpoliId = isset($this->attributes['result']['konsulpoli_id']) ? $this->attributes['result']['konsulpoli_id'] : NULL;
        $multiplePendaftaranId = isset($this->attributes['result']['multiple_pendaftaran']) ? $this->attributes['result']['multiple_pendaftaran'] : NULL;

        $this->konsulpoliId = $konsulpoliId;

		$this->masterPayload($pendaftaranId, $konsulpoliId);
				
        if ($pendaftaranId) {
					$this->masterPayload($pendaftaranId, $konsulpoliId);
					$payload = $this->buildEncounterFinish();

					$res = (new SatusehatService)->updateEncounter($payload, true);
					$this->setLogs($pendaftaranId, $state, $payload, $res);
        }

        if (empty($pendaftaranId) && !empty($multiplePendaftaranId) ) {
        	for ($i=0; $i < count($multiplePendaftaranId); $i++) { 
        		$this->masterPayload($multiplePendaftaranId[$i]);

						$payload = $this->buildEncounterFinish();
						$res = (new SatusehatService)->updateEncounter($payload, true);
						$this->setLogs($multiplePendaftaranId[$i], $state, $payload, $res);
        	}
        }

        return json_encode([
					'service' => 'Satusehat-EncounterFinish',
					'state' => $state,
					'timestamp' => date('Y-m-d H:i:s'),
					'payload' => isset($payload) ? $payload : null,
					'data_lap' => $this->lap_kunjungan_data,
					'data_diagnosa' => $this->data_diagnosa
        ]);
    }

    public function buildEncounterFinish() {
		$connection = !empty(Yii::$app->dbslave->username) ? Yii::$app->dbslave : Yii::$app->db;
		$gmtTime = (new DocoConstansId)->actionGetAdditional('gmt_time');
    	$satusehat = Satusehat::find()->select(['id', 'pendaftaran_id','satusehat_id','type']);

		if (!empty($this->konsulpoliId)) {
			$sql = "SELECT *
				FROM satusehat_integrasi_t
				WHERE 
					type = :type
					AND pendaftaran_id = :pendaftaran_id
					AND is_active = TRUE
					AND is_deleted = FALSE
				ORDER BY id DESC
				LIMIT 1";

			$params = [
				':type' => self::RESOURCE_TYPE,
				':pendaftaran_id' => $this->lap_kunjungan_data['pendaftaran_id']
			];
			$command = $connection->createCommand($sql, $params);
			$encounter = $command->queryOne();
			if ($encounter) {
				$encounterList = array($encounter);

				$encounterList = DocoHelpers::filterArrayLike($encounterList, 'payload', $this->satusehat_pasien_id, true);
				$encounterList = DocoHelpers::filterArrayLike($encounterList, 'payload', $this->satusehat_pegawai_id, true);
				$encounterList = DocoHelpers::filterArrayLike($encounterList, 'payload', $this->satusehat_ruangan_id, true);

				$encounter = !empty($encounterList) ? $encounterList[0] : null;
			}
		} else {
			$sql = "SELECT *
				FROM satusehat_integrasi_t
				WHERE 
					type = :type
					AND pendaftaran_id = :pendaftaran_id
					AND is_active = TRUE
					AND is_deleted = FALSE
				LIMIT 1";

			$params = [
				':type' => self::RESOURCE_TYPE,
				':pendaftaran_id' => $this->lap_kunjungan_data['pendaftaran_id'],
			];
			$command = $connection->createCommand($sql, $params);
			$encounter = $command->queryOne();
		}

		$sqlCondPrimary = "SELECT 
				satusehat_id,
				payload,
				(payload::json->'code'->'coding')::text AS code
			FROM satusehat_integrasi_t
			WHERE 
				type = :type
				AND pendaftaran_id = :pendaftaran_id
				AND is_active = TRUE
				AND is_deleted = FALSE
			ORDER BY created_date ASC";
		$commandCondPrimary = $connection->createCommand($sqlCondPrimary, [
			':type' => 'Condition',
			':pendaftaran_id' => ArrayHelper::getValue($this->lap_kunjungan_data, 'pendaftaran_id')
		]);
		$satusehat_condition_primary = $commandCondPrimary->queryAll();
		$satusehat_condition_primary = DocoHelpers::filterArrayLike($satusehat_condition_primary, 'payload', $encounter['satusehat_id'], true);

		$sqlCondSecondary = "SELECT 
				satusehat_id,
				payload,
				(payload::json->'code'->'coding')::text AS code
			FROM satusehat_integrasi_t
			WHERE 
				type = :type
				AND pendaftaran_id = :pendaftaran_id
				AND is_active = TRUE
				AND is_deleted = FALSE
			ORDER BY created_date ASC
		";
		$commandCondSecondary = $connection->createCommand($sqlCondSecondary, [
			':type' => 'ConditionSecondary',
			':pendaftaran_id' => ArrayHelper::getValue($this->lap_kunjungan_data, 'pendaftaran_id')
		]);
		$satusehat_condition_secondary = $commandCondSecondary->queryAll();
		$satusehat_condition_secondary = DocoHelpers::filterArrayLike($satusehat_condition_secondary, 'payload', $encounter['satusehat_id'], true);

		$lap_kunjungan = $this->lap_kunjungan_data;
			
			$tgl_pendaftaran = date('Y-m-d', strtotime($lap_kunjungan['tgl_pendaftaran'])).'T'.date('H:i:s', strtotime($lap_kunjungan['tgl_pendaftaran'])).$gmtTime;
			$tgl_progress = date('Y-m-d', strtotime($lap_kunjungan['tgl_masukperiksa'])).'T'.date('H:i:s', strtotime($lap_kunjungan['tgl_masukperiksa'])).$gmtTime;
			$tgl_pasien_pulang = (isset($lap_kunjungan['tgl_selesai']) && !empty($lap_kunjungan['tgl_selesai'])) ? date('Y-m-d', strtotime($lap_kunjungan['tgl_selesai'])).'T'.date('H:i:s', strtotime($lap_kunjungan['tgl_selesai'])).$gmtTime : date('Y-m-d') . 'T' . date('H:i:s') . $gmtTime;

    	$payload = $this->build();

			$payload['id'] = !empty($encounter['satusehat_id']) ? $encounter['satusehat_id'] : NULL;
			$payload['status'] = 'finished';
			$payload['period']['end'] = $tgl_pasien_pulang;

			$payload['statusHistory'] = [];

			$payload['statusHistory'][] = [
					'status' => 'arrived',
					'period' => [
							'start' => $tgl_pendaftaran,
							'end' => $tgl_progress
					]
			];
    	$payload['statusHistory'][] = [
    		'status' => 'in-progress',
    		'period' => [
    			'start' => $tgl_progress,
    			'end' => $tgl_pasien_pulang
    		]
    	];
    	$payload['statusHistory'][] = [
    		'status' => 'finished',
    		'period' => [
    			'start' => $tgl_pasien_pulang,
    			'end' => $tgl_pasien_pulang
    		]
    	];

			foreach($satusehat_condition_primary as $key => $primary) {
			/* PAYLOAD DIAGNOSA UTAMA */
					if (ArrayHelper::getValue($primary, 'satusehat_id') && ArrayHelper::getValue($primary, 'code')) {
						$codes = json_decode(ArrayHelper::getValue($primary, 'code'));
						foreach ($codes as $codeKey => $code) {
							$payload['diagnosis'][] = [
									"condition" => [
											"reference" => "Condition/".ArrayHelper::getValue($primary, 'satusehat_id'),
											"display" => trim(ArrayHelper::getValue($code, 'display')),
									],
									"use" => [
											"coding" => [
													[
															"system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
															"code" => "AD",
															"display" => "Admission diagnosis",
													],
											],
									],
									"rank" => 1
							];
						}
					}
			}
			foreach($satusehat_condition_secondary as $key => $secondary) {
			/* PAYLOAD DIAGNOSA PENDAMPING */
					if (ArrayHelper::getValue($secondary, 'satusehat_id') && ArrayHelper::getValue($secondary, 'code')) {
						$codes = json_decode(ArrayHelper::getValue($secondary, 'code'));
						foreach ($codes as $codeKey => $code) {
							$payload['diagnosis'][] = [
									"condition" => [
											"reference" => "Condition/".ArrayHelper::getValue($secondary, 'satusehat_id'),
											"display" => trim(ArrayHelper::getValue($code, 'display')),
									],
									"use" => [
											"coding" => [
													[
															"system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
															"code" => "AD",
															"display" => "Admission diagnosis",
													],
											],
									],
									"rank" => 2
							];
						}
					}
			}

    	return $payload;
    }
}
