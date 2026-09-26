<?php

use yii\db\Migration;

/**
 * Class m221018_033455_migrate_helper_function_sp_batal_periksa
 */
class m221018_033455_migrate_helper_function_sp_batal_periksa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."sp_batal_periksa"("xno_pendaftaran" varchar, "xalasan_batal" text)
  RETURNS TABLE("status" int4, "message" text) AS $BODY$
DECLARE 
    vstatus int4;
    vmessage TEXT;
    vpendaftaran_id int4;
    vpasienbatalperiksa_id int4;
    
BEGIN
    SELECT 
            pendaftaran_id INTO vpendaftaran_id
        FROM pendaftaran_t
        WHERE no_pendaftaran = xno_pendaftaran;
    
    IF NOT EXISTS (
            SELECT 
                1
            FROM pendaftaran_t
            WHERE no_pendaftaran = xno_pendaftaran
            LIMIT 1
    ) 
    THEN
        vstatus := 1;
        vmessage := CONCAT(\'No Pendaftaran \' , xno_pendaftaran , \' Tidak Ditemukan\') ;
    ELSE 
        IF EXISTS (
            SELECT 1
            FROM pembayaran_t 
            WHERE pendaftaran_id = vpendaftaran_id
            AND is_deleted IS FALSE
            LIMIT 1
        )
        THEN
            vstatus := 1;
            vmessage := CONCAT(\'No Pendaftaran \' , xno_pendaftaran , \' Tidak Ditemukan\') ;
        ELSE
            INSERT INTO pasienbatalperiksa_t(
                pendaftaran_id,
                tgl_batal,
                alasan_batal,
                created_by,
                created_date
            )VALUES(
                vpendaftaran_id,
                CURRENT_DATE,
                xalasan_batal,
                1,
                CURRENT_TIMESTAMP
            ) RETURNING pasienbatalperiksa_id INTO vpasienbatalperiksa_id; 
            
            UPDATE pendaftaran_t
            SET status_periksa = 628,
                    pasienbatalperiksa_id = vpasienbatalperiksa_id,
                    last_modified_by = 1,
                    last_modified_date = CURRENT_TIMESTAMP
            WHERE pendaftaran_id = vpendaftaran_id;
            
            UPDATE tindakanpelayanan_t
            SET is_deleted = TRUE,
                    deleted_date = CURRENT_TIMESTAMP,
                    deleted_by = 1
            WHERE pendaftaran_id = vpendaftaran_id
            AND is_deleted IS FALSE 
            AND tindakansudahbayar_id IS NULL;
            
            UPDATE obatalkespasien_t
            SET is_deleted = TRUE,
                    deleted_date = CURRENT_TIMESTAMP,
                    deleted_by = 1
            WHERE pendaftaran_id = vpendaftaran_id
            AND is_deleted IS FALSE 
            AND tindakansudahbayar_id IS NULL;
            
            vstatus := 0;
            vmessage := \'Success\';
        END IF;
    END IF;
    
    RETURN QUERY 
    SELECT vstatus, vmessage;
        
END; $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221018_033455_migrate_helper_function_sp_batal_periksa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221018_033455_migrate_helper_function_sp_batal_periksa cannot be reverted.\n";

        return false;
    }
    */
}
