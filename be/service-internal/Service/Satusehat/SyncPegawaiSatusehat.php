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
use Integrasi\Service\Satusehat\Models\Pegawai;
use Integrasi\Service\Satusehat\Models\SatusehatPegawai;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Sirs\Models\PegawaiMasterView;

class SyncPegawaiSatusehat extends \Integrasi\Contracts\DocoImplement
{
    const RESOURCE_TYPE = 'Practitioner';
    public $logType = 'Practitioner';
    public $defaultProcessLimit = 5;
    public $pegawaiId;
    public $organizationId;
    public $unique_str;

    public function execute()
    {
        $this->setAttrSatusehat();
        $pegawaiId = isset($this->attributes['result']['additional']['doctor_id']) ? ($this->attributes['result']['additional']['doctor_id']) : NULL;
        $oldNik = isset($this->attributes['result']['additional']['old_nik']) ? ($this->attributes['result']['additional']['old_nik']) : NULL;
        $state = $this->state;

        // Running handle by create / update pegawai
        if(!empty($pegawaiId)) {
            $pegawai = PegawaiMasterView::find()->where(['pegawai_id' => $pegawaiId]);
            $dataPegawai = $pegawai->asArray()->one();
            $dataPegawai['old_nik'] = $oldNik;
            $practitionerId = isset($dataPegawai['satusehat_pegawai_id']) ? $dataPegawai['satusehat_pegawai_id'] : null;

            $payload = $this->build($dataPegawai);

            $res = null;
            if ($payload) {
                $res = (new SatusehatService)->createPractitioner($payload, true);
            }
            $this->setLogs($pegawaiId, $state, $payload, $res, $practitionerId);
        }
        
        // Running handle by cron job
        if(empty($pegawaiId)) {
            $this->defaultProcessLimit = ArrayHelper::getValue($this->attributes, 'limit_process', 5);
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

            $pegawai = Pegawai::find();
            $dataPegawai = $pegawai->select([
                'pegawai_m.pegawai_id', 
                'pegawai_m.nomorindukpegawai', 
                'pegawai_m.gelardepan', 
                'pegawai_m.nama_pegawai', 
                'pegawai_m.gelarbelakang',
                'pegawai_m.jenisidentitas',
                'pegawai_m.noidentitas',
                'satusehat_pegawai.satusehat_pegawai_id' 
            ]);
            $dataPegawai = $pegawai->leftJoin('(
                SELECT 
                    p_satusehat.pegawai_id, 
                    p_satusehat.satusehat_pegawai_id,
                    p_satusehat.satusehat_integration_id
                FROM 
                    pegawai_satusehat_m p_satusehat 
                JOIN (
                    SELECT pegawai_id FROM pegawai_m 
                    WHERE is_active = TRUE
                    AND is_deleted = FALSE
                ) pegawai_m ON p_satusehat.pegawai_id = pegawai_m.pegawai_id
                WHERE 
                    p_satusehat.satusehat_pegawai_id IS NOT NULL 
                    AND p_satusehat.is_active = TRUE
                    AND p_satusehat.is_deleted = FALSE
            ) satusehat_pegawai', 'satusehat_pegawai.pegawai_id = pegawai_m.pegawai_id');
            $dataPegawai = $pegawai->where('pegawai_m.is_active = true AND pegawai_m.is_deleted = false AND pegawai_m.noidentitas is not null AND pegawai_m.noidentitas <> \'-\' AND satusehat_pegawai.satusehat_pegawai_id is null');
            $dataPegawai = $pegawai->orderBy('pegawai_m.pegawai_id ASC')->limit($this->defaultProcessLimit)->asArray()->all();

            $no = 0;
            foreach($dataPegawai as $key => $value) {
                $no++;
                $pegawai_id = isset($value['pegawai_id']) ? $value['pegawai_id'] : null;
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
                $res = (new SatusehatService)->createPractitioner($payload, true);
                $this->setLogs($pegawai_id, $state, $payload, $res);
            }
            if ($this->unique_str) {
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'syncDataSatusehat:'.$this->unique_str,
                    'message' => json_encode([
                        'status' => 'finish', 
                        'messageProcess' => 'Data Pegawai Berhasil di Sync',
                        'progress' => '100'
                    ]),
                ]);
            }
        }
        
        return json_encode([
            'service' => 'Sirs-SyncPegawaiSatusehat',
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => 'Data berhasil di sync',
            'res' => isset($res) ? $res : [],
            'unique_str' => $this->unique_str,
            'attr' => $this->attributes,
        ]);
    }

    protected function build($data, $wilayah_rs = NULL, $profile_rs = NULL) {
        $nik = isset($data['noidentitas']) ? $data['noidentitas'] : null;
        $oldNik = isset($data['old_nik']) ? $data['old_nik'] : null;

        if ($nik != $oldNik || empty($data['satusehat_pegawai_id'])) {
            if (empty($data['satusehat_pegawai_id'])) {
                $this->state = 'create';
            }
            return [
                'pegawai_nik' => $data['noidentitas']
            ];
        } else {
            return null;
        }
    }

    protected function setLogs($pegawaiId, $state, $payload, $result, $additionalId = NULL)
    {
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;
        $result_sercon = isset($result['response']) ? (is_array($result['response']) ? $result['response'] : json_decode($result['response'], true)) : [];

        $practitioner_id = isset($result_sercon['entry'][0]) ? $result_sercon['entry'][0]['resource']['id'] : null;
        
        if (empty($practitioner_id)) {
            $practitioner_id = isset($additionalId) ? $additionalId : null;
        }

        $error = !empty($result['error']) ? $result['error'] : NULL;

        if(!empty($error)) {
            $sync_response = $error;
        } else {        
            $sync_response = isset($result['response']) ? (
                        is_array($result['response']) ? json_encode($result['response']) : $result['response']
                    ) : json_encode($result);
        }

        $logData = [
            'is_sent' => true,
            'type' => $this->logType,
            'state' => $state,
            'id_sync_sercon' => $uidSercon,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
            'payload' => json_encode($payload),
            'is_deleted' => false,
            'is_active' => true,
            'additional_id' => $pegawaiId,
            'satusehat_id' => $practitioner_id,
            'sync_response' => $sync_response
        ];

        $satusehat = $this->saveLogs($logData);

        $data_pegawai = [
            'pegawai_id' => $pegawaiId,
            'satusehat_integration_id' => $satusehat['id'],
            'satusehat_pegawai_id' => $practitioner_id
        ];

        $this->saveSatusehatPegawai($data_pegawai);
    }

    protected function saveLogs($data)
    {   
        $isSent = isset($data['is_sent']) ? $data['is_sent'] : null;
        $type = isset($data['type']) ? $data['type'] : null;
        $state = isset($data['state']) ? $data['state'] : null;
        $idSyncSercon = isset($data['id_sync_sercon']) ? $data['id_sync_sercon'] : null;
        $createdDate = isset($data['created_date']) ? $data['created_date'] : null;
        $createdBy = isset($data['created_by']) ? $data['created_by'] : null;
        $payload = isset($data['payload']) ? $data['payload'] : null;
        $isDeleted = isset($data['is_deleted']) ? $data['is_deleted'] : null;
        $isActive = isset($data['is_active']) ? $data['is_active'] : null;
        $additionalId = isset($data['additional_id']) ? $data['additional_id'] : null;
        $satusehatId = isset($data['satusehat_id']) ? $data['satusehat_id'] : null;
        $syncResponse = isset($data['sync_response']) ? $data['sync_response'] : null;

        $insertSatusehat = new Satusehat;
        $insertSatusehat->is_sent = $isSent;
        $insertSatusehat->type = $type;
        $insertSatusehat->state = $state;
        $insertSatusehat->id_sync_sercon = $idSyncSercon;
        $insertSatusehat->created_date = $createdDate;
        $insertSatusehat->created_by = $createdBy;
        $insertSatusehat->payload = $payload;
        $insertSatusehat->is_deleted = $isDeleted;
        $insertSatusehat->is_active = $isActive;
        $insertSatusehat->additional_id = $additionalId;
        $insertSatusehat->satusehat_id = $satusehatId;
        $insertSatusehat->sync_response = $syncResponse;
        $insertSatusehat->save();

        return $insertSatusehat;
    }

    protected function saveSatusehatPegawai($data) 
    {
        $pegawaiSatusehat = SatusehatPegawai::find()->where(['pegawai_id' => $data['pegawai_id']])->one();

        if (empty($pegawaiSatusehat)) {
            $pegawaiSatusehat = new SatusehatPegawai;
        }
        $pegawaiSatusehat->pegawai_id = $data['pegawai_id'];
        $pegawaiSatusehat->satusehat_integration_id = $data['satusehat_integration_id'];
        $pegawaiSatusehat->satusehat_pegawai_id = $data['satusehat_pegawai_id'];

        $pegawaiSatusehat->save();

        return $pegawaiSatusehat;
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