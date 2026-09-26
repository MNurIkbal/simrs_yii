<?php

use yii\db\Migration;

/**
 * Class m230628_063427_migrate_f_batal_ranap_rpp_153
 */
class m230628_063427_migrate_f_batal_ranap_rpp_153 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION public.fbatalranap(vpendaftaran_id integer, vuser_id integer, vketerangan character varying)
        RETURNS TABLE(status boolean, message character varying)
        LANGUAGE plpgsql
       AS \$function\$
       
       DECLARE 
       vstatus BOOLEAN;
       vmessage VARCHAR;
       vpasienadmisi_id int4;
       vkamarruangan_id int4;
       vkamartempattidur_id int4;
       vkamarruangan_jenis int4;
       vstatus_ranap int4;
       vstatus_rujuk_ranap int4;
       vkettempattidur_id int4;
       vpasienbatalperiksa_id int4;
       vprev_pendaftaran_id int4;
       
       BEGIN
           
           vstatus := TRUE;
           vmessage := '';
           vstatus_ranap := 453;
           vstatus_rujuk_ranap := 433;
       
           SELECT 
               pasienadmisi_id, 
               prev_pendaftaran_id 
           INTO vpasienadmisi_id, vprev_pendaftaran_id
           FROM pendaftaran_t
           WHERE pendaftaran_id = vpendaftaran_id;
       
           IF EXISTS (
               SELECT 
                   *
               FROM pasienadmisi_t
               WHERE pasienadmisi_id = vpasienadmisi_id
               AND pasienpulang_id IS NULL
           ) 
           THEN
               SELECT 
                   kamarruangan_id, 
                   kamartempattidur_id
               INTO vkamarruangan_id, vkamartempattidur_id
               FROM pasienadmisi_t
               WHERE pasienadmisi_id = vpasienadmisi_id;
           
           SELECT 
               kamarruangan_jenis 
           INTO vkamarruangan_jenis
           FROM kamarruangan_m
           WHERE kamarruangan_id = vkamarruangan_id;
           
           SELECT 
               kettempattidur_id
           INTO vkettempattidur_id
           FROM kettempattidur_m
           WHERE kamarruangan_jenis = vkamarruangan_jenis
           AND is_kosong IS TRUE;
           
           UPDATE 
               kamartempattidur_m
           SET 
               status_isi = FALSE,
               kettempattidur_id = vkettempattidur_id
           WHERE kamartempattidur_id = vkamartempattidur_id;
           
           INSERT INTO pasienbatalperiksa_t (
               pendaftaran_id,
               pasienadmisi_id,
               tgl_batal,
               alasan_batal,
               created_date,
               created_by
           ) VALUES (
               vpendaftaran_id,
               vpasienadmisi_id, 
               CURRENT_DATE, 
               vketerangan, 
               CURRENT_TIMESTAMP, 
               vuser_id
           ) RETURNING pasienbatalperiksa_id INTO vpasienbatalperiksa_id;
       
           IF vprev_pendaftaran_id IS NOT NULL THEN
               UPDATE 
                   pendaftaran_t
               SET
                   status_periksa = vstatus_ranap,
                   keterangan_pendaftaran = vketerangan,
                   pasienbatalperiksa_id = vpasienbatalperiksa_id,
                   last_modified_by = vuser_id,
                   last_modified_date = CURRENT_DATE,
                   petugas_id = vuser_id,
                   petugas_tgl_pembuat = CURRENT_DATE
               WHERE pendaftaran_id = vpendaftaran_id;
                                   
               UPDATE 
                   pasienadmisi_t
               SET 
                   status_ranap = vstatus_ranap,
                   last_modified_by = vuser_id,
                   last_modified_date = CURRENT_DATE
               WHERE pasienadmisi_id = vpasienadmisi_id;
           ELSE
               UPDATE 
                   pendaftaran_t
               SET 
                   pasienadmisi_id = NULL,
                   status_periksa = vstatus_rujuk_ranap,
                   keterangan_pendaftaran = vketerangan,
                   last_modified_by = vuser_id,
                   last_modified_date = CURRENT_DATE,
                   petugas_id = vuser_id,
                   petugas_tgl_pembuat = CURRENT_DATE
               WHERE pendaftaran_id = vpendaftaran_id;
              
               UPDATE 
                   pasienadmisi_t
               SET 
                   pasienbatalperiksa_id = vpasienbatalperiksa_id,
                   status_ranap = vstatus_ranap
               WHERE pasienadmisi_id = vpasienadmisi_id;
           END IF;
           
           vstatus := TRUE;
           vmessage := '';
       ELSE
           vstatus := FALSE;
           vmessage := 'Pasien Sudah Pulang';
       END IF;
       
       RETURN QUERY
       SELECT vstatus, vmessage;
               
       END; 
       \$function\$
       ;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230628_063427_migrate_f_batal_ranap_rpp_153 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230628_063427_migrate_f_batal_ranap_rpp_153 cannot be reverted.\n";

        return false;
    }
    */
}
