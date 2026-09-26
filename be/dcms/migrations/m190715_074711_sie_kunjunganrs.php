<?php

use yii\db\Migration;

/**
 * Class m190715_074711_sie_kunjunganrs
 */
class m190715_074711_sie_kunjunganrs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW if exists public.sie_kunjunganrs;
        ');

        $this->execute("
          CREATE OR REPLACE VIEW public.sie_kunjunganrs AS 
 SELECT x.kunjungan,
    x.no_pendaftaran,
    x.tgl_pendaftaran,
    x.tahun,
    x.bulan,
    x.tanggal,
    x.instalasi_id,
    x.instalasi_nama,
    x.ruangan_id,
    x.ruangan_nama,
    x.carabayar_id,
    x.carabayar_nama,
    x.no_rm,
    x.nama_pasien,
    x.status_periksa,
    x.status_periksa_nama
   FROM ( SELECT 'RJ'::text AS kunjungan,
            pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text) AS tahun,
            to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text) AS bulan,
            to_char(pendaftaran_t.tgl_pendaftaran, 'DD'::text) AS tanggal,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            pasien_m.no_rekam_medik AS no_rm,
            pasien_m.nama_pasien,
            pendaftaran_t.status_periksa::integer AS status_periksa,
            fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
          WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.is_deleted = false
        UNION ALL
         SELECT 'RD'::text AS kunjungan,
            pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text) AS tahun,
            to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text) AS bulan,
            to_char(pendaftaran_t.tgl_pendaftaran, 'DD'::text) AS tanggal,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            pasien_m.no_rekam_medik AS no_rm,
            pasien_m.nama_pasien,
            pendaftaran_t.status_periksa::integer AS status_periksa,
            fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
          WHERE pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.is_deleted = false
        UNION ALL
         SELECT 'RI'::text AS kunjungan,
            pendaftaran_t.no_pendaftaran,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY'::text) AS tahun,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'MM'::text) AS bulan,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'DD'::text) AS tanggal,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            pasien_m.no_rekam_medik AS no_rm,
            pasien_m.nama_pasien,
            pasienadmisi_t.status_ranap,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa_nama
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pasienadmisi_t.is_deleted = false
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id) x;
        ");

        $this->execute('
          ALTER TABLE public.sie_kunjunganrs
        OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_074711_sie_kunjunganrs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_074711_sie_kunjunganrs cannot be reverted.\n";

        return false;
    }
    */
}
