<?php

use yii\db\Migration;

/**
 * Class m190621_103257_sie_kunjunganri_create
 */
class m190621_103257_sie_kunjunganri_create extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
    DROP VIEW IF exists public.sie_kunjunganri;
        ');

        $this->execute("
     CREATE OR REPLACE VIEW public.sie_kunjunganri AS 
 SELECT x.nama_pasien,
    x.no_rm,
    x.instalasi_id,
    x.instalasi_nama,
    x.no_pendaftaran,
    x.tgl_pendaftaran,
    x.status_ranap,
    x.status_ranap_nama
   FROM ( SELECT fgetpasien_nama(pasienadmisi_t.pasien_id) AS nama_pasien,
            fgetpasien_rm(pasienadmisi_t.pasien_id) AS no_rm,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.no_pendaftaran,
            to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
            pasienadmisi_t.status_ranap,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_ranap_nama
           FROM pasienadmisi_t
             LEFT JOIN pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
          WHERE pasienadmisi_t.is_deleted = false AND (pasienadmisi_t.status_ranap <> ALL (ARRAY[453, 487]))) x;
        ");

        $this->execute('
     ALTER TABLE public.sie_kunjunganri
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190621_103257_sie_kunjunganri_create cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190621_103257_sie_kunjunganri_create cannot be reverted.\n";

        return false;
    }
    */
}
