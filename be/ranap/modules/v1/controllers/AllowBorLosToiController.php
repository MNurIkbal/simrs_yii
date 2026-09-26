<?php
/*
 * @Author: metafiliana
 * @Date: 2018-01-29 13:10:59
 * @Last Modified by: Anggoro <tri.anggoro@docotel.com>
 * @Last Modified time: 2019-06-21
 * @Description:
 */

namespace app\modules\v1\controllers;

use Yii;

use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use app\modules\v1\models\HariPerawatan;
use yii\db\Expression;


class AllowBorLosToiController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        return $actions;
    }


    public function actionExecute($tahun = null)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $tahun = !empty($tahun) ? $tahun : date('Y');
            $query = "
                SELECT 
                  pasienadmisi_t.pasienadmisi_id,
                    masukkamar_t.tgl_masukkamar,
                    masukkamar_t.tgl_keluarkamar,
                    pasienadmisi_t.tgl_pulang,
                    masukkamar_t.kamarruangan_id,
                    masukkamar_t.pindahkamar_id,
                    pasienadmisi_t.pendaftaran_id
                FROM masukkamar_t
                JOIN pasienadmisi_t ON masukkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                WHERE date_part('year', tgl_admisi) = {$tahun}
                ORDER BY  pasienadmisi_t.pasienadmisi_id,masukkamar_t.tgl_masukkamar ASC
            ";

            $dataKamar = $connection->createCommand($query)->queryAll();
            $rekapKamar = $backupData = [];
            foreach ($dataKamar as $key => $value) {
                $idRoom = $value['kamarruangan_id'];
                $admisi = $value['pasienadmisi_id'];
                $dateIn = !empty($value['tgl_masukkamar']) ? date('Y-m-d', strtotime($value['tgl_masukkamar'])) : null;
                $dateOut = !empty($value['tgl_keluarkamar']) ? date('Y-m-d', strtotime($value['tgl_keluarkamar'])) : date('Y-m-d');
                $tglPulang = !empty($value['tgl_pulang']) ? date('Y-m-d',strtotime($value['tgl_pulang'])) : null;
                $isMove = !empty($value['pindahkamar_id']) ? $value['pindahkamar_id'] : null;
                $checkMove = false;
                $getMonthIn = date('m',strtotime($dateIn));
                $getMonthOut = date('m', strtotime($dateOut));
                if (!isset($rekapKamar[$admisi][$idRoom])) {
                    $rekapKamar[$admisi][$idRoom] = [
                        'pendaftaran_id' => $value['pendaftaran_id'],
                        'pasienadmisi_id' => $value['pasienadmisi_id'],
                        'kamarruangan_id' => $value['kamarruangan_id'],
                        'periode' => $tahun,
                        'jan' => 0,
                        'feb' => 0,
                        'mar' => 0,
                        'apr' => 0,
                        'mei' => 0,
                        'jun' => 0,
                        'jul' => 0,
                        'agus' => 0,
                        'sept' => 0,
                        'okt' => 0,
                        'nov' => 0,
                        'des' => 0,
                    ];
                }
                if (!isset($backupData[$admisi][$dateOut][$idRoom])) $backupData[$admisi][$dateOut][$idRoom] = true;
                /** Jika Pindah kamar di hari yang sama tanpa ada tanggl pulang */
                if ($dateIn == $dateOut && !empty($isMove)) {
                    $nextData = isset($dataKamar[$key+1]) ? $dataKamar[$key+1] : null;
                    if (!empty($nextData)) {
                        $nextAdmisi = $nextData['pasienadmisi_id'];
                        $nextDateIn = !empty($nextData['tgl_masukkamar']) 
                                ? date('Y-m-d',strtotime($nextData['tgl_masukkamar'])) : null;
                        if ($nextAdmisi == $admisi && $dateOut != $nextDateIn) {
                            $default = 1;
                            if (isset($backupData[$admisi][$dateIn][$idRoom])) $default = 0;
                            $hp = DocoHelpers::convertDateToAge($dateIn,'hari',$dateOut) + $default;
                            $bulanPerawatan = $this->mappMonth((int) $getMonthIn);
                            $rekapKamar[$admisi][$idRoom][$bulanPerawatan] += $hp;
                        }
                    }
                    continue;
                }

                if (!empty($dateIn)) {
                    /** Datang dan Pulang dibulan yang sama atau belum pulang sama sekali */
                    if ($getMonthIn == $getMonthOut) {
                        $default = 1;
                        if (isset($backupData[$admisi][$dateIn][$idRoom]) && $dateOut != date('Y-m-d')) $default = 0;
                        $hp = DocoHelpers::convertDateToAge($dateIn,'hari',$dateOut) + $default;
                        $bulanPerawatan = $this->mappMonth((int) $getMonthIn);
                        $rekapKamar[$admisi][$idRoom][$bulanPerawatan] += $hp;
                    } else {
                        for ($x = $getMonthIn; $x <= $getMonthOut; $x++) {
                            $maxDayIn = date("Y-m-t", strtotime($dateIn));
                            if ($x == $getMonthOut) {
                                $maxDayIn = $dateOut;
                            }
                            $hp = DocoHelpers::convertDateToAge($dateIn,'hari',$maxDayIn) + 1;
                            $bulanPerawatan = $this->mappMonth((int) $x);
                            $rekapKamar[$admisi][$idRoom][$bulanPerawatan] += $hp;
                            $nextMonth = $x + 1;
                            $dateIn = date("Y-{$nextMonth}-01");
                        }

                    }
                }
            }

            $insertRekap = [];
            foreach ($rekapKamar as $detailPasien) {
                foreach ($detailPasien as $detailRekap) {
                    $insertRekap[] = $detailRekap;
                }
            }
            $sqlHapusRekap = "DELETE FROM hariperawatan_r WHERE periode = {$tahun}";
            $result = $connection->createCommand($sqlHapusRekap)->execute();
            if (!empty($insertRekap)) {
                $insert = HariPerawatan::batchInsert($insertRekap);
            }
            $transaction->commit();
            $sql = "SELECT * FROM indikatorrs_r();";

            $result = $connection->createCommand($sql)->queryOne();

            return [
                "msg" => "Cron BOR LOS TOI BTO Berhasil",
                "data" => $result
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['messages' => $e->getMessage(),'status' => 500];
        }
    }

    /**
     * @param  Integer $month 
     * @return String
     */
    private function mappMonth($month)
    {
        $mapp = [
            1 => 'jan',
            2 => 'feb',
            3 => 'mar',
            4 => 'apr',
            5 => 'mei',
            6 => 'jun',
            7 => 'jul',
            8 => 'agus',
            9 => 'sept',
            10 => 'okt',
            11 => 'nov',
            12 => 'des',
        ];

        return isset($mapp[$month]) ? $mapp[$month] : '';
    }
}
