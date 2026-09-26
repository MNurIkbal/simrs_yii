<?php

/**
 * @author Randy Vianda Putra
 * @todo All about notication query
 * @copyright 8 November 2018 aweutist
 */

namespace app\modules\v1\models;

use Yii;

class NotifikasiComponent
{
    /**
    * @author Randy Vianda Putra
    * @todo query get list all notif by loginmobile_id
    */
    public function queryListNotifByUser($loginmobile_id)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT
                lm.loginmobile_id,
                lm.player_id,
                po.jadwaldokter_id,
                n.notifikasi_id,
                n.judul_temp,
                n.notifikasi,
                ruangan.ruangan_nama,
                j.instalasi_id,
                instalasi.instalasi_nama,
                j.pegawai_id,
                CONCAT(
                    gelarDepan.lookup_value, 
                    ' ',
                    pegawai.nama_pegawai,
                    ' ',
                    gelarBelakang.gelarbelakang_nama 
                ) as nama_pegawai,
                lookup_hari.lookup_name AS hari_nama,
                to_char(j.jadwaldokter_mulai, 'HH24:MI') as jadwaldokter_mulai,
                to_char(j.jadwaldokter_tutup, 'HH24:MI') as jadwaldokter_tutup
            FROM
                loginmobile_k lm
                JOIN (
                    SELECT
                        pendaftaranol_t.created_by as user_id,
                        pendaftaranol_t.jadwaldokter_id
                    FROM
                        pendaftaranol_t
                    LEFT JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = pendaftaranol_t.pendaftaran_id 
                ) po ON po.user_id = lm.loginmobile_id
                JOIN jadwaldokter_m j ON j.jadwaldokter_id = po.jadwaldokter_id
                JOIN pegawai_m pegawai ON j.pegawai_id = pegawai.pegawai_id
                LEFT JOIN lookup_m gelarDepan ON pegawai.gelardepan::integer = gelarDepan.lookup_id
                LEFT JOIN gelarbelakang_m gelarBelakang ON pegawai.gelarbelakang::integer = gelarBelakang.gelarbelakang_id  
                JOIN ruangan_m ruangan ON j.ruangan_id = ruangan.ruangan_id
                JOIN instalasi_m instalasi ON j.instalasi_id = instalasi.instalasi_id
                JOIN notifikasi_m n ON n.notifikasi_id = j.notifikasi_id
                JOIN jadwalbukapoli_m bukapoli ON bukapoli.jadwalbukapoli_id = j.jadwalbukapoli_id
                JOIN lookup_m lookup_hari ON lookup_hari.lookup_id = bukapoli.hari
            WHERE
                lm.loginmobile_id = {$loginmobile_id}
        ";
        $data = $connection->createCommand($sql)->queryAll();

        return $data;
    }

        /**
    * @author Randy Vianda Putra
    * @todo query get list all notif by day for antrian
    */
    public function queryListNotifByDay($hari)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT
                j.jadwaldokter_id,
                n.notifikasi_id,
                n.judul_temp,
                n.notifikasi,
                ruangan.ruangan_nama,
                j.instalasi_id,
                instalasi.instalasi_nama,
                j.pegawai_id,
                CONCAT(
                    gelarDepan.lookup_value, 
                    ' ',
                    pegawai.nama_pegawai,
                    ' ',
                    gelarBelakang.gelarbelakang_nama 
                ) as nama_pegawai,
                lookup_hari.lookup_name AS hari_nama,
                to_char(j.jadwaldokter_mulai, 'HH24:MI') as jadwaldokter_mulai,
                to_char(j.jadwaldokter_tutup, 'HH24:MI') as jadwaldokter_tutup
            FROM
                jadwaldokter_m j 
                JOIN pegawai_m pegawai ON j.pegawai_id = pegawai.pegawai_id
                LEFT JOIN lookup_m gelarDepan ON pegawai.gelardepan::integer = gelarDepan.lookup_id
                LEFT JOIN gelarbelakang_m gelarBelakang ON pegawai.gelarbelakang::integer = gelarBelakang.gelarbelakang_id  
                JOIN ruangan_m ruangan ON j.ruangan_id = ruangan.ruangan_id
                JOIN instalasi_m instalasi ON j.instalasi_id = instalasi.instalasi_id
                JOIN notifikasi_m n ON n.notifikasi_id = j.notifikasi_id
                JOIN jadwalbukapoli_m bukapoli ON bukapoli.jadwalbukapoli_id = j.jadwalbukapoli_id
                JOIN lookup_m lookup_hari ON lookup_hari.lookup_id = bukapoli.hari
            WHERE
                lookup_hari.lookup_id = {$hari} AND j.notifikasi_id IS NOT NULL
        ";
        $data = $connection->createCommand($sql)->queryAll();

        return $data;
    }
}
