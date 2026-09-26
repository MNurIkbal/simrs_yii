<?php


namespace app\modules\v1\models;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\models\bpjs\BpjsJkn;

class JadwalDokterView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infojadwaldokter_v';
    }

    public static function primaryKey()
    {
        return ['jadwaldokter_id'];
    }


    public function updateJadwalDokterJkn($jadwaldokter_id)
    {
        $primaryId = $jadwaldokter_id;
        $state = 'update-jkn';
        $payload = $result = $jadwal = [];

        $getData = JadwalDokterView::find()->select([
                        'infojadwaldokter_v.jadwaldokter_id',
                        'infojadwaldokter_v.hari_jadwalbuka',
                        'infojadwaldokter_v.waktu_mulai',
                        'infojadwaldokter_v.waktu_selesai',
                        'infojadwaldokter_v.is_active',
                        'infojadwaldokter_v.kode_dokter_bpjs',
                        'infojadwaldokter_v.kode_ruangan_bpjs',
                        'c.kdpoli',
                    ])
                    ->leftJoin('bpjs_referensipoli c', 'infojadwaldokter_v.kode_ruangan_bpjs = c.kdsubspesialis');

        if (!empty($primaryId)) {
            $getData->andWhere(['jadwaldokter_id' => $primaryId]);
        }

        $qSchedule = $getData->asArray()->one();

        if (!empty($qSchedule)) {
            if ($qSchedule['is_active'] == true && !empty($qSchedule['kode_ruangan_bpjs']) && !empty($qSchedule['kdpoli'])){
                $kodepoli = ArrayHelper::getValue($qSchedule, 'kdpoli', null);
                $kodesubspesialis = ArrayHelper::getValue($qSchedule, 'kode_ruangan_bpjs', null);
                $kodedokter = ArrayHelper::getValue($qSchedule, 'kode_dokter_bpjs', null);
                $buka = !empty($qSchedule['waktu_mulai']) ? date("H:i", strtotime($qSchedule['waktu_mulai'])) : null;
                $tutup = !empty($qSchedule['waktu_selesai']) ? date("H:i", strtotime($qSchedule['waktu_selesai'])) : null;
                $hari = isset($qSchedule['hari_jadwalbuka']) ? $qSchedule['hari_jadwalbuka'] : null;
                $jadwal[] = [
                    'hari' => (string) self::mappDays($hari),
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

                return compact('result', 'payload');

            }
        }

        $result = [
            'metadata' => [
                'message' => 'Tidak Ada Jadwal BPJS'
            ]
        ];

        return self::setResponse($payload, $result);
    }

    private function setResponse($payload, $result)
    {
        return [
            'service' => 'Sirs-UpdateDoctorScheduleJkn',
            'payload' => $payload,
            'timestamp' => date('Y-m-d H:i:s'),
            'result' => $result
        ];
    }

    private function mappDays($dayId)
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
