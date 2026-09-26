<?php

use yii\db\Migration;

/**
 * Class m190912_061924_infopasienrskoreksidetail_v
 */
class m190912_061924_infopasienrskoreksidetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienrskoreksidetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienrskoreksidetail_v AS 
 SELECT 'RJ'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmorbiditas_t.pasienmorbiditas_id AS diagnosapasien_id,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_masuk,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_utama,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 3 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_penyerta,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 6 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
UNION ALL
 SELECT 'RD'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    NULL::integer AS pasienadmisi_id,
    cppt_t.cppt_id AS diagnosapasien_id,
    NULL::json AS diagnosa_masuk,
    cppt_t.a_diag_utama AS diagnosa_utama,
    cppt_t.a_diag_penyerta AS diagnosa_penyerta,
    NULL::json AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN ( SELECT cppt_t_1.cppt_id,
            cppt_t_1.pendaftaran_id,
            cppt_t_1.pasienadmisi_id,
            cppt_t_1.a_diag_utama,
            cppt_t_1.a_diag_penyerta
           FROM cppt_t cppt_t_1
             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                    cppt_last.pendaftaran_id,
                    cppt_last.pasienadmisi_id
                   FROM cppt_t cppt_last
                  WHERE cppt_last.is_deleted = false AND cppt_last.pendaftaran_id = 660
                  GROUP BY cppt_last.pendaftaran_id, cppt_last.pasienadmisi_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
UNION ALL
 SELECT 'RI'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    resumemedisri_t.resumemedisri_id AS diagnosapasien_id,
    resumemedisri_t.diag_masuk AS diagnosa_masuk,
    resumemedisri_t.diag_utama AS diagnosa_utama,
    resumemedisri_t.diag_penyerta AS diagnosa_penyerta,
    resumemedisri_t.prosedur_diag AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id;
");

        $this->execute('ALTER TABLE public.infopasienrskoreksidetail_v
  OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190912_061924_infopasienrskoreksidetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190912_061924_infopasienrskoreksidetail_v cannot be reverted.\n";

        return false;
    }
    */
}
