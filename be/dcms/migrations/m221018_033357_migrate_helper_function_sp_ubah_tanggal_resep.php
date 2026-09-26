<?php

use yii\db\Migration;

/**
 * Class m221018_033357_migrate_helper_function_sp_ubah_tanggal_resep
 */
class m221018_033357_migrate_helper_function_sp_ubah_tanggal_resep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."sp_ubah_tanggal_resep"("xno_resep" varchar, "xtanggal" timestamp)
  RETURNS TABLE("status" int4, "message" text) AS $BODY$
DECLARE 
    vstatus int4;
    vmessage TEXT;
    
BEGIN
    IF NOT EXISTS (
            SELECT 
                1
            FROM penjualanresep_t
            WHERE noresep = xno_resep
            LIMIT 1
    ) 
    THEN
        vstatus := 1;
        vmessage := CONCAT(\'No Resep \' , xno_resep , \' Tidak Ditemukan\') ;
    ELSE 
        UPDATE penjualanresep_t
        SET tglresep = xtanggal
        WHERE noresep = xno_resep;
        
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
        echo "m221018_033357_migrate_helper_function_sp_ubah_tanggal_resep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221018_033357_migrate_helper_function_sp_ubah_tanggal_resep cannot be reverted.\n";

        return false;
    }
    */
}
