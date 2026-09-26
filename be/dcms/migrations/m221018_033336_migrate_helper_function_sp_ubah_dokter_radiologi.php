<?php

use yii\db\Migration;

/**
 * Class m221018_033336_migrate_helper_function_sp_ubah_dokter_radiologi
 */
class m221018_033336_migrate_helper_function_sp_ubah_dokter_radiologi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."sp_ubah_dokter_radiologi"("xno_masukpenunjang" text, "xnama_pegawai" text)
  RETURNS TABLE("status" int4, "message" text) AS $BODY$
DECLARE 
    vstatus int4;
    vmessage TEXT;
    vpasienmasukpenunjang_id int4;
    vpegawai_id int4;
    
BEGIN
    SELECT 
        pasienmasukpenunjang_id INTO vpasienmasukpenunjang_id
    FROM pasienmasukpenunjang_t 
    WHERE no_masukpenunjang = xno_masukpenunjang;
    
    SELECT pegawai_id INTO vpegawai_id
    FROM pegawai_m
    WHERE nama_pegawai = xnama_pegawai
    AND is_deleted IS FALSE
    AND is_active IS TRUE;
    
    IF (vpasienmasukpenunjang_id IS NULL) 
    THEN
        vstatus := 1;
        vmessage := CONCAT(\'No Radiologi \' , xno_masukpenunjang , \' Tidak Ditemukan\') ;
    ELSEIF (vpegawai_id IS NULL) 
    THEN
        vstatus := 1;
        vmessage := CONCAT(\'Pegawai/Dokter \' , xnama_pegawai , \' Tidak Ditemukan\') ;
    ELSE 
        UPDATE tindakanpelayanan_t
        SET dokterpenanggungjawab_id = vpegawai_id
        WHERE pasienmasukpenunjang_id = vpasienmasukpenunjang_id
        AND instalasi_id = 5;
        
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
        echo "m221018_033336_migrate_helper_function_sp_ubah_dokter_radiologi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221018_033336_migrate_helper_function_sp_ubah_dokter_radiologi cannot be reverted.\n";

        return false;
    }
    */
}
