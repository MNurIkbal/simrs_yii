<?php

use yii\db\Migration;

/**
 * Class m190327_095736_infojadwaldokter_v
 */
class m190327_095736_infojadwaldokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
          $this->execute('
           DROP VIEW if exists public.antrian_v;
        ');

          $this->execute('
           DROP VIEW if exists public.infojadwaldokter_v;
        ');

        $this->execute("
            CREATE OR REPLACE VIEW public.infojadwaldokter_v AS 
 SELECT jadwaldokter_m.jadwaldokter_id,
    jadwaldokter_m.ruangan_id,
    jadwaldokter_m.instalasi_id,
    jadwaldokter_m.pegawai_id,
    ruangan_m.ruangan_nama,
    pegawai_m.nama_pegawai,
    jadwaldokter_m.jadwaldokter_hari,
    concat(jadwaldokter_m.jadwaldokter_mulai, '-', jadwaldokter_m.jadwaldokter_tutup) AS \"Waktu\",
    jadwaldokter_m.maximumantrian AS kuota,
    jadwaldokter_m.jadwaldokter_mulai AS waktu_mulai,
    jadwaldokter_m.jadwaldokter_tutup AS waktu_selesai,
    jadwaldoktertambahan_m.kuota_penambahan,
    jadwaldokter_m.maximumantrian::double precision + jadwaldoktertambahan_m.kuota_penambahan::double precision AS total_kuota,
    hari.lookup_name AS hari,
    jadwalbukapoli_m.hari AS hari_jadwalbuka,
    jadwaldokter_m.kuota_online,
    jadwaldokter_m.jadwaldokter_tgl,
    pegawai_m.dokter_id,
    ruangan_m.poliklinik_id,
        CASE
            WHEN jadwalbukapoli_m.shift_id IS NULL THEN 0
            ELSE jadwalbukapoli_m.shift_id
        END AS shift_id,
    shift_m.shift1_id,
    jadwaldokter_m.notifikasi_id,
    notifikasi_m.judul_temp,
    notifikasi_m.notifikasi,
    COALESCE(kuotadokter_r.kuota_tersedia, 0::real) AS kuota_tersedia,
    jadwaldokter_m.is_active,
    kuotadokter_r.kuotadokter_id,
    jadwaldokter_m.jadwalbukapoli_id
   FROM jadwaldokter_m
     JOIN ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON jadwaldokter_m.instalasi_id = instalasi_m.instalasi_id
     JOIN pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN jadwaldoktertambahan_m ON jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id
     JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
     LEFT JOIN lookup_m hari ON jadwalbukapoli_m.hari = hari.lookup_id
     LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
     LEFT JOIN notifikasi_m ON jadwaldokter_m.notifikasi_id = notifikasi_m.notifikasi_id
     LEFT JOIN kuotadokter_r ON jadwaldokter_m.jadwaldokter_id = kuotadokter_r.jadwaldokter_id AND kuotadokter_r.is_online
  WHERE jadwaldokter_m.is_deleted = false AND jadwaldokter_m.is_active = true;
        ");
        
        $this->execute('
            ALTER TABLE infojadwaldokter_v
              OWNER TO postgres;
        ');

                $this->execute("
                  CREATE VIEW public.antrian_v AS  SELECT antrian_t.antrian_id,
    antrian_t.no_antrian,
    antrian_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.no_telepon_pasien,
    antrian_t.ruangan_id,
    ruangan_m.ruangan_nama,
    antrian_t.carabayar_id,
    carabayar_m.carabayar_nama,
    antrian_t.penjamin_id,
    penjamin_m.penjamin_nama,
    antrian_t.pendaftaran_id,
    antrian_t.layarantrian_id,
    layarantrian_m.layarantrian_nama,
    antrian_t.loket_id,
    loket_m.loket_nama,
    antrian_t.panggilan_ke,
    antrian_t.tgl_antrian,
    antrian_t.status_antrian,
        CASE
            WHEN (antrian_t.status_antrian = 0) THEN 'Belum Panggil'::text
            WHEN (antrian_t.status_antrian = 1) THEN 'Panggil'::text
            WHEN (antrian_t.status_antrian = 2) THEN 'Lewati'::text
            ELSE 'Batal'::text
        END AS stat_antrian,
    antrian_t.status_pasien,
    status_pasien.lookup_name AS stat_pasien,
    antrian_t.racikan_id,
    racikan_m.racikan_nama,
    pegawai_m.nama_pegawai,
    concat(gelardepan.lookup_name, ' ', pegawai_m.nama_pegawai, ' ', gelarbelakang.gelarbelakang_nama) AS nama_pegawai_lengkap,
    group_carabayar.lookup_name AS namagroupcarabayar,
    antrian_t.jenisantrian_id,
    pegawai_m.dokter_id,
    ruangan_m.poliklinik_id,
    infojadwaldokter_v.shift_id,
    antrian_t.is_online,
    antrian_t.fungsiantrian_id,
    fungsi_antrian.lookup_name AS fungsi_nama,
    instalasi.instalasi_nama,
    antrian_t.antrian_farmasi,
        CASE
            WHEN (antrian_farmasi.lookup_name IS NULL) THEN ('Belum Proses'::text)::character varying
            ELSE antrian_farmasi.lookup_name
        END AS stat_antrian_farmasi,
        CASE
            WHEN (antrian_t.antrian_farmasi = 584) THEN 'Siap Ambil'::text
            WHEN (antrian_t.antrian_farmasi = 585) THEN 'Selesai'::text
            WHEN (antrian_t.antrian_farmasi = 586) THEN 'Selesai'::text
            ELSE 'Proses'::text
        END AS stat_proses_antrian_farmasi,
    antrian_t.is_appointment,
    pendaftaran_t.no_pendaftaran,
    pendaftaranol_t.tgl_pendaftaranol,
    antrian_t.panggil_flag,
    pegawai_m.pegawai_id,
    ruangan_m.ruangan_urutan
   FROM ((((((((((((((((((antrian_t
     LEFT JOIN ruangan_m ON ((antrian_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN carabayar_m ON ((antrian_t.antrian_id = carabayar_m.carabayar_id)))
     LEFT JOIN layarantrian_m ON ((antrian_t.layarantrian_id = layarantrian_m.layarantrian_id)))
     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
     LEFT JOIN lookup_m status_pasien ON ((antrian_t.status_pasien = status_pasien.lookup_id)))
     LEFT JOIN pasien_m ON ((antrian_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN penjamin_m ON ((antrian_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN racikan_m ON ((antrian_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN pegawai_m ON ((antrian_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN lookup_m gelardepan ON (((pegawai_m.gelardepan)::integer = gelardepan.lookup_id)))
     LEFT JOIN gelarbelakang_m gelarbelakang ON (((pegawai_m.gelarbelakang)::integer = gelarbelakang.gelarbelakang_id)))
     LEFT JOIN lookup_m group_carabayar ON ((antrian_t.groupcarabayar_id = group_carabayar.lookup_id)))
     LEFT JOIN infojadwaldokter_v ON ((antrian_t.jadwaldokter_id = infojadwaldokter_v.jadwaldokter_id)))
     LEFT JOIN lookup_m fungsi_antrian ON ((antrian_t.fungsiantrian_id = fungsi_antrian.lookup_id)))
     LEFT JOIN instalasi_m instalasi ON ((antrian_t.instalasi_id = instalasi.instalasi_id)))
     LEFT JOIN lookup_m antrian_farmasi ON ((antrian_t.antrian_farmasi = antrian_farmasi.lookup_id)))
     LEFT JOIN pendaftaran_t ON ((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pendaftaranol_t ON ((antrian_t.antrian_id = pendaftaranol_t.antrian_id)));
                  ");

     $this->execute('
            ALTER TABLE public.antrian_v OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_095736_infojadwaldokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_095736_infojadwaldokter_v cannot be reverted.\n";

        return false;
    }
    */
}
