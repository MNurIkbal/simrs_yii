<?php

use yii\db\Migration;

/**
 * Class m230305_025205_migrate_GB1083_function_sp_merge_rm
 */
class m230305_025205_migrate_GB1083_function_sp_merge_rm extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP FUNCTION IF EXISTS sp_merge_rm(varchar,varchar);
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."sp_merge_rm"("xrm_tujuan" varchar, "xrm_lama" varchar, "xuser_id" int4)
  RETURNS TABLE("status" int4, "message" text) AS $BODY$
DECLARE 
        vstatus int4;
        vmessage TEXT;
        vpasien_id_tujuan int4;
        vpasien_id_lama int4;
        vno_rekam_medik VARCHAR;
        vno_rm_1 VARCHAR;
        vno_rm_2 VARCHAR;
        vis_merge_transaction BOOLEAN;
        
BEGIN
        vno_rm_1 := xrm_tujuan;
        vno_rm_2 := xrm_lama;
        
--      xrm_tujuan := \'0200\'||RIGHT(\'00000\'||xrm_tujuan,6);
--      xrm_lama := \'0200\'||RIGHT(\'00000\'||xrm_lama,6);

        SELECT
                pasien_id,no_rekam_medik INTO vpasien_id_tujuan, vno_rekam_medik
        FROM pasien_m
        WHERE no_rekam_medik = xrm_tujuan;

        SELECT
                pasien_id INTO vpasien_id_lama
        FROM pasien_m
        WHERE no_rekam_medik = xrm_lama;
        
        SELECT
            is_merge_transaction INTO vis_merge_transaction
        FROM konfigsystem_k 
        WHERE konfigsystem_id = 1;
        IF(COALESCE(vpasien_id_tujuan,0) = 0) 
        
        THEN
            vstatus := 1;
            vmessage := CONCAT(\'No Rekam Medik \' , xrm_tujuan , \' tidak ditemukan!\') ;
        ELSEIF(COALESCE(vpasien_id_lama,0) = 0) 
        THEN
            vstatus := 2;
            vmessage := CONCAT(\'No Rekam Medik \' , xrm_lama , \' tidak ditemukan!\') ;
        ELSE 
                -- pasien 
                UPDATE pasien_m
                SET is_mergerm = \'[\'||xrm_tujuan||\']\',
                        is_active = FALSE ,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                -- kunjungan
                UPDATE pendaftaran_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE pasienadmisi_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE konsulpoli_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE pasienpulang_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE penjualanresep_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                IF EXISTS (
                    SELECT *FROM information_schema.tables
                    WHERE table_schema = \'public\'
                    AND "table_name" = \'riwayatkunjungan_r\'
                    LIMIT 1
                )
                THEN
                    UPDATE riwayatkunjungan_r
                    SET pasien_id = vpasien_id_tujuan,
                            no_rekam_medik = vno_rekam_medik
                    WHERE pasien_id = vpasien_id_lama;
                END IF;
                
                IF EXISTS (
                    SELECT *FROM information_schema.tables
                    WHERE table_schema = \'history\'
                    AND "table_name" = \'riwayat_lab\'
                    LIMIT 1
                )
                THEN
                    UPDATE history.riwayat_lab
                    SET no_rm = vno_rm_1
                    WHERE no_rm = vno_rm_2;
                END IF;
                
                IF EXISTS (
                    SELECT *FROM information_schema.tables
                    WHERE table_schema = \'history\'
                    AND "table_name" = \'riwayat_rad\'
                    LIMIT 1
                )
                THEN
                    UPDATE history.riwayat_rad
                    SET no_rm = vno_rm_1
                    WHERE no_rm = vno_rm_2;
                END IF;
                
                IF EXISTS (
                    SELECT *FROM information_schema.tables
                    WHERE table_schema = \'history\'
                    AND "table_name" = \'riwayat_resep\'
                    LIMIT 1
                )
                THEN
                    UPDATE history.riwayat_resep
                    SET no_rm = vno_rm_1
                    WHERE no_rm = vno_rm_2;
                END IF;
                -- riwayat soap, askep, asmed
                UPDATE soaprj_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE cppt_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE soapfisioterapi_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE soapfisioterapidetail_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE anamnesa_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE resumemedis_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE pemeriksaanfisik_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE instruksitindakan_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE instruksitindakanbmhp_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE instruksitindakanbmhp_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE koreksidiagnosa_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE pasienmorbiditas_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE reseptur_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE returresep_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                -- penunjang
                UPDATE pasienkirimkeunitlain_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE pasienmasukpenunjang_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE hasilpemeriksaanlab_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE jadwalterapifisio_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE programterapi_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE programterapirajal_r
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE pasiendirujukkeluar_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE pemakaianambulan_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                UPDATE pesanambulan_t
                SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE pasien_id = vpasien_id_lama;
                
                IF(vis_merge_transaction = TRUE)
                THEN 
                    UPDATE tindakanpelayanan_t
                    SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                    WHERE pasien_id = vpasien_id_lama;
                    
                    UPDATE obatalkespasien_t
                    SET pasien_id = vpasien_id_tujuan,
                        last_modified_by = xuser_id,
                        last_modified_date = CURRENT_TIMESTAMP
                    WHERE pasien_id = vpasien_id_lama;
                END IF;
                
                vstatus := 0;
                vmessage := \'Success\';
                    
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
        echo "m230305_025205_migrate_GB1083_function_sp_merge_rm cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230305_025205_migrate_GB1083_function_sp_merge_rm cannot be reverted.\n";

        return false;
    }
    */
}
