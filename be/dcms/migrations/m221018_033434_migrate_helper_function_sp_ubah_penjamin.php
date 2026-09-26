<?php

use yii\db\Migration;

/**
 * Class m221018_033434_migrate_helper_function_sp_ubah_penjamin
 */
class m221018_033434_migrate_helper_function_sp_ubah_penjamin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."sp_ubah_penjamin"("xno_pendaftaran" varchar, "xpenjamin_nama" text)
  RETURNS TABLE("status" int4, "message" text) AS $BODY$
DECLARE 
    vstatus int4;
    vmessage TEXT;
    vpendaftaran_id int4;
    vpenjamin_id int4;
    vcarabayar_id int4;
    
BEGIN
    SELECT 
        pendaftaran_id INTO vpendaftaran_id
    FROM pendaftaran_t 
    WHERE no_pendaftaran = xno_pendaftaran;
    
    SELECT 
        penjamin_id, carabayar_id INTO vpenjamin_id, vcarabayar_id
    FROM penjamin_m
    WHERE penjamin_nama = xpenjamin_nama;
    
    IF (vpendaftaran_id IS NULL) 
    THEN
        vstatus := 1;
        vmessage := CONCAT(\'No Pendaftaran \' , xno_pendaftaran , \' Tidak Ditemukan\') ;
    ELSEIF(vpenjamin_id IS NULL)
    THEN
        vstatus := 2;
        vmessage := CONCAT(\'Penjamin \' , xpenjamin_nama , \' Tidak Ditemukan\') ;
    ELSE 
        UPDATE pendaftaran_t
        SET penjamin_id = vpenjamin_id,
                carabayar_id = vcarabayar_id,
                last_modified_by = 1,
                last_modified_date = CURRENT_TIMESTAMP
        WHERE pendaftaran_id = vpendaftaran_id;
        
        UPDATE pasienadmisi_t
        SET penjamin_id = vpenjamin_id,
                carabayar_id = vcarabayar_id,
                last_modified_by = 1,
                last_modified_date = CURRENT_TIMESTAMP
        WHERE pendaftaran_id = vpendaftaran_id;
        
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
        echo "m221018_033434_migrate_helper_function_sp_ubah_penjamin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221018_033434_migrate_helper_function_sp_ubah_penjamin cannot be reverted.\n";

        return false;
    }
    */
}
