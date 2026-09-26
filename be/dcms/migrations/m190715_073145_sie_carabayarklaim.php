<?php

use yii\db\Migration;

/**
 * Class m190715_073145_sie_carabayarklaim
 */
class m190715_073145_sie_carabayarklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
          DROP VIEW IF exists public.sie_carabayarklaim;
              ');

        $this->execute("
          CREATE OR REPLACE VIEW public.sie_carabayarklaim AS 
 SELECT 'RJ/RD'::text AS pengunjung,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE
            WHEN pendaftaran_t.status_verifikasi = 551 THEN 'Sudah Klaim'::text
            ELSE 'Belum Klaim'::text
        END AS status_klaim,
    pendaftaran_t.status_verifikasi,
    fgetnamalookup(pendaftaran_t.status_verifikasi) AS verifikasi
   FROM pendaftaran_t
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
  WHERE pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])
UNION ALL
 SELECT 'RI'::text AS pengunjung,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.tgl_pendaftaran,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE
            WHEN pendaftaran_t.status_verifikasi = 551 THEN 'Sudah Klaim'::text
            ELSE 'Belum Klaim'::text
        END AS status_klaim,
    pendaftaran_t.status_verifikasi,
    fgetnamalookup(pendaftaran_t.status_verifikasi) AS verifikasi
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id;
              ");

        $this->execute('
          ALTER TABLE public.sie_carabayarklaim
        OWNER TO postgres;

              ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190715_073145_sie_carabayarklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190715_073145_sie_carabayarklaim cannot be reverted.\n";

        return false;
    }
    */
}
