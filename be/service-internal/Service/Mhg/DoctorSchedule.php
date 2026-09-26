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
use Integrasi\Service\Mhg\Cache\Cache;
use Integrasi\Service\Mhg\Models\InfoJadwalDokterView;
use Integrasi\Service\Mhg\Models\JadwalDokter;

class DoctorSchedule extends \Integrasi\Contracts\DocoImplement
{
    const SCHEDULE_TYPE = 'By Schedule';
    const APPOINTMENT_TYPE = 'By Appointment';

    public function execute()
    {
        $isLast = $this->last_insert;
        $state = $this->state;
        $primaryId = $this->id;
        $isDoctor = $this->isDoctor;
        $payload = [];
        $ruanganTelekonsultasi = Cache::lookTeleRoom()->kode_id;

        if (!empty($isDoctor)) {
            return $this->deleteScheduleByDoctor($primaryId);
        }

        $getData = InfoJadwalDokterView::find()->select([
                        'infojadwaldokter_v.jadwaldokter_id',
                        'infojadwaldokter_v.nama_pegawai',
                        'infojadwaldokter_v.hari_jadwalbuka',
                        'infojadwaldokter_v.waktu_mulai',
                        'infojadwaldokter_v.waktu_selesai',
                        'infojadwaldokter_v.is_active',
                        'infojadwaldokter_v.is_loaddokter',
                        'infojadwaldokter_v.jumlah_loaddokter',
                        'infojadwaldokter_v.is_bersedia',
                        'a.additional_data',
                        'a.nomorindukpegawai',
                        'infojadwaldokter_v.ruangan_id',
                        'infojadwaldokter_v.ruangan_nama'
                    ])
                    ->innerJoin('pegawai_m a', 'infojadwaldokter_v.pegawai_id = a.pegawai_id');

        $listState = true;
        if ($isLast) {
            $getData->orderBy(['jadwaldokter_id' => SORT_DESC]);
        } else if (!empty($primaryId)) {
            $getData->andWhere(['jadwaldokter_id' => $primaryId]);
        } else {
            $listState = false;
        }

        $result = [];

        if (!empty($listState)) {
            $qSchedule = $getData->asArray()->one();
            $payload = [
                'doctor_schedule_id' => $primaryId,
            ];

            // if ($state != 'update') {
                if ($qSchedule['is_active'] == true && $qSchedule['is_loaddokter'] == true){
                    if (!empty($qSchedule)) {
                        $hari = isset($qSchedule['hari_jadwalbuka']) ? $qSchedule['hari_jadwalbuka'] : null;
                        $additional = !empty($qSchedule['additional_data']) 
                                            ? json_decode($qSchedule['additional_data'],true) : null;
                        $codeDoc = isset($qSchedule['nomorindukpegawai']) ? $qSchedule['nomorindukpegawai'] : null;
                        $jamMulai = !empty($qSchedule['waktu_mulai']) ? date("H:i", strtotime($qSchedule['waktu_mulai'])) : null;
                        $jamSelesai = !empty($qSchedule['waktu_selesai']) ? date("H:i", strtotime($qSchedule['waktu_selesai'])) : null;
                        $isActive = !empty($qSchedule['is_active']) ? $qSchedule['is_active'] : false;
                        $slotType = isset($qSchedule['is_bersedia']) && $qSchedule['is_bersedia'] == false ? self::SCHEDULE_TYPE : self::APPOINTMENT_TYPE;
                        $slotDuration = ArrayHelper::getValue($qSchedule, 'jumlah_loaddokter', null);
                        if ($state != 'delete' && $isActive) {
                            $payload = [
                                'doctor_schedule_id' => $qSchedule['jadwaldokter_id'],
                                'doctor_code' => $codeDoc,
                                'doctor_name' => isset($qSchedule['nama_pegawai']) ? $qSchedule['nama_pegawai'] : null,
                                'day_id' => $this->mappDays($hari),
                                'schedule_type' => $slotType,
                                'section' => strtoupper($this->getShift($qSchedule['waktu_mulai'])),
                                'schedule_start' => $jamMulai,
                                'schedule_end' => $jamSelesai,
                                'is_create' => $state == 'create' ? 1 : 0,
                                'hospital_id' => null,
                                'slot_duration' => $slotDuration,
                                'room_name' => $qSchedule['ruangan_nama'],
                                'is_teleconsultation' => (!empty($ruanganTelekonsultasi) && $qSchedule['ruangan_id'] == $ruanganTelekonsultasi) ? 1 : 0,
                            ];
                            $result = (new MhgService)->doctorScheduleCreate($payload);
                        } else {
                            $result = $this->deleteSchedule($payload);
                        }
                    } else {
                        $result = $this->deleteSchedule($payload);
                    }
                } else {
                    $result = $this->deleteSchedule($payload);
                }
            // }
            

            $this->setLogs($result, $state, $payload);
        }

        return $this->setResponse($payload, $result);
    }

    public function deleteScheduleByDoctor($id)
    {
        $qSchedule = Yii::$app->db->createCommand("
            SELECT 
                jadwaldokter_id 
            FROM jadwaldokter_m 
            WHERE pegawai_id = {$id} AND is_deleted = false
        ")->queryAll();

        $result = [];
        $payload = [];
        if (!empty($qSchedule)) {
            foreach ($qSchedule as $value) {
                $bodyReq = [
                    'doctor_schedule_id' => $value['jadwaldokter_id']
                ];
                $response = $this->deleteSchedule($bodyReq);
                $this->setLogs($response, $this->state, $bodyReq);
                $payload[] = $bodyReq;
                $result[] = $response;
            }
        }

        return $this->setResponse($payload, $result);
    }

    public function setResponse($payload, $result)
    {
        return json_encode([
            'service' => 'Mhg-DoctorSchedule',
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
        // $state = $state == 'update' ? 'up-by-cron' : $state;

        $isSent = isset($response['is_error']) && !empty($response['is_error']) ? false : true;

        $logData[] = [
            'jadwaldokter_id' => $payload['doctor_schedule_id'],
            'is_sending' => true,
            'is_sent' => $isSent,
            'id_sync_sercon' => $uidSercon,
            'sync_respon' => isset($response['response']) ? json_encode($response['response']) : null,
            'state' => $state,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
            'payload' => isset($response['payload']) ? json_encode($response['payload']) : null,
        ];

        $this->saveLogs($logData);
    }

    public function saveLogs($data)
    {
        return JadwalDokter::batchInsert($data);
    }

    private function deleteSchedule($payload)
    {
        return (new MhgService)->doctorScheduleDelete($payload);
    }

    public function mappDays($dayId)
    {
        $listMapp = [
            75 => 2, //"MONDAY",
            76 => 3, // "TUESDAY"
            77 => 4, //"WEDNESDAY"
            78 => 5, // "THURSDAY"
            79 => 6, // "FRIDAY"
            80 => 7, //"SATURDAY"
            81 => 1, //"SUNDAY"
        ];

        return isset($listMapp[$dayId]) ? $listMapp[$dayId] : null;
    }

    protected static function getShift($dateTime = null)
    {
        $time = strtotime(date($dateTime ? : "H:i:s"));
        $listShift = Cache::getShift();
        $shift_name = "";
        foreach ($listShift as $value) {
            $timeStart = strtotime($value['shift_jamawal']);
            $timeEnd = strtotime($value['shift_jamakhir']);
            if ($time >= $timeStart && $time <= $timeEnd) {
                $shift_name = $value['shift_nama'];
                break;
            }
        }
        return $shift_name;
    }
}