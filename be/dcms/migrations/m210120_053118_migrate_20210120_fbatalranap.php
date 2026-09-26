<?php

use yii\db\Migration;

/**
 * Class m210120_053118_migrate_20210120_fbatalranap
 */
class m210120_053118_migrate_20210120_fbatalranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"fbatalranap\"(\"vpendaftaran_id\" int4, \"vuser_id\" int4, \"vketerangan\" varchar)
  RETURNS TABLE(\"status\" bool, \"message\" varchar) AS \$BODY\$
DECLARE 
vstatus BOOLEAN;
vmessage VARCHAR;
vpasienadmisi_id int4;
vkamarruangan_id int4;
vkamartempattidur_id int4;
vkamarruangan_jenis int4;
vstatus_ranap int4;
vkettempattidur_id int4;
vpasienbatalperiksa_id int4;

BEGIN
    vstatus := TRUE;
    vmessage := '';
    
    vstatus_ranap := 453;
    
    SELECT pasienadmisi_id INTO vpasienadmisi_id
    FROM pendaftaran_t
    WHERE pendaftaran_id = vpendaftaran_id;
    
    IF EXISTS(
        SELECT *
        FROM pasienadmisi_t
        WHERE pasienadmisi_id = vpasienadmisi_id
        AND pasienpulang_id IS NULL
    )
    THEN
        SELECT kamarruangan_id, kamartempattidur_id
        INTO vkamarruangan_id, vkamartempattidur_id
        FROM pasienadmisi_t
        WHERE pasienadmisi_id = vpasienadmisi_id;
        
        SELECT kamarruangan_jenis INTO vkamarruangan_jenis
        FROM kamarruangan_m
        WHERE kamarruangan_id = vkamarruangan_id;
        
        SELECT kettempattidur_id
        INTO vkettempattidur_id
        FROM kettempattidur_m
        WHERE kamarruangan_jenis = vkamarruangan_jenis
        AND is_kosong IS TRUE;
        
        UPDATE pasienadmisi_t
        SET status_ranap = vstatus_ranap,
                last_modified_by = vuser_id,
                last_modified_date = CURRENT_DATE
        WHERE pasienadmisi_id = vpasienadmisi_id;
        
        UPDATE kamartempattidur_m
        SET status_isi = FALSE,
                kettempattidur_id = vkettempattidur_id
        WHERE kamartempattidur_id = vkamartempattidur_id;
        
        INSERT INTO pasienbatalperiksa_t(
            pendaftaran_id , pasienadmisi_id, tgl_batal, alasan_batal, created_date, created_by
        )VALUES(
            vpendaftaran_id, vpasienadmisi_id, CURRENT_DATE, vketerangan, CURRENT_TIMESTAMP, vuser_id
        ) RETURNING pasienbatalperiksa_id INTO vpasienbatalperiksa_id;
        
        UPDATE pendaftaran_t
        SET 
                --status_periksa = vstatus_ranap,
                keterangan_pendaftaran = vketerangan,
                pasienbatalperiksa_id = vpasienbatalperiksa_id,
                last_modified_by = vuser_id,
                last_modified_date = CURRENT_DATE
        WHERE pendaftaran_id = vpendaftaran_id;
        
        vstatus := TRUE;
        vmessage := '';
    ELSE
        vstatus := FALSE;
        vmessage := 'Pasien Sudah Pulang';
    END IF;
    
    RETURN QUERY 
    SELECT vstatus, vmessage;
        
END; \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210120_053118_migrate_20210120_fbatalranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210120_053118_migrate_20210120_fbatalranap cannot be reverted.\n";

        return false;
    }
    */
}
