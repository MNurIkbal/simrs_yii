<?php

use yii\db\Migration;

/**
 * Class m191007_032112_sp_tindakanpelayan_multiple
 */
class m191007_032112_sp_tindakanpelayan_multiple extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.sp_tindakanpelayan_multiple(text);');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.sp_tindakanpelayan_multiple(IN xparam text)
  RETURNS TABLE(status boolean, message character varying) AS
\$BODY\$
DECLARE 
    vstatus bool;
    vmessage VARCHAR;
    vid int4;
    vnama VARCHAR;
    rec_tindakan   RECORD;
    cur_tindakan CURSOR FOR 
     SELECT 
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',1) AS kelaspelayanan_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',2) AS pasien_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',3) AS rencanaoperasi_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',4) AS instalasi_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',5) AS daftartindakan_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',6) AS tipepaket_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',7) AS tindakansudahbayar_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',8) AS carabayar_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',9) AS pendaftaran_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',10) AS jeniskasuspenyakit_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',11) AS ruangan_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',12) AS pasienmasukpenunjang_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',13) AS penjamin_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',14) AS pasienadmisi_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',15) AS instruksitindakan_id,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',16) AS tgl_tindakan,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',17) AS qty_tindakan,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',18) AS is_cyto,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',19) AS is_diskon,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',20) AS perawat1,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',21) AS perawat2,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',22) AS penatajasa,
         SPLIT_PART(unnest(string_to_array(xparam,';')),',',23) AS user_id;
    vkelaspelayanan_id VARCHAR;
    vpasien_id VARCHAR;
    vrencanaoperasi_id VARCHAR;
    vinstalasi_id VARCHAR;
    vdaftartindakan_id VARCHAR;
    vtipepaket_id VARCHAR;
    vtindakansudahbayar_id VARCHAR;
    vcarabayar_id VARCHAR;
    vpendaftaran_id VARCHAR;
    vjeniskasuspenyakit_id VARCHAR;
    vruangan_id VARCHAR;
    vpasienmasukpenunjang_id VARCHAR;
    vpenjamin_id VARCHAR;
    vpasienadmisi_id VARCHAR;
    vinstruksitindakan_id VARCHAR;
    vtgl_tindakan VARCHAR;
    vqty_tindakan VARCHAR;
    vis_cyto VARCHAR;
    vis_diskon VARCHAR;
    vperawat1 VARCHAR;
    vperawat2 VARCHAR;
    vis_penatajasa VARCHAR;
    vuser_id VARCHAR;
BEGIN
    -- Open the cursor
    OPEN cur_tindakan;

    LOOP
    -- fetch row into the film
        FETCH cur_tindakan INTO rec_tindakan;
    -- exit when no more row to fetch
        EXIT WHEN NOT FOUND;
    
    
    
    vkelaspelayanan_id := rec_tindakan.kelaspelayanan_id;
    vpasien_id := rec_tindakan.pasien_id;
    vrencanaoperasi_id := rec_tindakan.rencanaoperasi_id;
    vinstalasi_id := rec_tindakan.instalasi_id;
    vdaftartindakan_id := rec_tindakan.daftartindakan_id;
    vtipepaket_id := rec_tindakan.tipepaket_id;
    vtindakansudahbayar_id := rec_tindakan.tindakansudahbayar_id;
    vcarabayar_id := rec_tindakan.carabayar_id;
    vpendaftaran_id := rec_tindakan.pendaftaran_id;
    vjeniskasuspenyakit_id := rec_tindakan.jeniskasuspenyakit_id;
    vruangan_id := rec_tindakan.ruangan_id;
    vpasienmasukpenunjang_id := rec_tindakan.pasienmasukpenunjang_id;
    vpenjamin_id := rec_tindakan.penjamin_id;
    vpasienadmisi_id := rec_tindakan.pasienadmisi_id; 
    vinstruksitindakan_id := rec_tindakan.instruksitindakan_id;
    vtgl_tindakan := rec_tindakan.tgl_tindakan;
    vqty_tindakan := rec_tindakan.qty_tindakan;
    vis_cyto := rec_tindakan.is_cyto;
    vis_diskon := rec_tindakan.is_diskon;
    vperawat1 :=rec_tindakan.perawat1;
    vperawat2 := rec_tindakan.perawat2;
    vis_penatajasa := rec_tindakan.penatajasa;
    vuser_id := rec_tindakan.user_id;
    
    IF(LOWER(vkelaspelayanan_id) = 'null') THEN vkelaspelayanan_id := NULL; END IF;
    IF(LOWER(vpasien_id) = 'null') THEN vpasien_id := NULL; END IF;
    IF(LOWER(vrencanaoperasi_id) = 'null') THEN vrencanaoperasi_id := NULL; END IF;
    IF(LOWER(vinstalasi_id) = 'null') THEN vinstalasi_id := NULL;   END IF;
    IF(LOWER(vdaftartindakan_id) = 'null') THEN vdaftartindakan_id := NULL; END IF;
    IF(LOWER(vtipepaket_id) = 'null') THEN vtipepaket_id := NULL;   END IF;
    IF(LOWER(vtindakansudahbayar_id) = 'null') THEN vtindakansudahbayar_id := NULL; END IF;
    IF(LOWER(vcarabayar_id) = 'null') THEN vcarabayar_id := NULL;   END IF;
    IF(LOWER(vpendaftaran_id) = 'null') THEN vpendaftaran_id := NULL;   END IF;
    IF(LOWER(vjeniskasuspenyakit_id) = 'null') THEN vjeniskasuspenyakit_id := NULL; END IF;
    IF(LOWER(vruangan_id) = 'null') THEN vruangan_id := NULL;   END IF;
    IF(LOWER(vpasienmasukpenunjang_id) = 'null') THEN vpasienmasukpenunjang_id := NULL; END IF;
    IF(LOWER(vpenjamin_id) = 'null') THEN vpenjamin_id := NULL; END IF;
    IF(LOWER(vpasienadmisi_id) = 'null') THEN vpasienadmisi_id := NULL; END IF;
    IF(LOWER(vinstruksitindakan_id) = 'null') THEN vinstruksitindakan_id := NULL;   END IF;
    IF(LOWER(vtgl_tindakan) = 'null') THEN vtgl_tindakan := NULL;   END IF;
    IF(LOWER(vqty_tindakan) = 'null') THEN vqty_tindakan := NULL;   END IF;
    IF(LOWER(vis_cyto) = 'null') THEN vis_cyto := NULL; END IF;
    IF(LOWER(vis_diskon) = 'null') THEN vis_diskon := NULL; END IF;
    IF(LOWER(vperawat1) = 'null') THEN vperawat1 := NULL;   END IF;
    IF(LOWER(vperawat2) = 'null') THEN vperawat2 := NULL;   END IF;
    IF(LOWER(vis_penatajasa) = 'null') THEN vis_penatajasa := NULL; END IF;
    IF(LOWER(vuser_id) = 'null') THEN vuser_id := NULL; END IF;
    
    

