<?php

namespace Integrasi\Service\Sirs\BussinesLogic;

use Yii;
use Integrasi\Service\Sirs\Models\SlotJadwalDokter;
use Integrasi\Service\Sirs\Models\SlotJadwalDokterR;
use Integrasi\Service\Sirs\Models\JadwalDokter;
use Integrasi\Components\DocoConstants;
use yii\helpers\ArrayHelper;

class Slot
{
    public $state;
    /**
     * Bussines logic create slot jadwal dokter
     * @param  $id           [description]
     * @param  [type] $userIdentity [description]
     * @return array
     */
    public function execute($user, $primaryId = null, $additional = [])
    {
        $this->state = ArrayHelper::getValue($additional, 'state');
        $qJadwal = JadwalDokter::find()
                ->selectAttr();

        if (!empty($primaryId)) {
            $qJadwal->findByJadwalId($primaryId);
        } else {
            $qJadwal->orderByPrimary();
        }

        $data = $qJadwal->getRowArray();
        $listSlot = [];
        $db = Yii::$app->db;
        
        if (!empty($data['is_loaddokter']) && !empty($data['jumlah_loaddokter'])) {
            $primaryId = $data['jadwaldokter_id'];
            $jmlLoad = $data['jumlah_loaddokter'];
            $mulai = $data['jadwaldokter_mulai'];
            $selesai = $data['jadwaldokter_tutup'];
            $rangeMinutes = $this->getMinutesFromRange($mulai, $selesai);
            $getSlot = $rangeMinutes / $jmlLoad;
            $slotType = $data['is_bersedia'] == true ? false : true;
            for ($x=1; $x <= $getSlot; $x++) {
                $row = [
                    'slot_sequence' => $x,
                    'jadwaldokter_id' => $primaryId,
                    'jam_mulai' => $mulai,
                    'jam_selesai' => date('H:i:s', strtotime("+{$jmlLoad} minutes" , strtotime( $mulai ))),
                    'slot_type' => $slotType,
                    'created_by' => $user['uid'],
                    'created_date' => date('Y-m-d H:i:s'),
                    'is_active' => true,
                ];

                $mulai = $row['jam_selesai'];
                $listSlot[] = $row;
            }
            if (!empty($listSlot)) {
                if($this->state == DocoConstants::STATE_UPDATE) {
                    $today = date('Y-m-d', strtotime('NOW'));
                    $listSlotOld = [];
                    $exsistingOldSlot = $db->createCommand("SELECT count(slotjadwaldokter_id) as data FROM slotjadwaldokter_r WHERE jadwaldokter_id = :id AND created_date::DATE = :tgl")
                    ->bindParam(':id', $primaryId)
                    ->bindParam(':tgl', $today)
                    ->queryOne();

                    if($exsistingOldSlot['data'] < 1) {
                        $commandSlotOld = Yii::$app->db->createCommand("SELECT * FROM slotjadwaldokter_m WHERE jadwaldokter_id = :id")
                        ->bindParam(':id', $primaryId);
                        $dataSlot = $commandSlotOld->queryAll();
                        foreach($dataSlot as $val) {
                            $rowData = [
                                'slotjadwaldokter_id' => $val['slotjadwaldokter_id'],
                                'slot_sequence' => $val['slot_sequence'],
                                'jadwaldokter_id' => $val['jadwaldokter_id'],
                                'jam_mulai' => $val['jam_mulai'],
                                'jam_selesai' => $val['jam_selesai'],
                                'slot_type' => $val['slot_type'],
                                'created_by' => $user['uid'],
                                'created_date' => date('Y-m-d H:i:s'),
                                'is_active' => true,
                            ];
                            $listSlotOld[] = $rowData;
                        }
                        // $db->createCommand("DELETE FROM slotjadwaldokter_r WHERE jadwaldokter_id = {$primaryId}")->execute();
                        SlotJadwalDokterR::batchInsert($listSlotOld);
                    }
                }
                $db->createCommand("DELETE FROM slotjadwaldokter_m WHERE jadwaldokter_id = {$primaryId}")->execute();
                SlotJadwalDokter::batchInsert($listSlot);
            }
        }
        return $listSlot;
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

}