<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoConstansId;
use Integrasi\Service\Sirs\Models\InfoJadwalDokterView;
use Integrasi\Service\Sirs\Models\JadwalDokterInt;
use Integrasi\Service\Sirs\Models\BpjsJkn;

class UpdateDoctorScheduleJkn extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $primaryId = $this->id;
        $state = 'update-jkn';
        $payload = $result = $jadwal = [];

        $getData = InfoJadwalDokterView::find()->select([
                        'infojadwaldokter_v.jadwaldokter_id',
                        'infojadwaldokter_v.hari_jadwalbuka',
                        'infojadwaldokter_v.waktu_mulai',
                        'infojadwaldokter_v.waktu_selesai',
                        'infojadwaldokter_v.is_active',
                        'infojadwaldokter_v.kode_dokter_bpjs',
                        'infojadwaldokter_v.kode_ruangan_bpjs',
                        'b.spesialis_kode',
                        'c.subspesialis_kode',
                    ])
                    ->innerJoin('spesialisruangan_mp a', 'infojadwaldokter_v.ruangan_id = a.ruangan_id')
                    ->innerJoin('spesialis_m b', 'a.spesialis_id = b.spesialis_id')
                    ->innerJoin('subspesialis_m c', 'a.subspesialis_id = c.subspesialis_id and c.subspesialis_kode = b.spesialis_kode');

        if (!empty($primaryId)) {
            $getData->andWhere(['jadwaldokter_id' => $primaryId]);
        }

        $qSchedule = $getData->asArray()->one();

        if (!empty($qSchedule)) {
            if ($qSchedule['is_active'] == true && !empty($qSchedule['kode_ruangan_bpjs']) && !empty($qSchedule['subspesialis_kode'])){
                $kodepoli = ArrayHelper::getValue($qSchedule, 'kode_ruangan_bpjs', null);
                $kodesubspesialis = ArrayHelper::getValue($qSchedule, 'subspesialis_kode', null);
                $kodedokter = ArrayHelper::getValue($qSchedule, 'kode_dokter_bpjs', null);
                $buka = !empty($qSchedule['waktu_mulai']) ? date("H:i", strtotime($qSchedule['waktu_mulai'])) : null;
                $tutup = !empty($qSchedule['waktu_selesai']) ? date("H:i", strtotime($qSchedule['waktu_selesai'])) : null;
                $hari = isset($qSchedule['hari_jadwalbuka']) ? $qSchedule['hari_jadwalbuka'] : null;
                $jadwal[] = [
                    'hari' => (string) $this->mappDays($hari),
                    'buka' => $buka,
                    'tutup' => $tutup
                ];

                $payload = [
                    'kodepoli' => $kodepoli,
                    'kodesubspesialis' => $kodesubspesialis,
                    'kodedokter' => (int) $kodedokter,
                    'jadwal' => $jadwal
                ];

                $result = (new BpjsJkn)->updateJadwalDokterJkn($payload);

                $this->setLogs($primaryId, $result, $state, $payload);
            }
        }     

        return $this->setResponse($payload, $result);
    }

    public function setResponse($payload, $result)
    {
        return json_encode([
            'service' => 'Sirs-UpdateDoctorScheduleJkn',
            'payload' => $payload,
            'timestamp' => date('Y-m-d H:i:s'),
            'attributes' => $this->attributes,
            'result' => $result
        ]);
    }

    public function setLogs($primaryId, $result, $state, $payload)
    {
        $userIdentity = $this->user_identity;
        $responseCode = ArrayHelper::getValue($result, 'metadata', null);
        if (!empty($responseCode)) {
            $isSent = $result['metadata']['code'] == 200 ? true : false;
            $syncResponse = $result['metadata']['message'];
        } else {
            $isSent = false;
            $syncResponse = json_encode($result);
        }

        $logData[] = [
            'jadwaldokter_id' => $primaryId,
            'is_sending' => true,
            'is_sent' => $isSent,
            'sync_respon' => $syncResponse,
            'state' => $state,
            'created_date' => date('Y-m-d H:i:s'),
            'created_by' => isset($userIdentity['uid']) ? $userIdentity['uid'] : null,
            'payload' => json_encode($payload),
        ];

        $this->saveLogs($logData);
    }

    public function saveLogs($data)
    {
        return JadwalDokterInt::batchInsert($data);
    }

    public function mappDays($dayId)
    {
        $listMapp = [
            75 => 1, //"MONDAY",
            76 => 2, // "TUESDAY"
            77 => 3, //"WEDNESDAY"
            78 => 4, // "THURSDAY"
            79 => 5, // "FRIDAY"
            80 => 6, //"SATURDAY"
            81 => 7, //"SUNDAY"
        ];

        return isset($listMapp[$dayId]) ? $listMapp[$dayId] : null;
    }
}