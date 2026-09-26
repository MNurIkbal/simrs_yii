<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\base\DynamicModel;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\components\constans\HariConstans;
use DatePeriod, DateTime, DateInterval;
use app\modules\v1\payload\SlotStatusPayload;
use app\modules\v1\cache\Cache;
use Doco\models\Pegawai;
use yii\helpers\ArrayHelper;
class AppointmentController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
        $verbs["get-availabel-slot"] = ["GET"];
        $verbs["get-first-available-slot"] = ["GET"];
        $verbs["get-slot-status"] = ["GET","POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        return null;
    }

    public function actionGetAvailabelSlot($doctor_code = null, $from = null, $to = null)
    {
        $model = new DynamicModel(compact('doctor_code', 'from', 'to'));
        $model->addRule(['from', 'to'], 'datetime', ['format' => 'php:Y-m-d'])
              ->addRule(['doctor_code', 'from', 'to'], 'required');
        
        if (!$model->validate()) 
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $model->errors]);

        $db = Yii::$app->db;
        $dataDokter = Yii::$app->db->createCommand("
            SELECT 
                a.pegawai_id,
                a.nama_pegawai,
                a.nomorindukpegawai,
                b.spesialis_nama
            FROM pegawai_m a
            LEFT JOIN spesialis_m b ON a.spesialis_id = b.spesialis_id
            WHERE a.nomorindukpegawai = '{$doctor_code}' AND a.is_deleted = false AND a.is_active = true
        ")->queryOne();

        if (!empty($dataDokter)) {
            $pegawaiId = $dataDokter['pegawai_id'];
            $docCode = $dataDokter['nomorindukpegawai'];
            $namaSpsialis = $dataDokter['spesialis_nama'];
            $namaDok = $dataDokter['nama_pegawai'];

            $start = $from ? date('Y-m-d', strtotime($from)) : date('Y-m-d');
            $end = $to ? date('Y-m-d', strtotime($to)) : date('Y-m-d');

            // $listDataBooked = $this->getListBooked($pegawaiId, $start, $end);
            $listHari = $mappDaysToDate = [];
            $mappDate = DocoHelpers::mappDateWithLookup($start, $end);
            if(!empty($mappDate)) {
                $listHari = $mappDate['listHari'];
                $mappDaysToDate = $mappDate['mappDate'];
            }

            $listDataSlot = [];
            if (!empty($listHari)) {
                $expListHari = implode(',', $listHari);
                $getDataSchedule = $db->createCommand("
                    SELECT 
                        a.jadwaldokter_id,
                        a.jadwaldokter_mulai,
                        a.jadwaldokter_tutup,
                        a.is_loaddokter,
                        a.jumlah_loaddokter,
                        a.maximumantrian as kuota_offline,
                        a.kuota_online,
                        c.kuota_tersedia,
                        c.kuota_masuk,
                        b.hari,
                        d.ruangan_nama
                    FROM jadwaldokter_m a
                    JOIN jadwalbukapoli_m b ON a.jadwalbukapoli_id = b.jadwalbukapoli_id
                    JOIN kuotadokter_r c ON a.jadwaldokter_id = c.jadwaldokter_id and c.is_online = true
                    JOIN ruangan_m d ON a.ruangan_id = d.ruangan_id
                    WHERE a.is_deleted = false 
                            AND a.is_active = true 
                            AND b.hari IN ({$expListHari})
                            AND a.is_loaddokter = true
                            AND a.jumlah_loaddokter IS NOT NULL
                            AND a.pegawai_id = {$pegawaiId}
                ")->queryAll();

                $statusDitolak = DocoConstants::VAR_STATUS_DAFTAR_OL_DITOLAK;
                $getKuotaOut = $db->createCommand("
                    SELECT 
                        a.tgl_antrian::DATE,
                        a.jadwaldokter_id,
                        ( SELECT count(d.jadwaldokter_id)
                            FROM antrian_t d
                            LEFT JOIN pendaftaranol_t e on d.antrian_id = e.antrian_id
                            WHERE d.tgl_antrian::DATE = a.tgl_antrian::DATE
                                AND d.jadwaldokter_id = a.jadwaldokter_id
                                AND e.status_daftar_ol <> {$statusDitolak}
                        ) as kuota_keluar
                    FROM antrian_t a
                    WHERE a.is_deleted = false 
                        AND a.pegawai_id = {$pegawaiId}
                        AND a.tgl_antrian::DATE BETWEEN '{$start}' AND '{$end}'
                ")->queryAll();

                if (!empty($getDataSchedule)) {
                    foreach ($getDataSchedule as $dataSchedule) {
                        $jmlLoad = $dataSchedule['jumlah_loaddokter'];
                        $mulai = date('H:i', strtotime($dataSchedule['jadwaldokter_mulai']));
                        $selesai = date('H:i', strtotime($dataSchedule['jadwaldokter_tutup']));
                        $hariId = $dataSchedule['hari'];
                        $rangeMinutes = $this->getMinutesFromRange($mulai, $selesai);
                        // $kuota = $rangeMinutes / $jmlLoad;
                        $kuotaMasuk = (int) $dataSchedule['kuota_masuk'];
                        $jadwalDokterId = $dataSchedule['jadwaldokter_id'];
                        $ruangan = $dataSchedule['ruangan_nama'];

                        // if(!is_null($dataSchedule['kuota_tersedia'])) {
                        //     $kuota = (int) $dataSchedule['kuota_tersedia'];
                        // }

                        $listDataSlot[$hariId][] = [
                            'kode_dokter' => $docCode,
                            'nama_dokter' => $namaDok,
                            'dokter_spesialis' => $namaSpsialis,
                            'tanggal' => null,
                            'jadwaldokter_mulai' => $mulai,
                            'jadwaldokter_tutup' => $selesai,
                            'kuota' => $kuotaMasuk,
                            'jadwaldokter_id' => $jadwalDokterId,
                            'kuota_master' => $dataSchedule['kuota_online'],
                            'nama_ruangan' => $ruangan,
                        ];
                    }
                }
            }

            $listSlotAvailability = $listChangeSchedule = [];
            $today = date('Y-m-d', strtotime('NOW'));
            $listChangeSchedule = $this->getChangingSchedule();

            foreach ($mappDaysToDate as $date => $days) {
                if (isset($listDataSlot[$days])) {
                    foreach ($listDataSlot[$days] as $rowData) {
                        $dataSlot = $rowData;
                        $dataSlot['tanggal'] = $date;

                        if($date == $today) {
                            if(isset($listChangeSchedule[$dataSlot['jadwaldokter_id']])) {
                                $dataSlot['jadwaldokter_mulai'] = $listChangeSchedule[$dataSlot['jadwaldokter_id']]['jam_mulai'];
                                $dataSlot['jadwaldokter_tutup'] = $listChangeSchedule[$dataSlot['jadwaldokter_id']]['jam_selesai'];
                            }
                        } else {
                            $dataSlot['kuota'] = $dataSlot['kuota_master'];
                        }

                        foreach ($getKuotaOut as $dataKuotaOut) {
                            if ($dataKuotaOut['jadwaldokter_id'] == $dataSlot['jadwaldokter_id'] && $dataKuotaOut['tgl_antrian'] == $date) {
                                $dataSlot['kuota'] = $dataSlot['kuota'] - $dataKuotaOut['kuota_keluar'];
                                break;
                            }
                        }

                        $listSlotAvailability[] = $dataSlot;
                    }
                }
            }
            return [
                'data' => $listSlotAvailability
            ];
        }

        return [
            'status' => 422,
            'messages' => "Dokter dengan kode {$doctor_code} tidak ditemukan",
            'data' => "Dokter dengan kode {$doctor_code} tidak ditemukan"
        ];
    }

    public function actionGetSlotStatus()
    {
        $request = Yii::$app->request;
        $payload = new SlotStatusPayload;
        $payload->attributes = $request->post();
        if ($payload->validate()) {
            $db = Yii::$app->db;
            $getDays = new DateTime($payload->slot_date);
            $start = Date('H:i:s', strtotime($payload->slot_start));
            $end = Date('H:i:s', strtotime($payload->slot_end));
            $daysSchedule = DocoHelpers::mappDaysFromLookup($getDays->format('w'));
            $slotType = $payload->slot_type == 1 ? 'true' : 'false';
            $today = date('Y-m-d', strtotime('NOW'));

            if($payload->slot_date == $today) {
                /* check if jadwal change */
                $checkChangeSchedule = $db->createCommand("
                SELECT COUNT
                    ( slotjadwaldokter_r.jadwaldokter_id ) as data
                FROM
                    slotjadwaldokter_r
                    JOIN jadwaldokter_m ON slotjadwaldokter_r.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id 
                WHERE
                    slotjadwaldokter_r.created_date :: DATE = '{$today}' 
                    AND jadwaldokter_m.pegawai_id = {$payload->slot_doctor_id}
                ")->queryOne();

                if(!empty($checkChangeSchedule['data'])) {
                    $getDataSchedule = $this->slotChangeSchedule($payload, [
                        'daysSchedule' => $daysSchedule,
                        'slotType' => $slotType,
                        'start' => $start,
                        'end' => $end
                    ]);
                } else {
                    $getDataSchedule = $this->slotNormal($payload, [
                        'daysSchedule' => $daysSchedule,
                        'slotType' => $slotType,
                        'start' => $start,
                        'end' => $end
                    ]);
                }
            } else {
                $getDataSchedule = $this->slotNormal($payload, [
                    'daysSchedule' => $daysSchedule,
                    'slotType' => $slotType,
                    'start' => $start,
                    'end' => $end
                ]);
            }

            return [
                'data' => $getDataSchedule
            ];
        }
        return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $payload->errors]);
    }

    private function getListBooked($pegawaiId, $start, $end)
    {
        $db = Yii::$app->db;
        $getListBooked = $db->createCommand("
            SELECT 
                b.tgl_antrian::date,
                a.jadwaldokter_id,
                a.pegawai_id,
                COUNT(*) as booked
            FROM jadwaldokter_m a
            JOIN antrian_t b ON a.jadwaldokter_id = b.jadwaldokter_id
            WHERE b.slot_sequence IS NOT NULL 
                AND b.tgl_antrian::date BETWEEN  '{$start}' AND '{$end}'
                AND a.pegawai_id = {$pegawaiId}
            GROUP BY b.tgl_antrian::date,  a.jadwaldokter_id, a.pegawai_id
        ")->queryAll();

        $listDataBooked = [];

        if (!empty($getListBooked)) {
            foreach ($getListBooked as $value) {
                $tglAntrian = isset($value['tgl_antrian']) ? $value['tgl_antrian'] : null;
                $jadwalId = isset($value['jadwaldokter_id']) ? $value['jadwaldokter_id'] : null;
                if (!empty($jadwalId) && !empty($tglAntrian)) {
                    $listDataBooked[$jadwalId][$tglAntrian] = !empty($value['booked']) ? $value['booked'] : 0;
                }
            }
        }

        return $listDataBooked;
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

    private function getChangingSchedule()
    {
        $listChangeSchedule = [];
        $today = date('Y-m-d', strtotime('NOW'));
        $db = Yii::$app->db;
        $getChangeSchedule = $db->createCommand(
            "SELECT jadwaldokter_id, min(jam_mulai) as jam_mulai, max(jam_selesai) as jam_selesai FROM slotjadwaldokter_r WHERE created_date::date = :tgl group by jadwaldokter_id"
        )
        ->bindParam(':tgl', $today)
        ->queryAll();

        if(count($getChangeSchedule) > 0) {
            $listChangeSchedule = ArrayHelper::index($getChangeSchedule, function ($element) {
                return $element['jadwaldokter_id'];
            });
        }

        return $listChangeSchedule;
    }

    private function slotNormal($payload, $data)
    {
        $db = Yii::$app->db;
        $getDataSchedule = $db->createCommand("
            SELECT 
                a.jadwaldokter_id,
                a.pegawai_id,
                b.hari,
                c.slot_sequence,
                c.jam_mulai,
                c.jam_selesai,
                CASE WHEN c.slot_type = true THEN 1 ELSE 2 END as slot_type,
                CASE WHEN d.jadwaldokter_id IS NOT NULL THEN 1 ELSE 0 END as slot_status,
                c.slotjadwaldokter_id,
                a.kuota_online - count(e.jadwaldokter_id) as kuota_tersedia
            FROM jadwaldokter_m a
            JOIN jadwalbukapoli_m b ON a.jadwalbukapoli_id = b.jadwalbukapoli_id
            JOIN slotjadwaldokter_m c ON c.jadwaldokter_id = a.jadwaldokter_id
            LEFT JOIN (
                SELECT 
                    antrian_t.jadwaldokter_id 
                FROM antrian_t
				JOIN pendaftaranol_t b ON antrian_t.antrian_id = b.antrian_id
                WHERE tgl_antrian::date = '{$payload->slot_date}'
                AND b.status_daftar_ol != 566 AND b.is_active = TRUE AND b.is_deleted = FALSE
                AND slot_sequence = {$payload->slot_sequence}
            ) d ON d.jadwaldokter_id = a.jadwaldokter_id
            LEFT JOIN (
                SELECT 
                    a.jadwaldokter_id ,
					b.pendaftaranol_id
                FROM antrian_t a
				JOIN pendaftaranol_t b ON a.antrian_id = b.antrian_id
                WHERE a.tgl_antrian::date = '{$payload->slot_date}' AND
                b.status_daftar_ol != 566 AND b.is_active = TRUE AND b.is_deleted = FALSE
            ) e ON e.jadwaldokter_id = a.jadwaldokter_id
            WHERE a.is_deleted = false 
            AND a.is_active = true 
            AND b.hari IN ({$data['daysSchedule']})
            AND a.is_loaddokter = true
            AND a.jumlah_loaddokter IS NOT NULL
            AND a.pegawai_id = {$payload->slot_doctor_id}
            AND c.slot_sequence = {$payload->slot_sequence}
            AND c.slot_type = {$data['slotType']}
            AND c.jam_mulai = '{$data['start']}'
            AND c.jam_selesai = '{$data['end']}'
            GROUP BY  a.jadwaldokter_id,
            a.pegawai_id,
            b.hari,
            c.slot_sequence,
            c.jam_mulai,
            c.jam_selesai,
            c.slot_type,
            d.jadwaldokter_id,
            c.slotjadwaldokter_id
        ")->queryOne();

        return $getDataSchedule;
    }

    private function slotChangeSchedule($payload, $data)
    {
        $db = Yii::$app->db;
        $getDataSchedule = $db->createCommand("
            SELECT 
                a.jadwaldokter_id,
                a.pegawai_id,
                b.hari,
                c.slot_sequence,
                c.jam_mulai,
                c.jam_selesai,
                CASE WHEN c.slot_type = true THEN 1 ELSE 2 END as slot_type,
                CASE WHEN d.jadwaldokter_id IS NOT NULL THEN 1 ELSE 0 END as slot_status,
                c.slotjadwaldokter_id,
                kr.kuota_masuk - count(e.jadwaldokter_id) as kuota_tersedia
            FROM jadwaldokter_m a
            JOIN jadwalbukapoli_m b ON a.jadwalbukapoli_id = b.jadwalbukapoli_id
            JOIN slotjadwaldokter_r c ON c.jadwaldokter_id = a.jadwaldokter_id
            LEFT JOIN (
                SELECT 
                    jadwaldokter_id 
                FROM antrian_t
                WHERE tgl_antrian::date = '{$payload->slot_date}'
                        AND slot_sequence = {$payload->slot_sequence}
            ) d ON d.jadwaldokter_id = a.jadwaldokter_id
            LEFT JOIN kuotadokter_r kr ON kr.jadwaldokter_id = a.jadwaldokter_id
            LEFT JOIN (
                SELECT 
                a.jadwaldokter_id ,
                                    b.pendaftaranol_id
            FROM antrian_t a
                            JOIN pendaftaranol_t b ON a.antrian_id = b.antrian_id
            WHERE a.tgl_antrian::date = '{$payload->slot_date}' AND
            b.status_daftar_ol != 566 AND b.is_active = TRUE AND b.is_deleted = FALSE
            ) e ON e.jadwaldokter_id = a.jadwaldokter_id
            WHERE a.is_deleted = false 
            AND a.is_active = true 
            AND b.hari IN ({$data['daysSchedule']})
            AND a.is_loaddokter = true
            AND a.jumlah_loaddokter IS NOT NULL
            AND a.pegawai_id = {$payload->slot_doctor_id}
            AND c.slot_sequence = {$payload->slot_sequence}
            AND c.slot_type = {$data['slotType']}
            AND c.jam_mulai = '{$data['start']}'
            AND c.jam_selesai = '{$data['end']}'
            AND kr.is_online = true
            GROUP BY  a.jadwaldokter_id,
            a.pegawai_id,
            b.hari,
            c.slot_sequence,
            c.jam_mulai,
            c.jam_selesai,
            c.slot_type,
            d.jadwaldokter_id,
            c.slotjadwaldokter_id,
            kr.kuota_masuk
        ")->queryOne();

        return $getDataSchedule;
    }

    public function actionGetFirstAvailableSlot($doctor_code = null, $from = null, $to = null)
    {
        $request = Yii::$app->request;
        $tanggal = $request->get('appointment_date');
        $dokterCode = $request->get('doctor_code');
        $startDate = $request->get('appointment_time');

        /**
         * Convert date.
         */
        $getDays = new DateTime($tanggal);
        $daysSchedule = DocoHelpers::mappDaysFromLookup($getDays->format('w'));
        
        $dataPegawai = Pegawai::find()->where(['nomorindukpegawai' => $dokterCode])->one();

        if (empty($dataPegawai)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => 'Pegawai Tidak Ditemukan !'], 404);
        }

        $pegawaiId = $dataPegawai['pegawai_id'];

        /**
         * Get slot yang belum terisi
         */
        $slotBelumTerisi = $this->getSlotBelumTerisi($tanggal, $dokterCode, $pegawaiId, $daysSchedule, $startDate);
        if (empty($slotBelumTerisi)) {
            return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => 'Slot Tidak Ditemukan !'], 404);
        }

        return [
            'slotBelumTerisi' => isset($slotBelumTerisi[0]) ? $slotBelumTerisi[0] : []
        ];
    }


    /**
     * Function ini untuk mengambil data slot yang belum terisi 
     * berdasarkan komparasi slot yang sudah terisi dan belum terisi
     * 
     * @param string $tanggal
     * @param string $dokterCode
     * @param int $pegawaiId
     * @param int $days
     * 
     * @return array
     */
    private function getSlotBelumTerisi($tanggal, $dokterCode, $pegawaiId, $days, $startDate)
    {
        $querySelect = "
            SELECT 
                a.jadwaldokter_id,
                c.slotjadwaldokter_id
            FROM jadwaldokter_m a
            JOIN jadwalbukapoli_m b ON a.jadwalbukapoli_id = b.jadwalbukapoli_id
            JOIN slotjadwaldokter_m c ON c.jadwaldokter_id = a.jadwaldokter_id
            LEFT JOIN (
                SELECT 
                    jadwaldokter_id 
                FROM antrian_t
                WHERE tgl_antrian::date = :tanggal
            ) d ON d.jadwaldokter_id = a.jadwaldokter_id
            LEFT JOIN (
                SELECT 
                    a.jadwaldokter_id ,
					b.pendaftaranol_id
                FROM antrian_t a
				JOIN pendaftaranol_t b ON a.antrian_id = b.antrian_id
                WHERE a.tgl_antrian::date = :tanggal AND
                b.status_daftar_ol != 566 AND b.is_active = TRUE AND b.is_deleted = FALSE
            ) e ON e.jadwaldokter_id = a.jadwaldokter_id
            WHERE a.is_deleted = false 
            AND a.is_active = true 
            AND b.hari IN (:days)
            AND a.is_loaddokter = true
            AND a.jumlah_loaddokter IS NOT NULL
            AND a.pegawai_id = :pegawai_id
            AND c.slot_type = false
            AND c.jam_mulai >= :minimumdate
            AND c.slotjadwaldokter_id NOT IN (
                SELECT 
                    slotjadwaldokter_m.slotjadwaldokter_id
                FROM
                    pendaftaranol_t  
                    JOIN antrian_t ON antrian_t.antrian_id = pendaftaranol_t.antrian_id
                    JOIN slotjadwaldokter_m ON slotjadwaldokter_m.slot_sequence = antrian_t.slot_sequence
                    JOIN pegawai_m ON pegawai_m.pegawai_id = pendaftaranol_t.pegawai_id 
                WHERE pendaftaranol_t.tgl_pendaftaranol::DATE = :tanggal
                    AND lower(pegawai_m.nomorindukpegawai) LIKE :nip
                    AND pendaftaranol_t.status_daftar_ol IN ( 565, 564 ) 
                    AND pendaftaranol_t.is_active = TRUE 
                    AND pendaftaranol_t.is_deleted = FALSE 
                    AND slotjadwaldokter_m.jadwaldokter_id = antrian_t.jadwaldokter_id
            )
            GROUP BY a.jadwaldokter_id,
                a.pegawai_id,
                b.hari,
                c.slot_sequence,
                c.jam_mulai,
                c.jam_selesai,
                c.slot_type,
                d.jadwaldokter_id,
                c.slotjadwaldokter_id
            ORDER BY c.slot_sequence asc
            LIMIT 1
        ";

        return Yii::$app->db->createCommand($querySelect)
            ->bindValue(':tanggal', $tanggal)
            ->bindValue(':days', $days)
            ->bindValue(':pegawai_id', $pegawaiId)
            ->bindValue(':nip', $dokterCode)
            ->bindValue(':minimumdate', $startDate)
            ->queryAll();
    }
}