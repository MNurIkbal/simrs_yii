<?php

use yii\db\Migration;

/**
 * Class m201221_101636_migrate_20201221_fn_stokbarang
 */
class m201221_101636_migrate_20201221_fn_stokbarang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE OR REPLACE FUNCTION "public"."stokbarang_t"()
  RETURNS "pg_catalog"."trigger" AS $BODY$
-- @created by yaya
-- before insert
--- Sok mangga mun bade di edit mah
-- 26 Maret 2018
-- 16 Januari 2019 by Ikbal, edit case mutasi barang mengurangi qty_sisa dan qty_tersedia dan menambahkan qty_keluar
DECLARE
-- Prepare data untuk inser or update ke stokbarang_r
  vQtyAwal INTEGER;
  vQtyMasuk INTEGER;
  vQtyKeluar INTEGER;
  vQtySisa INTEGER;
  vIdPeriode INTEGER;
  vQtyTersedia INTEGER;
  vQtyPesan INTEGER;
  vQtyDiPesan INTEGER;
  vIdStokBarang INTEGER;
-- additional attributes for condition
   vIsFlag BOOLEAN;
   vQSisa INTEGER;
   vQKeluar INTEGER;
-- attribute untuk periode dari periodeposting_m
   vPeriodeId INTEGER; -- @alias periodeposting_id

-- Untuk Nampung dari stokbarang_t
    vRuanganId INTEGER; -- @alias ruangan_id
  vBarangId INTEGER; -- @alias barang_id
  vMutasiBarangDetail INTEGER; -- @alias mutasibarangdetail_id
  vTerimaMutasiDetailId INTEGER; -- @alias terimamutasidetail_id
  vQtyIn INTEGER; -- @alias qtystok_in
  vQtyOut INTEGER; -- @alias qtystok_out

-- Kondisi untuk Stok Opname
   vKondisiSo INTEGER;

BEGIN
  -- ini untuk redeclare varaiable yang di perlukan untuk kondisi
  vRuanganId := NEW.ruangan_id;
  vBarangId := NEW.barang_id;
  vMutasiBarangDetail := NEW.mutasibarangdetail_id;
  vTerimaMutasiDetailId := NEW.terimamutasibarangdetail_id;
  vQtyIn := NEW.qtystok_in;
  vQtyOut := NEW.qtystok_out;
 -- declare untuk periode id
    SELECT 
      periodestokbarang_id
    INTO
      vPeriodeId
    FROM periodestokbarang_m --rubah tabel 
    WHERE current_date BETWEEN tglperiodestok_awal AND tglperiodestok_akhir;

-- declare untuk ke tabel stokbarang_r
    SELECT 
       stokbarangr_id,
       qty_awal,
       qty_masuk,
       qty_keluar,
       COALESCE(qty_sisa,0),
       qty_dipesan,
       qty_tersedia,
       periodestokbarang_id
    INTO
       vIdStokBarang,
       vQtyAwal,
       vQtyMasuk,
       vQtyKeluar,
       vQtySisa,
       vQtyPesan,
       vQtyTersedia,
       vIdPeriode
    FROM stokbarang_r
    WHERE barang_id = vBarangId AND ruangan_id = vRuanganId  AND is_periode = true;
    
    -- Kondisi untuk ada mutasi maka Qty Pemesanan Bertambah
  vQSisa := ((vQtyMasuk + vQtyIn) - (vQtyKeluar + vQtyOut));
  vQKeluar := vQtyKeluar + vQtyOut;
  IF (NEW.mutasibarangdetail_id IS NOT NULL)
      THEN 
            --update case mutasi barang
