<?php

use yii\db\Migration;

/**
 * Class m190710_042736_riwayatalergi_v
 */
class m190710_042736_riwayatalergi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
         DROP VIEW if exists public.riwayatalergi_v;
              ');

        $this->execute("
        CREATE OR REPLACE VIEW public.riwayatalergi_v AS 
 SELECT pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    string_agg((
        CASE COALESCE(anamnesa_t.riwayat_alergiobat, ''::text)
            WHEN ''::text THEN ''::text
            ELSE COALESCE(anamnesa_t.riwayat_alergiobat, ''::text)
        END ||
        CASE COALESCE(asesmenperawatrd_t.alergi_obat, ''::text)
            WHEN ''::text THEN ''::text
            ELSE COALESCE(asesmenperawatrd_t.alergi_obat, ''::text)
        END) ||
        CASE COALESCE(asesmenmedis_t.r_alergiobat, ''::character varying::text)
            WHEN ''::text THEN ''::character varying::text
            ELSE COALESCE(asesmenmedis_t.r_alergiobat, ''::character varying::text)
        END, '-'::text) AS riwayat_alergi
   FROM pasien_m
     LEFT JOIN pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id
     LEFT JOIN anamnesa_t ON pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id
     LEFT JOIN asesmenperawatrd_t ON pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id
  GROUP BY pasien_m.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien;
              ");

        $this->execute('
         ALTER TABLE public.riwayatalergi_v
  OWNER TO postgres;
              ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190710_042736_riwayatalergi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190710_042736_riwayatalergi_v cannot be reverted.\n";

        return false;
    }
    */
}
