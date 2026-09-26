<?php

namespace Doco\components;

use Yii;
use yii\helpers\ArrayHelper;

use Doco\components\DocoHelpers;

use app\modules\v1\models\Antrian;
use app\modules\v1\models\Pendaftaran;


class PendaftaranHelpers
{
    public static function getEstimasiDilayani($pendaftaranol_id, $defaultMillisecond = true)
    {
        $today_id = date('N') + 74;
        $dataPendaftaran = Yii::$app->db->createCommand("
            select
                pt.pasien_id,
                pt.pegawai_id,
                pt.ruangan_id,
                pm.kode_dokter_bpjs ,
                pm.nama_pegawai  ,
                rm.kode_ruangan_bpjs ,
                rm.ruangan_nama,
                pt.tgl_pendaftaranol as tgl_pendaftaran,
                pt.estimasidilayani
            from pendaftaranol_t pt
            join pegawai_m pm on pm.pegawai_id  = pt.pegawai_id
            join ruangan_m rm on rm.ruangan_id = pt.ruangan_id
            where  pt.pendaftaranol_id = {$pendaftaranol_id};
        ")->queryOne();

        $pendaftaran_estimasidilayani = ArrayHelper::getValue($dataPendaftaran,'estimasidilayani');
        $pegawai_id = isset($dataPendaftaran['pegawai_id']) ? $dataPendaftaran['pegawai_id'] : 0 ;
        $ruangan_id = isset($dataPendaftaran['ruangan_id']) ? $dataPendaftaran['ruangan_id'] : 0;
        $tgl_pendaftaran = isset($dataPendaftaran['tgl_pendaftaran']) ? $dataPendaftaran['tgl_pendaftaran'] : 0;
        $waktu_pendaftaran = date('H:i:s', strtotime($tgl_pendaftaran));

        $jadwalDokter = Yii::$app->db->createCommand("
            select
                d.jadwaldokter_id ,
                d.jadwaldokter_mulai ,
                d.jadwaldokter_tutup ,
                d.jumlah_loaddokter as estimasidilayani,
                d.kuota_bpjs_online + d.kuota_bpjs_offline AS kuotajkn,
                d.kuota_nonbpjs_online + d.kuota_nonbpjs_offline AS kuotanonjkn,
                kuotadokter_r.kuota_bpjs_online + kuotadokter_r_offline.kuota_bpjs_offline AS sisakuotajkn,
                kuotadokter_r.kuota_nonbpjs_online + kuotadokter_r_offline.kuota_nonbpjs_offline AS sisakuotanonjkn
            from jadwalbukapoli_m j
                RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id
                RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id
                JOIN ( SELECT a.kuota_bpjs_online,
                        a.kuota_nonbpjs_online,
                        a.jadwaldokter_id,
                        a.is_online
                    FROM kuotadokter_r a) kuotadokter_r ON d.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online = true
                JOIN ( SELECT a.kuota_bpjs_offline,
                        a.kuota_nonbpjs_offline,
                        a.jadwaldokter_id,
                        a.is_online
                    FROM kuotadokter_r a) kuotadokter_r_offline ON d.jadwaldokter_id = kuotadokter_r_offline.jadwaldokter_id AND kuotadokter_r_offline.is_online = false
            where j.ruangan_id = {$ruangan_id}
                and j.hari = {$today_id}
                and p.pegawai_id = {$pegawai_id}
                and d.is_deleted = false and d.is_active = true
                and p.is_deleted = false and p.is_active = true
        ")->queryAll();
        /** Proses selected range tanggal ketika ada 2 shift */
        $endBefore = null;
        $selectedJadwal = [];

        foreach ($jadwalDokter as $key => $value) {
            if (!empty($endBefore)) {
                if (strtotime($waktu_pendaftaran) < strtotime($value['jadwaldokter_mulai'])) {
                    $selectedJadwal = $jadwalDokter[$key-1];
                    break;
                }
            }

            if (strtotime($waktu_pendaftaran) <= strtotime($value['jadwaldokter_mulai'])
                    || (strtotime($waktu_pendaftaran) >= strtotime($value['jadwaldokter_mulai'])
                            && strtotime($waktu_pendaftaran) <= ($value['jadwaldokter_tutup']) )) {
                $selectedJadwal = $value;
            }

            $endBefore = $value['jadwaldokter_tutup'];
        }

        if (empty($selectedJadwal) && !empty($value)) {
            $selectedJadwal = $value;
        }

        $jam_mulai = !empty($selectedJadwal['jadwaldokter_mulai'])
                        ? date('H:i', strtotime($selectedJadwal['jadwaldokter_mulai'])) : '00:00';
        $jam_tutup = !empty($selectedJadwal['jadwaldokter_tutup'])
                        ? date('H:i', strtotime($selectedJadwal['jadwaldokter_tutup'])) : '00:00';

        $tglestimasi = date('Y-m-d', strtotime($tgl_pendaftaran));
        $jamestimasi = date('H:i:s', strtotime($jam_mulai));

        $kuotaJkn = isset($selectedJadwal['kuotajkn']) ? $selectedJadwal['kuotajkn'] : 0;
        $kuotaNonJkn = isset($selectedJadwal['kuotanonjkn']) ? $selectedJadwal['kuotanonjkn'] : 0;
        $totalAntrian = $kuotaJkn + $kuotaNonJkn;

        $sisaKuotaNonJkn = isset($selectedJadwal['sisakuotanonjkn']) ? $selectedJadwal['sisakuotanonjkn'] : 0;
        $sisaKuotaJkn = isset($selectedJadwal['sisakuotajkn']) ? $selectedJadwal['sisakuotajkn'] : 0;
        $sisaAntrian = $sisaKuotaJkn + $sisaKuotaNonJkn;

        $spm = isset($selectedJadwal['estimasidilayani']) ? $selectedJadwal['estimasidilayani'] : 15;
        $waktuestimasi = $tglestimasi . " " . $jamestimasi;
        $noUrut = ($totalAntrian - $sisaAntrian);
        $timestampsecond = (date_create($waktuestimasi)->getTimestamp()) + (
            (($spm * $noUrut) * 60)
        );


        if($defaultMillisecond){
            $estimasidilayani = $timestampsecond * 1000;
        }else{
            $estimasidilayani = date("d-m-Y H:i", ($timestampsecond * 1000) / 1000);
        }

        //replace estimasidilayani dari data yg disimpan
        if(isset($pendaftaran_estimasidilayani) && !empty($pendaftaran_estimasidilayani)){
            if($defaultMillisecond){
                $estimasidilayani = $pendaftaran_estimasidilayani;
            }else{
                $estimasidilayani = date("d-m-Y H:i", $pendaftaran_estimasidilayani / 1000);
            }
        }

        return $estimasidilayani;
    }

    public static function getEstimasiTimeAntrian($array_request)
    {
        $pendaftaran_id = ArrayHelper::getValue($array_request, 'pendaftaran_id');
        $ruangan_id = ArrayHelper::getValue($array_request, 'ruangan_id');
        $pegawai_id = ArrayHelper::getValue($array_request, 'pegawai_id');
        $tgl_pendaftaran = ArrayHelper::getValue($array_request, 'tgl_pendaftaran');
        $antrian_id = ArrayHelper::getValue($array_request, 'antrian_id');

        $today_id = DocoHelpers::getIdHariIni();
        $pendaftaran_ol_estimasidilayani = null;
        if (!$pendaftaran_id) {
            if (!$antrian_id) {
                return false;
            }
            $antrian = Antrian::find()->select(['antrian_id', 'jadwaldokter_id', 'jadwalbukapoli_id', 'tgl_antrian'])
                                ->where(['antrian_id' => $antrian_id])
                                ->andWhere(['jenisantrian_id' => DocoConstants::VAR_JA_P])
                                ->asArray()->one();
            $tgl_pendaftaran = ArrayHelper::getValue($antrian, 'tgl_antrian');

            $pendaftaran_ol_estimasidilayani = Yii::$app->db->createCommand("
                SELECT
                    estimasidilayani
                FROM
                    pendaftaranol_t
                WHERE
                    antrian_id = :antrian_id
            ")->bindValue(':antrian_id',$antrian_id)->queryScalar();
        } else {
            $antrian = Antrian::find()->select(['antrian_id', 'jadwaldokter_id', 'jadwalbukapoli_id', 'tgl_antrian'])
                                ->where(['pendaftaran_id' => $pendaftaran_id])
                                ->andWhere(['jenisantrian_id' => DocoConstants::VAR_JA_P])
                                ->asArray()->one();
        }

        if (($tgl_pendaftaran == null || $ruangan_id == null || $pegawai_id == null) && $pendaftaran_id) {
            $pendaftaran = Pendaftaran::find()->select(['pendaftaran_id', 'tgl_pendaftaran', 'ruangan_id', 'pegawai_id'])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            $tgl_pendaftaran = ArrayHelper::getValue($pendaftaran, 'tgl_pendaftaran');
            $ruangan_id = ArrayHelper::getValue($pendaftaran, 'ruangan_id');
            $pegawai_id = ArrayHelper::getValue($pendaftaran, 'pegawai_id');
        }



        $query_jadwal_dokter = "select
              d.jadwaldokter_id ,
              d.jadwaldokter_mulai ,
              d.jadwaldokter_tutup ,
              d.jumlah_loaddokter as estimasidilayani,
              d.kuota_total
          from jadwalbukapoli_m j
          RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id
          RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id";

        if (ArrayHelper::getValue($antrian, 'jadwaldokter_id') != null) {
            $kondisi_jadwal_dokter = "where d.jadwaldokter_id = '".ArrayHelper::getValue($antrian, 'jadwaldokter_id')."'
                      and d.is_deleted = false and d.is_active = true
                      and p.is_deleted = false and p.is_active = true";
        } else {
            $kondisi_jadwal_dokter = "where j.ruangan_id = {$ruangan_id}
                      and j.hari = {$today_id}
                      and p.pegawai_id = {$pegawai_id}
                      and d.is_deleted = false and d.is_active = true
                      and p.is_deleted = false and p.is_active = true";
        }
        $jadwal_dokter = Yii::$app->db->createCommand($query_jadwal_dokter .' '. $kondisi_jadwal_dokter)->queryOne();
        if ($jadwal_dokter) {
            $start_date = date('Y-m-d', strtotime(ArrayHelper::getValue($antrian, 'tgl_antrian'))); // untuk query hari tgl antrian saja
            $query_antrian = "select count(distinct(at.antrian_id)) as posisi_antrian from
                                (select antrian_id from antrian_t at2
                                    WHERE at2.jadwaldokter_id = '".ArrayHelper::getValue($jadwal_dokter, 'jadwaldokter_id')."'
                                    AND at2.jenisantrian_id = '".DocoConstants::VAR_JA_P."'
                                    AND at2.tgl_antrian::date = '".$start_date."'
                                    AND at2.antrian_id <= '".ArrayHelper::getValue($antrian, 'antrian_id')."'
                                    ORDER BY at2.antrian_id DESC
                                ) as at";
            $posisi_antrian = Yii::$app->db->createCommand($query_antrian)->queryScalar();

            // gak pake array helper, gak tau kenapa gak masuk ke third parameter jika null
            $tglestimasi = date('Y-m-d', strtotime($tgl_pendaftaran));
            $jamestimasi = isset($jadwal_dokter['jadwaldokter_mulai']) ? date('H:i', strtotime($jadwal_dokter['jadwaldokter_mulai'])) : date('H:i', strtotime('00:00'));
            $totalAntrian = (isset($jadwal_dokter['kuotajkn']) ? $jadwal_dokter['kuotajkn'] : 0) + (isset($jadwal_dokter['kuotanonjkn']) ? $jadwal_dokter['kuotanonjkn'] : 0);
            $sisaAntrian = (isset($jadwal_dokter['sisakuotanonjkn']) ? $jadwal_dokter['sisakuotanonjkn'] : 0) + (isset($jadwal_dokter['sisakuotajkn']) ? $jadwal_dokter['sisakuotajkn'] : 0);
            $spm = (isset($jadwal_dokter['estimasidilayani']) ? $jadwal_dokter['estimasidilayani'] : 6);
            $tgl_jam_mulai_dokter = $tglestimasi . " " . $jamestimasi;
            $noUrut = ($totalAntrian - $sisaAntrian);
            $waktuestimasi_mulai_timestamp = (date_create($tgl_jam_mulai_dokter)->getTimestamp()) + (
                (($spm * ($posisi_antrian - 1)) * 60)
            );
            $waktuestimasi_berakhir_timestamp = (date_create($tgl_jam_mulai_dokter)->getTimestamp()) + (
                ($spm * $posisi_antrian * 60)
            );
            //override waktu estimasi dari pendaftaran online
            if(isset($pendaftaran_ol_estimasidilayani) && !empty($pendaftaran_ol_estimasidilayani)){
                $waktuestimasi_mulai_timestamp = $pendaftaran_ol_estimasidilayani/1000;
                $waktuestimasi_berakhir_timestamp = ($pendaftaran_ol_estimasidilayani/1000) + ($spm * 60);
            }

            return [
                'waktuestimasi_mulai' => date('H:i', $waktuestimasi_mulai_timestamp),
                'waktuestimasi_mulai_timestamp' => $waktuestimasi_mulai_timestamp,
                'waktuestimasi_berakhir' => date('H:i', $waktuestimasi_berakhir_timestamp),
                'waktuestimasi_berakhir_timestamp' => $waktuestimasi_berakhir_timestamp,
                'posisi_antrian' => $posisi_antrian,
                'kuota_total' => isset($jadwal_dokter['kuota_total']) ? $jadwal_dokter['kuota_total'] : 0 ,
            ];
        }

        return [
            'waktuestimasi_mulai' => date('H:i', strtotime('00:00')),
            'waktuestimasi_mulai_timestamp' => 0,
            'waktuestimasi_berakhir' => date('H:i', strtotime('00:00')),
            'waktuestimasi_berakhir_timestamp' => 0,
            'posisi_antrian' => 0,
            'kuota_total' => 0,
        ];

    }

}
