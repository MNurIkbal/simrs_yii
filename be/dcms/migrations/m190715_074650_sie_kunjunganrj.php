<?php

use yii\db\Migration;

/**
 * Class m190715_074650_sie_kunjunganrj
 */
class m190715_074650_sie_kunjunganrj extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW if exists public.sie_kunjunganrj;
        ');

        $this->execute("
          CREATE OR REPLACE VIEW public.sie_kunjunganrj AS 
 SELECT x.no_pendaftaran,
    x.tgl_pendaftaran,
    x.tanggal,
    x.bulan,
    x.tahun,
    x.nama_pasien,
    x.no_rm,
    x.instalasi_id,
    x.instalasi_nama,
    x.status_periksa,
    x.status_periksa_nama
   FROM ( SELECT pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text) AS tahun,
            to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text) AS bulan,
            to_char(pendaftaran_t.tgl_pendaftaran, 'DD'::text) AS tanggal,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasien_m.no_rekam_medik AS no_rm,
            pasien_m.nama_pasien,
            pendaftaran_t.status_periksa,
            fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
          WHERE pendaftaran_t.instalasi_id = 1 AND pendaftaran_t.is_deleted = false
        UNION
         SELECT pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text) AS tahun,
            to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text) AS bulan,
            to_char(pendaftaran_t.tgl_pendaftaran, 'DD'::text) AS tanggal,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasien_m.no_rekam_medik AS no_rm,
            pasien_m.nama_pasien,
            pendaftaran_t.status_periksa,
            fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama
           FROM pendaftaran_t
             JOIN konsulpoli_t ON pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id AND konsulpoli_t.is_deleted = false
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
          WHERE instalasi_m.instalasi_id = 1 AND pendaftaran_t.is_deleted = false) x;
        ");

        $this->execute('
          ALTER TABLE public.sie_kunjunganrj
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_074650_sie_kunjunganrj cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_074650_sie_kunjunganrj cannot be reverted.\n";

        return false;
    }
    */
}
