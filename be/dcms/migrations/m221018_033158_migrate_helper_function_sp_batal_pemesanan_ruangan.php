<?php

use yii\db\Migration;

/**
 * Class m221018_033158_migrate_helper_function_sp_batal_pemesanan_ruangan
 */
class m221018_033158_migrate_helper_function_sp_batal_pemesanan_ruangan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."sp_batal_pemesanan_ruangan"("xno_pemesanan" text, "xjenis_pemesanan" text)
  RETURNS TABLE("status" int4, "message" text) AS $BODY$
DECLARE 
    vstatus int4;
    vmessage TEXT;
    vpesanobatalkes_id int4;
    vpesanbarang_id int4;
BEGIN
    IF(xjenis_pemesanan = \'BARANG\')
    THEN
        SELECT 
            pesanbarang_id INTO vpesanbarang_id
        FROM pesanbarang_t 
        WHERE no_pemesanan = xno_pemesanan
        AND statuspesan = \'398\';
        
        IF (vpesanbarang_id IS NULL) 
        THEN
            vstatus := 1;
            vmessage := CONCAT(\'No Pemesanan \' , xno_pemesanan , \' Barang Tidak Ditemukan atau Sudah Dikirim\') ;
        ELSE 
            UPDATE pesanbarang_t
            SET statuspesan = \'602\'
            WHERE pesanbarang_id = vpesanbarang_id;
            
            UPDATE pesanbarangdetail_t
            SET is_deleted = TRUE,
                    deleted_date = CURRENT_TIMESTAMP,
                    deleted_by = 0
            WHERE pesanbarang_id = vpesanbarang_id;
            
            vstatus := 0;
            vmessage := \'Success\';
            
        END IF;
    ELSE 
        SELECT 
            pesanobatalkes_id INTO vpesanobatalkes_id
        FROM pesanobatalkes_t 
        WHERE nopemesanan = xno_pemesanan
        AND statuspesan = \'398\';
        
        IF (vpesanobatalkes_id IS NULL) 
        THEN
            vstatus := 1;
            vmessage := CONCAT(\'No Pemesanan \' , xno_pemesanan , \' Alkes Tidak Ditemukan atau Sudah Dikirim\') ;
        ELSE 
            UPDATE pesanobatalkes_t
            SET statuspesan = \'602\'
            WHERE pesanobatalkes_id = vpesanobatalkes_id;
            
            UPDATE pesanobatdetail_t
            SET is_deleted = TRUE,
                    deleted_date = CURRENT_TIMESTAMP,
                    deleted_by = 0
            WHERE pesanobatalkes_id = vpesanobatalkes_id;
            
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
        echo "m221018_033158_migrate_helper_function_sp_batal_pemesanan_ruangan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221018_033158_migrate_helper_function_sp_batal_pemesanan_ruangan cannot be reverted.\n";

        return false;
    }
    */
}
