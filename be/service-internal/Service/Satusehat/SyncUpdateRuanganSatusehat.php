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

class SyncUpdateRuanganSatusehat extends \Integrasi\Service\Satusehat\SyncRuanganSatusehat
{
    const RESOURCE_TYPE = 'Location';
    public $logType = 'LocationUpdate';
    public $defaultProcessLimit = 5;
    public $ruanganId;
    public $organizationId;

    public function execute()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '3600');

        $this->setAttrSatusehat();
        $ruanganId = isset($this->attributes['result']['id']) ? DocoHelpers::decrypt($this->attributes['result']['id']) : NULL;
        $state = $this->state;

        if (isset($this->attributes['result']['state_input']) && $this->attributes['result']['state_input'] != 'update') {
            return json_encode([
                'service' => 'Sirs-SyncUpdateRuanganSatusehat',
                'timestamp' => date('Y-m-d H:i:s'),
                'message' => 'Data diproses pada bagian sync ruangan satusehat',
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
                if (!empty($ruanganId) && $this->attributes['result']['state_input'] == 'update') {
                    $ruangan = Ruangan::find()->where(['ruangan_id' => $ruanganId]);

                    $dataRuangan = $ruangan->select([
                        'ruangan_m.*',
                        'instalasi_m.instalasi_id',
                        'instalasi_m.instalasi_nama',
                        'instalasi_m.is_pelayanan',
                        'instalasi_m.satusehat_instalasi_id'
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
        	        		a.is_active = TRUE
        	        		AND a.is_deleted = FALSE
        	    	) instalasi_m', 'ruangan_m.instalasi_id = instalasi_m.instalasi_id');

                    
                    $antrian_lantai = JenisAntrianRuanganMp::find()->where(['ruangan_id' => $ruanganId])->one();
                    $lantai_nama = null;
                    if ($antrian_lantai) {
                        $lantai_detail = JenisAntrianDetail::find()->Where(['jenisantriandetail_id' => $antrian_lantai->jenisantriandetail_id])->one();
                        $lantai_nama = $lantai_detail->nama;
                    }

                    $dataRuangan = $ruangan->asArray()->one();

                    $payload = $this->buildUpdate($dataRuangan, $wilayah_rs, $profile_rs, $lantai_nama);
                    $res = (new SatusehatService)->updateLocation($payload, true);
                    $this->setLogs($ruanganId, $state, $payload, $res);
                }

                return json_encode([
                    'service' => 'Sirs-SyncUpdateRuanganSatusehat',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'message' => 'Data berhasil di sync',
                ]);
            } else {
                return json_encode([
                    'service' => 'Sirs-SyncUpdateRuanganSatusehat',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'message' => 'Data gagal di sync, Kode Wilayah Rumah Sakit tidak ditemukan',
                ]);
            }
        } else {
            return json_encode([
                'service' => 'Sirs-SyncUpdateRuanganSatusehat',
                'timestamp' => date('Y-m-d H:i:s'),
                'message' => 'Data gagal di sync, Profile Rumah Sakit harus di isi dahulu',
            ]);
        }
    }

    protected function buildUpdate($data, $wilayah_rs, $profile_rs, $lantai_nama)
    {
        $satusehat_ruangan = SatusehatRuangan::find()
            ->where('ruangan_id = ' . $data['ruangan_id'] . ' 
										AND satusehat_ruangan_id IS NOT NULL
										AND is_active = true
										AND is_deleted = false')
            ->asArray()->one();

        $payload = $this->build($data, $wilayah_rs, $profile_rs, $lantai_nama);
        $payload['id'] = $satusehat_ruangan['satusehat_ruangan_id'];
        $payload['status'] = ($data['is_active'] == true) ? 'active' : 'inactive';
        $payload['name'] = $data['ruangan_nama'];

        return $payload;
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
    }
}
