<?php

use yii\db\Migration;

/**
 * Class m190621_103508_sie_kunjunganrj_create
 */
class m190621_103508_sie_kunjunganrj_create extends Migration
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
 SELECT x.nama_pasien,
    x.no_rm,
    x.instalasi_id,
    x.instalasi_nama,
    x.no_pendaftaran,
    x.tgl_pendaftaran,
    x.tanggal,
    x.bulan,
    x.tahun,
    x.status_periksa,
    x.status_periksa_nama
   FROM ( SELECT fgetpasien_nama(pendaftaran_t.pasien_id) AS nama_pasien,
            fgetpasien_rm(pendaftaran_t.pasien_id) AS no_rm,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.no_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'DD'::text)::character varying AS tanggal,
            to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text)::character varying AS bulan,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun,
            pendaftaran_t.status_periksa::integer AS status_periksa,
            fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama
           FROM pendaftaran_t
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
          WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1])) AND pendaftaran_t.is_deleted = false AND (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 4, 433]))
        UNION ALL
         SELECT fgetpasien_nama(pendaftaran_t.pasien_id) AS nama_pasien,
            fgetpasien_rm(pendaftaran_t.pasien_id) AS no_rm,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.no_pendaftaran,
            to_char(konsulpoli_t.tgl_konsulpoli, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            to_char(pendaftaran_t.tgl_pendaftaran, 'DD'::text)::character varying AS tanggal,
            to_char(pendaftaran_t.tgl_pendaftaran, 'MM'::text)::character varying AS bulan,
            to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY'::text)::character varying AS tahun,
            pendaftaran_t.status_periksa::integer AS status_periksa,
            fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa_nama
           FROM pendaftaran_t
             JOIN konsulpoli_t ON pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id
             JOIN ruangan_m ON konsulpoli_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
          WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1])) AND pendaftaran_t.is_deleted = false AND (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 4, 433]))) x;
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
        echo "m190621_103508_sie_kunjunganrj_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190621_103508_sie_kunjunganrj_create cannot be reverted.\n";

        return false;
    }
    */
}
