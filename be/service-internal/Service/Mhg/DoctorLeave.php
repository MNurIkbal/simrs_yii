<?php

namespace Integrasi\Service\Mhg;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Integrasi\Components\Services\MhgService;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoConstansId;
use Integrasi\Service\Mhg\Models\JadwalCuti;
use Integrasi\Service\Mhg\Models\JadwalCutiView;

class DoctorLeave extends \Integrasi\Contracts\DocoImplement
{
    const STATE_CREATE = 'create';
    const STATE_UPDATE = 'update';
    const STATE_DELETE = 'delete';
    const IS_CREATE = 1;
    const IS_UPDATE = 0;
    const IS_ACTIVE = 1;
    const FULLDAY = 0;

    public function execute()
    {
        $id = ArrayHelper::getValue($this->result, 'jadwalcuti_id');
        $resultData = ArrayHelper::getValue($this->result, 'data');
        $state = $this->state;
        $userIdentity = $this->user_identity;
        $allPayload = $payload = $allResult = $result = [];
        
        $modelCuti = $this->getJadwalCuti();
        if ($id) {
            $modelCuti->where(['jadwalcuti_id' => $id]);
        } else {
            $tglAwal = date('Y-m-d', strtotime($resultData['tgl_cuti_awal']));
            $tglAkhir = date('Y-m-d', strtotime($resultData['tgl_cuti_akhir']));
            $modelCuti->where([
                'dokter_id' => $resultData['pegawai_id'],
                'ruangan_id' => $resultData['ruangan_id'],
            ])->andWhere(['BETWEEN', 'DATE(tgl_cuti_awal)', $tglAwal, $tglAkhir]);
        }
        $dataCuti = $modelCuti->asArray()->all();

        
        if ($state != self::STATE_DELETE) {
            if (!empty($dataCuti)) {
                foreach ($dataCuti as $key => $value) {
                    $payload = [
                        'leave_id_sirs' => $value['jadwalcuti_id'],
                        'hospital_id' => null,
                        'leave_date' => date('Y-m-d', strtotime($value['tgl_cuti_awal'])),
                        'doctor_id' => $value['dokter_id'],
                        'leave_half_day' => self::FULLDAY,
                        'leave_start_time' => "",
                        'leave_end_time' => "",
                        'leave_reason' => $value['alasan_cuti'],
                        'status' => self::IS_ACTIVE,
                        'username' => ArrayHelper::getValue($userIdentity, 'username'),
                        'is_create' => $state == self::STATE_CREATE ? self::IS_CREATE : self::IS_UPDATE,
                    ]; 
                    $result = (new MhgService)->doctorLeaveCreate($payload);
                    $this->setLogs($result, $state, $payload);

                    $allResult[] = $result;
                    $allPayload[] = $payload;
                }
            }
        } else {
            $allPayload = [
                'leave_id_sirs' => $id,
                'hospital_id' => null,
            ];
            $allResult = $this->deleteDoctorLeave($allPayload);

            $this->setLogs($allResult, $state, $allPayload);
        }

        return $this->setResponse($allPayload, $allResult);
    }

    public function getJadwalCuti() {
        return JadwalCutiView::find();
    }

    public function setResponse($payload, $result)
    {
        return json_encode([
            'service' => 'Mhg-DoctorLeave',
            'payload' => $payload,
            'timestamp' => date('Y-m-d H:i:s'),
            'attributes' => $this->attributes,
            'result' => $result
        ]);
    }

    public function setLogs($result, $state, $payload)
    {
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;
        $response = isset($result['response']) ? $result['response'] : [];
        $isSent = isset($response['is_error']) && !empty($response['is_error']) ? false : true;

        $logData[] = [
            'jadwalcuti_id' => $payload['leave_id_sirs'],
            'is_sending' => true,
            'is_sent' => $isSent,
            'id_sync_sercon' => $uidSercon,
            'sync_respon' => isset($response['response']) ? json_encode($response['response']) : null,
            'state' => $state,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
            'payload' => json_encode($payload),
        ];

        $this->saveLogs($logData);
    }

    public function saveLogs($data)
    {
        return JadwalCuti::batchInsert($data);
    }

    private function deleteDoctorLeave($payload)
    {
        return (new MhgService)->doctorLeaveDelete($payload);
    }
}