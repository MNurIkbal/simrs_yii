<?php

use yii\db\Migration;

/**
 * Class m190715_074658_sie_kunjunganri
 */
class m190715_074658_sie_kunjunganri extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW if exists public.sie_kunjunganri;
        ');

        $this->execute("
          CREATE OR REPLACE VIEW public.sie_kunjunganri AS 
 SELECT pendaftaran_t.no_pendaftaran,
    to_char(pasienadmisi_t.tgl_pendaftaran, 'YYYY-MM-DD'::text)::date AS tgl_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_ranap_nama
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id AND pasienadmisi_t.is_deleted = false
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id;
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
        echo "m190715_074658_sie_kunjunganri cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_074658_sie_kunjunganri cannot be reverted.\n";

        return false;
    }
    */
}