--        vQtyPesan := vQtyPesan + vQtyOut;
--        vQtyTersedia := vQtyTersedia - vQtyOut;
--        vQSisa := vQtySisa;
--        vQKeluar := vQtyKeluar;
        SELECT
            qty_dipesan
        INTO
            vQtyDiPesan
        FROM mutasibarangdetail_t mt 
        WHERE mt.mutasibarangdetail_id = NEW.mutasibarangdetail_id;
    
        vQtyPesan := vQtyPesan - vQtyDiPesan;
        vQtyTersedia := vQtyTersedia;
        vQSisa := vQtySisa - vQtyOut;
        vQKeluar := vQtyKeluar + vQtyOut;
 ELSEIF (NEW.terimamutasibarangdetail_id IS NOT NULL)
      THEN
 -- Ketika mutasi tsb sudah di terimamutasibarangdetail_t
       vQtyPesan := vQtyIn - vQtyIn;
       vQtyTersedia := vQtyTersedia + vQtyIn;
 ELSEIF (NEW.stokopnamebarangdetail_id IS NOT NULL)
     THEN
 -- Ketika melakukan Stok opname engan kondisi Stok Awal
       SELECT 
         h.jenisstokopname
       INTO
         vKondisiSo
       FROM stokopnamebarangdetail_t c
       INNER JOIN stokopnamebarang_t h ON c.stokopnamebarang_id = h.stokopnamebarang_id
       WHERE c.stokopnamebarangdetail_id = NEW.stokopnamebarangdetail_id;
       IF (vKondisiSo = 139) THEN
          vQtyTersedia := vQtyIn - vQtyPesan;
          vQtyMasuk := 0;
          vQtyAwal := vQtyIn;
          vQKeluar := 0;
          vQSisa := vQtyIn;
       END IF;
 ELSE
      vQtyTersedia := ((vQtyMasuk + vQtyIn) - (vQtyKeluar + vQtyOut));
 END IF;
-- Data sudah ada berarti di update datanya execute data
    IF (vIdStokBarang  != 0)
         THEN
       -- check periode yang berlaku apakah sama dengan peridode di stokbarang_r
              IF (vIdPeriode != vPeriodeId)
                                    THEN
                                                    UPDATE stokbarang_r SET 
                                                    is_periode = FALSE 
                                                    WHERE stokbarangr_id = vIdStokBarang;
                                                                -- SET value baru buat stok
                                                             NEW.stok := (vQtySisa - vQtyOut);
                                                            -- INSERT data baru ini dari pengadaan atau mutasi barang alkes dan belum ada recordnya
                                                            -- Membuat Stok awal yang baru
                                                            INSERT INTO stokbarang_r 
                                                                        (ruangan_id,barang_id,qty_awal,qty_masuk,qty_keluar,qty_sisa,periodestokbarang_id,qty_tersedia,qty_dipesan)
                                                            VALUES 
                                                                     (vRuanganId, vBarangId, vQtySisa, vQtySisa, vQtyOut, (vQtySisa - vQtyOut), vPeriodeId,vQtyTersedia,vQtyPesan);                                     
                            ELSE
                                    IF (vQtyOut != 0)
                     THEN
                     vQtyTersedia = ((vQtyMasuk + vQtyIn) - (vQtyKeluar + vQtyOut));
                                    END IF;
                                        UPDATE stokbarang_r SET 
                    qty_awal = vQtyAwal,
                                        qty_masuk = vQtyMasuk + vQtyIn, 
                                        qty_keluar = vQKeluar, 
                                        qty_sisa = vQSisa, 
                                        qty_tersedia = vQtyTersedia, 
                                        qty_dipesan = vQtyPesan 
                                        WHERE stokbarangr_id = vIdStokBarang;
                    
                                            -- SET value baru buat stok
                                         NEW.stok := vQSisa;
              END IF; 
    ELSEIF (vQtyIn != 0)
        THEN
                        -- SET value baru buat stok
                        NEW.stok := vQtyIn;
                -- INSERT data baru ini dari pengadaan atau mutasi barang alkes dan belum ada recordnya
        -- Membuat Stok awal yang baru
                INSERT INTO stokbarang_r 
                            (ruangan_id,barang_id,qty_awal,qty_masuk,qty_keluar,qty_sisa,periodestokbarang_id,qty_tersedia,qty_dipesan)
                VALUES 
                         (vRuanganId, vBarangId, vQtyIn, vQtyIn, 0, vQtyIn, vPeriodeId,vQtyIn,0);
    END IF;

       
RETURN NEW;

END;$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201221_101636_migrate_20201221_fn_stokbarang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201221_101636_migrate_20201221_fn_stokbarang cannot be reverted.\n";

        return false;
    }
    */
}
