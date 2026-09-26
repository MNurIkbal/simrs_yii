<?php

namespace Integrasi\Service\Sirs;

use Yii;
use \DateTime;
use \DateInterval;
use \DatePeriod;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Sirs\Models\JadwalCutiView;

class UpdateCutiDokter extends \Integrasi\Contracts\DocoImplement
{
    const STATE_DISBALE = 'disable';
    const STATE_ENABLE = 'enable';
    public static $today;
    public static $oneWeek;
    public static $resultData;
    public static $id;

    public function execute()
    {
        self::$today = date('Y-m-d');
        self::$oneWeek = date('Y-m-d', strtotime('+6 days'));
        self::$resultData = ArrayHelper::getValue($this->result, 'data');
        self::$id = ArrayHelper::getValue($this->result, 'jadwalcuti_id');

        $state = $this->state;
        $isActive = 'false';
        $listJadwalDokterId = [];

        // Get query condition for getting jadwaldokter_id
        if ($state == self::STATE_DISBALE) {
            $condition = $this->disableJadwalDokter();
        } else if ($state == self::STATE_ENABLE) {
            $isActive = 'true';
            $condition = $this->enableJadwalDokter();
        }

        // Get and update jadwal dokter
        if ($condition) {
            $listJadwalDokterId = $this->getJadwalDokter($condition);
            if (!empty($listJadwalDokterId)) {
                $this->updateJadwalDokter($listJadwalDokterId, $isActive);
            }
        }
        $result = [
            'state' => $state,
            'jadwaldokter_id' => $listJadwalDokterId,
        ];

        return $this->setResponse($result);
    }

    public function disableJadwalDokter()
    {
        $condition = null;
        $modelCuti = $this->getJadwalCuti();
        if (!empty(self::$resultData)) {
            $tglAwal = date('Y-m-d', strtotime(self::$resultData['tgl_cuti_awal']));
            $tglAkhir = date('Y-m-d', strtotime(self::$resultData['tgl_cuti_akhir']));
            $modelCuti->where([
                'dokter_id' => self::$resultData['pegawai_id'],
                'ruangan_id' => self::$resultData['ruangan_id'],
            ])->andWhere(['BETWEEN', 'DATE(tgl_cuti_awal)', $tglAwal, $tglAkhir]);
        } else {
            $modelCuti->where([
                'BETWEEN', 
                'DATE(tgl_cuti_awal)', 
                self::$today, 
                self::$oneWeek
            ]);
        }
        $dataCuti = $modelCuti->asArray()->all();

        foreach ($dataCuti as $key => $value) {
            $pegawaiId = $value['dokter_id'];
            $ruanganId = $value['ruangan_id'];
            $tglAwal = date('Y-m-d', strtotime($value['tgl_cuti_awal']));
            $tglAkhir = date('Y-m-d', strtotime($value['tgl_cuti_akhir']));

            // Limit disable jadwal dokter to max one week
            if ($tglAkhir <= self::$oneWeek) {
                $listHari = $this->getHari($tglAwal, $tglAkhir);
            
                if (!$condition) {
                    $condition .= " WHERE";
                } else {
                    $condition .= " OR";
                }
                $condition .= " (a.pegawai_id = '{$pegawaiId}' 
                    AND a.ruangan_id = '{$ruanganId}' 
                    AND b.hari IN (" . implode(', ', $listHari) . ") )
                    AND a.is_active = true";
            }
        }

        return $condition;
    }

    public function enableJadwalDokter()
    {
        $condition = null;
        $yesterday = date('Y-m-d', strtotime(self::$today . '-1 day'));

        // Case delete doctor leave
        if (self::$id) {
            $dataCuti = $this->getJadwalCutiAll(self::$id);
        } else {
            $modelCuti = $this->getJadwalCuti();
            $modelCuti->where(['DATE(tgl_cuti_awal)' => $yesterday]);
            $dataCuti = $modelCuti->asArray()->all();
        }

        foreach ($dataCuti as $key => $value) {
            $pegawaiId = $value['dokter_id'];
            $ruanganId = $value['ruangan_id'];
            
            if (self::$id) {
                $tglAwal = date('Y-m-d', strtotime($value['tgl_cuti_awal']));
                $tglAkhir = date('Y-m-d', strtotime($value['tgl_cuti_akhir']));
                $listHari = $this->getHari($tglAwal, $tglAkhir);
            } else {
                $listHari = $this->getHari($yesterday, $yesterday);
            }
            
            if (!$condition) {
                $condition .= " WHERE";
            } else {
                $condition .= " OR";
            }
            $condition .= " (a.pegawai_id = '{$pegawaiId}' 
                AND a.ruangan_id = '{$ruanganId}' 
                AND b.hari IN (" . implode(', ', $listHari) . ") )
                AND a.is_active = false";
        }

        return $condition;
    }

    public function getJadwalCuti() {
        return JadwalCutiView::find();
    }

    public function getJadwalCutiAll($id) {
        $result = [];
        $query = "
            SELECT *,
                pegawai_id AS dokter_id
            FROM jadwalcuti_m
            WHERE jadwalcuti_id = {$id}
        ";
        $result = Yii::$app->db->createCommand($query)->queryAll();

        return $result;
    }

    public function getHari($tglAwal, $tglAkhir) {
        $result = [];
        $tgl_cuti_awal = new DateTime($tglAwal . " 00:00:00");
        $tgl_cuti_akhir = new DateTime($tglAkhir . " 23:59:59");

        $interval = new DateInterval('P1D');
        $rangeTanggal = new DatePeriod($tgl_cuti_awal, $interval ,$tgl_cuti_akhir);
        foreach($rangeTanggal as $value){
            $hari = DocoConstants::$look_hari[$value->format("N")];
            $result[] = $hari;
        }

        return $result;
    }

    public function getJadwalDokter($queryCondition) {
        $jadwalDokterId = [];
        $query = "
            SELECT 
                a.jadwaldokter_id
            FROM jadwaldokter_m a 
            LEFT JOIN jadwalbukapoli_m b
                ON b.jadwalbukapoli_id = a.jadwalbukapoli_id
        ";
        $query .= $queryCondition;
        $queryAll = Yii::$app->db->createCommand($query)->queryAll();
        if (!empty($queryAll)) {
            foreach ($queryAll as $key => $value) {
                $jadwalDokterId[] = $value['jadwaldokter_id'];
            }
        }
        return $jadwalDokterId;
    }

    public function updateJadwalDokter($listId, $isActive) {
        Yii::$app->db->createCommand("
            UPDATE jadwaldokter_m SET is_active = {$isActive} WHERE jadwaldokter_id IN (" . implode(', ', $listId) . ")
        ")->execute();
    }

    public function setResponse($result)
    {
        return json_encode([
            'service' => 'Sirs-UpdateCutiDokter',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'result' => $result
        ]);
    }
}