--  vmessage := vkelaspelayanan_id::VARCHAR ;
--  vmessage := vkelaspelayanan_id;
    -- proess
--      SELECT *FROM sp_tindakanpelayan(
--          REPLACE(rec_tindakan.kelaspelayanan_id ,'null',NULL)::int4, 
--          REPLACE(rec_tindakan.pasien_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.rencanaoperasi_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.instalasi_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.daftartindakan_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.tipepaket_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.tindakansudahbayar_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.carabayar_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.pendaftaran_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.jeniskasuspenyakit_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.ruangan_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.pasienmasukpenunjang_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.penjamin_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.pasienadmisi_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.instruksitindakan_id, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.tgl_tindakan,'null',NULL)::TIMESTAMP, 
--          REPLACE(rec_tindakan.qty_tindakan, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.is_cyto, 'null',NULL)::bool, 
--          REPLACE(rec_tindakan.is_diskon, 'null',NULL)::bool, 
--          REPLACE(rec_tindakan.perawat1, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.perawat2, 'null',NULL)::int4, 
--          REPLACE(rec_tindakan.user_id, 'null',NULL)::int4
--          );
        SELECT *FROM sp_tindakanpelayan(
            vkelaspelayanan_id::int4 , 
            vpasien_id::int4,
            vrencanaoperasi_id::int4, 
            vinstalasi_id::int4, 
            vdaftartindakan_id::int4, 
            vtipepaket_id::int4, 
            vtindakansudahbayar_id::int4 ,
            vcarabayar_id::int4, 
            vpendaftaran_id::int4, 
            vjeniskasuspenyakit_id::int4, 
            vruangan_id::int4, 
            vpasienmasukpenunjang_id::int4,
            vpenjamin_id::int4, 
            vpasienadmisi_id::int4,  
            vinstruksitindakan_id::int4,
            vtgl_tindakan::TIMESTAMP, 
            vqty_tindakan::int4,  
            vis_cyto::bool, 
            vis_diskon::bool, 
            vperawat1::int4,  
            vperawat2::int4,  
            vis_penatajasa::bool,
            vuser_id::int4
        ) INTO vstatus, vmessage;
    END LOOP;

    -- Close the cursor
    CLOSE cur_tindakan;
    
    vstatus := TRUE;
    vmessage := 'Success';
    
    RETURN QUERY 
    SELECT vstatus, vmessage;
        
END; 
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;");

        $this->execute('ALTER FUNCTION public.sp_tindakanpelayan_multiple(text)
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191007_032112_sp_tindakanpelayan_multiple cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191007_032112_sp_tindakanpelayan_multiple cannot be reverted.\n";

        return false;
    }
    */
}
