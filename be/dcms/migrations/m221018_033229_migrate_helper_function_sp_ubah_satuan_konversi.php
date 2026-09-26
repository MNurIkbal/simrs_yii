<?php

use yii\db\Migration;

/**
 * Class m221018_033229_migrate_helper_function_sp_ubah_satuan_konversi
 */
class m221018_033229_migrate_helper_function_sp_ubah_satuan_konversi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."sp_ubah_satuan_konversi"("xobatalkes_kode" text, "xsatuan_besar_lama" text, "xsatuan_besar" text, "xsatuan_kecil_lama" text, "xsatuan_kecil" text, "xnilai_konversi" numeric)
  RETURNS TABLE("status" int4, "message" text) AS $BODY$
DECLARE 
    vstatus int4;
    vmessage TEXT;
    vobatalkes_id int4;
    vsatuanbesar_id_lama int4;
    vsatuankecil_id_lama int4;
    vsatuanbesar_id int4;
    vsatuankecil_id int4;
    
BEGIN
    SELECT
        obatalkes_id INTO vobatalkes_id
    FROM obatalkes_m 
    WHERE obatalkes_kode = xobatalkes_kode;
    
    SELECT satuanunit_id INTO vsatuanbesar_id_lama 
    FROM satuanunit_m
    WHERE satuanunit_nama = xsatuan_besar_lama;
    
    SELECT satuanunit_id INTO vsatuankecil_id_lama 
    FROM satuanunit_m
    WHERE satuanunit_nama = xsatuan_kecil_lama;
    
    SELECT satuanunit_id INTO vsatuanbesar_id
    FROM satuanunit_m
    WHERE satuanunit_nama = xsatuan_besar;
    
    SELECT satuanunit_id INTO vsatuankecil_id
    FROM satuanunit_m
    WHERE satuanunit_nama = xsatuan_kecil;
    
    IF (vobatalkes_id IS NULL) 
    THEN
        vstatus := 1;
        vmessage := CONCAT(\'Kode Obat \' , xobatalkes_kode , \' Tidak Ditemukan\') ;
    ELSEIF (vsatuanbesar_id_lama IS NULL) 
    THEN
        vstatus := 2;
        vmessage := CONCAT(\'Satuan Besar Lama \' , xsatuan_besar_lama , \' Tidak Ditemukan\') ;
    ELSEIF (vsatuankecil_id_lama IS NULL) 
    THEN
        vstatus := 3;
        vmessage := CONCAT(\'Satuan Kecil Lama \' , xsatuan_besar_lama , \' Tidak Ditemukan\') ;
    ELSEIF (vsatuanbesar_id IS NULL) 
    THEN
        vstatus := 4;
        vmessage := CONCAT(\'Satuan Besar Baru \' , xsatuan_besar , \' Tidak Ditemukan\') ;
    ELSEIF (vsatuanbesar_id IS NULL) 
    THEN
        vstatus := 5;
        vmessage := CONCAT(\'Satuan Kecil Baru \' , xsatuan_kecil , \' Tidak Ditemukan\') ;
    ELSE 
        UPDATE obatalkes_m
        SET satuanbesar_id = vsatuanbesar_id,
                satuankecil_id = vsatuankecil_id
        WHERE obatalkes_id = vobatalkes_id;
        
        IF NOT EXISTS (
            SELECT 1
            FROM satuankonversi_m
            WHERE obatalkes_id = vobatalkes_id
            AND satuanbesar_id = vsatuanbesar_id_lama
            AND satuankecil_id = vsatuankecil_id_lama
            AND nilai_konversi = xnilai_konversi
            LIMIT 1
        )
        THEN
            UPDATE satuankonversi_m
            SET is_deleted = TRUE 
            WHERE obatalkes_id = vobatalkes_id
            AND satuanbesar_id = vsatuanbesar_id_lama
            AND satuankecil_id = vsatuankecil_id_lama;
        
            INSERT INTO satuankonversi_m(obatalkes_id,satuanbesar_id, satuankecil_id, nilai_konversi)
            VALUES(vobatalkes_id, vsatuanbesar_id, vsatuankecil_id, xnilai_konversi);
        ELSE 
            UPDATE satuankonversi_m
            SET nilai_konversi = xnilai_konversi 
            WHERE obatalkes_id = vobatalkes_id
            AND satuanbesar_id = vsatuanbesar_id_lama
            AND satuankecil_id = vsatuankecil_id_lama;
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
        echo "m221018_033229_migrate_helper_function_sp_ubah_satuan_konversi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221018_033229_migrate_helper_function_sp_ubah_satuan_konversi cannot be reverted.\n";

        return false;
    }
    */
}
