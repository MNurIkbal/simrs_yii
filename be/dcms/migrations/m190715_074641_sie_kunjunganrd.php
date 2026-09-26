<?php

use yii\db\Migration;

/**
 * Class m190715_074641_sie_kunjunganrd
 */
class m190715_074641_sie_kunjunganrd extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW if exists public.sie_kunjunganrd;
        ');

        $this->execute("
          CREATE OR REPLACE VIEW public.sie_kunjunganrd AS 
 SELECT x.no_pendaftaran,
    x.tgl_pendaftaran,
    x.tahun,
    x.bulan,
    x.tanggal,
    x.instalasi_id,
    x.instalasi_nama,
    x.no_rm,
    x.nama_pasien,
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
          WHERE pendaftaran_t.instalasi_id = 2 AND pendaftaran_t.is_deleted = false) x;
        ");

        $this->execute('
          ALTER TABLE public.sie_kunjunganrd
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_074641_sie_kunjunganrd cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_074641_sie_kunjunganrd cannot be reverted.\n";

        return false;
    }
    */
}
