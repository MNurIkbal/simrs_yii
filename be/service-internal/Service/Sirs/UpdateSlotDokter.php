<?php

namespace Integrasi\Service\Sirs;

use DateInterval;
use DateTime;
use Integrasi\Service\Sirs\Models\JadwalDokter;
use Integrasi\Service\Sirs\Models\SlotJadwalDokter;
use Yii;
use Integrasi\Service\Mhg\Models\InfoJadwalDokterView;
use Integrasi\Components\Services\MhgService;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Mhg\Cache\Cache;
use yii\db\Expression;

class UpdateSlotDokter extends \Integrasi\Contracts\DocoImplement
{
    const MINUTES_TO_ADD = 5;
    const KUOTA_ONLINE_DEFAULT = 50;
    const MAXIMUMANTRIAN_DEFAULT = 50;
    const KUOTA_TOTAL_DEFAULT = 100;
    const STATE_GENERATE_SLOT = 'generate_slot';
    const SCHEDULE_TYPE = 'By Schedule';
    const APPOINTMENT_TYPE = 'By Appointment';
    const LIMIT_UPDATE = 50;

    public function execute()
    {
        $minutes_to_add = self::MINUTES_TO_ADD;
        $state = $this->state;
        $connection = Yii::$app->db;
        $isIntegrate = $this->integrasi;
        $temp =[];
        $jadwalId = [];
        $jadwal_dokter = new JadwalDokter;
        $limit = self::LIMIT_UPDATE;

        $getJadwalId = $connection->createCommand("
            SELECT jadwaldokter_id from jadwaldokter_m where jadwaldokter_id NOT IN (
                SELECT jadwaldokter_id FROM slotjadwaldokter_m group by jadwaldokter_id
            ) and is_active = true and is_deleted = false limit {$limit}
        ")->queryAll();

        if(!empty($getJadwalId)) {
            foreach($getJadwalId as $val) {
                $jadwalId[] = $val['jadwaldokter_id'];
            }
        }

        if(!empty($state) && $state == self::STATE_GENERATE_SLOT) { 
            $temp = $jadwal_dokter->find()
            ->selectAttr()
            ->where(new Expression('jumlah_loaddokter is null'))
            ->orderBy(['jadwaldokter_id' => SORT_ASC])
            ->limit($limit)
            ->asArray()
            ->all();

            // get and delete slotdokter
            // $connection->createCommand("
            //     DELETE FROM slotjadwaldokter_m WHERE jadwaldokter_id IN (
            //         SELECT jadwaldokter_id FROM jadwaldokter_m where jumlah_loaddokter is null limit {$limit}
            //     )
            // ")->execute();
        } else { //case data awal
            $isIntegrate = false;
            $temp = $jadwal_dokter->find()->selectAttr()->where(['IN', 'jadwaldokter_id',$jadwalId])->asArray()->all();
            $temp2 =[];
            $i =0;
            $j =0;
            // foreach ($jadwal_dokter as $value) {
            //     if ($i < self::LIMIT_UPDATE) {
            //         $temp[$i] = $value;

            //     } else {
            //         if (empty($value['jumlah_loaddokter'])) {
            //             $temp2[$j] = $value;
            //             $j++;
            //         }
            //     }
            //     $i++;
            // }

            // hapus semua slot jadwal dokter
            // $connection->createCommand()->truncateTable('slotjadwaldokter_m')->execute();
            // end
        }

        // generate slot dokter
        // $result = $this->generateSlotDokter($temp, $minutes_to_add, $isIntegrate);
        $this->generateSlotDokter($temp, $minutes_to_add, $isIntegrate);
        // $this->generateSlotDokter($temp2 , $minutes_to_add, $isIntegrate);
        // end

        return json_encode([
            'service' => 'Sirs-UodateSlotDokter',
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => 'Data berhasil di sync',
        ]);
    }

    private function generateSlotDokter($jadwal_dokter, $minutes_to_add, $isIntegrate){
        
        foreach ($jadwal_dokter as $value) {
            $list_slot = [];
            
            $jadwaldokter_id = $value['jadwaldokter_id'];
            $jadwaldokter_tgl = $value['jadwaldokter_tgl'];
            $jadwaldokter_mulai = $value['jadwaldokter_mulai'];
            $jadwaldokter_tutup = $value['jadwaldokter_tutup'];
            $jumlah_loaddokter = $value['jumlah_loaddokter'];
            
            $slot_sequence = 0;
            $time = new DateTime(''.$jadwaldokter_tgl.' '.$jadwaldokter_mulai.'');
            $times = new DateTime(''.$jadwaldokter_tgl.' '.$jadwaldokter_tutup.'');
            $minutes_set = (!empty($jumlah_loaddokter)) ? $jumlah_loaddokter : $minutes_to_add;
            $rangeMinutes = $this->getMinutesFromRange($jadwaldokter_mulai, $jadwaldokter_tutup);
            $totalKuota = floor($rangeMinutes / $minutes_set);
            $kuotaOnlineAndOffline = floor($totalKuota / 2);

            while ($time < $times){
                $slot_sequence++;
                $time_start = $time->format('H:i:s');
                $time_end = $time->add(new DateInterval('PT' . $minutes_set . 'M'));
                $time = $time_end;
                $time_end = $time_end->format('H:i:s');
                $data = [
                    'slot_sequence' => $slot_sequence,
                    'jadwaldokter_tgl' => $jadwaldokter_tgl,
                    'jadwaldokter_id' => $jadwaldokter_id,
                    'jam_mulai' => $time_start,
                    'jam_selesai' => $time_end,
                    'slot_type' => true,
                    'created_by' => $this->user_identity['uid'],
                    'created_date' => date('Y-m-d H:i:s'),
                    'is_active' => true,
                ];
                $list_slot[] = $data;
            }
            $update_jadwal_dokter = JadwalDokter::find()->where(['jadwaldokter_id' => $jadwaldokter_id])->one();
            $update_jadwal_dokter->is_loaddokter = (!empty($value['is_loaddokter']) && (($value['is_loaddokter'] == false) || ($value['is_loaddokter'] == '0'))) ? true : $value['is_loaddokter'];
            $update_jadwal_dokter->maximumantrian = (!empty($totalKuota)) ? $kuotaOnlineAndOffline : self::MAXIMUMANTRIAN_DEFAULT;
            $update_jadwal_dokter->kuota_online = (!empty($totalKuota)) ? $kuotaOnlineAndOffline : self::KUOTA_ONLINE_DEFAULT;
            $update_jadwal_dokter->kuota_total = (!empty($totalKuota)) ? $totalKuota : self::KUOTA_TOTAL_DEFAULT;
            $update_jadwal_dokter->jumlah_loaddokter = $minutes_set;

            $kuota_offline_bpjs = floor($update_jadwal_dokter->maximumantrian/2);
            $kuota_offline_nonbpjs = floor($update_jadwal_dokter->maximumantrian/2);
            $kuota_online_bpjs = floor($update_jadwal_dokter->kuota_online/2);
            $kuota_online_nonbpjs = floor($update_jadwal_dokter->kuota_online/2);
            $kuota_total_bpjs = floor($kuota_online_bpjs + $kuota_offline_bpjs);
            $kuota_total_nonbpjs = floor($kuota_online_nonbpjs + $kuota_offline_nonbpjs);
            
            $update_jadwal_dokter->kuota_bpjs_offline = $kuota_offline_bpjs;
            $update_jadwal_dokter->kuota_nonbpjs_offline = $kuota_offline_nonbpjs;
            $update_jadwal_dokter->kuota_bpjs_online = $kuota_online_bpjs;
            $update_jadwal_dokter->kuota_nonbpjs_online = $kuota_online_nonbpjs;
            $update_jadwal_dokter->kuota_bpjs_total = $kuota_total_bpjs;
            $update_jadwal_dokter->kuota_nonbpjs_total = $kuota_total_nonbpjs;


            $update_jadwal_dokter->save();
            if(!empty($isIntegrate) && $isIntegrate == true) {
                $this->syncSchedule($jadwaldokter_id);
            }
            $this->insertSlotDokter($list_slot);
        }
        
    }

    private function insertSlotDokter($list_slot){
        return SlotJadwalDokter::batchInsert($list_slot);
    }

    private function getMinutesFromRange($timeStart, $timeEnd)
    {
        $start = $this->convertToMinutes($timeStart);
        $end = $this->convertToMinutes($timeEnd);

        return $end - $start;
    }

    private function convertToMinutes($time)
    {
        $time = date('H:i', strtotime($time));
        $timeExplode = explode(":", $time);

        $hour = $timeExplode[0] * 60;
        $minute = $timeExplode[1];

        return $hour + $minute;
    }

    private function syncSchedule($id)
    {
        $ruanganTelekonsultasi = Cache::lookTeleRoom()->kode_id;
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
        ->innerJoin('pegawai_m a', 'infojadwaldokter_v.pegawai_id = a.pegawai_id')
        ->where(['jadwaldokter_id' => $id]);

        $qSchedule = $getData->asArray()->one();

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
                if ($isActive) {
                    $payload = [
                        'doctor_schedule_id' => $qSchedule['jadwaldokter_id'],
                        'doctor_code' => $codeDoc,
                        'doctor_name' => isset($qSchedule['nama_pegawai']) ? $qSchedule['nama_pegawai'] : null,
                        'day_id' => DocoHelpers::mappDays($hari),
                        'schedule_type' => $slotType,
                        'section' => strtoupper(DocoHelpers::getShift($qSchedule['waktu_mulai'])),
                        'schedule_start' => $jamMulai,
                        'schedule_end' => $jamSelesai,
                        'is_create' => 1,
                        'hospital_id' => null,
                        'slot_duration' => $slotDuration,
                        'room_name' => $qSchedule['ruangan_nama'],
                        'is_teleconsultation' => (!empty($ruanganTelekonsultasi) && $qSchedule['ruangan_id'] == $ruanganTelekonsultasi) ? 1 : 0,
                    ];
                    $result = (new MhgService)->doctorScheduleCreate($payload);
                }
            }

            $this->setLogs($result, 'create', $payload);
        }
    }

    public function setLogs($result, $state, $payload)
    {
        $userIdentity = $this->user_identity;
        $uidSercon = isset($result['uid']) ? $result['uid'] : null;
        $response = isset($result['response']) ? $result['response'] : [];
        $state = $state == 'update' ? 'up-by-cron' : $state;

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
}