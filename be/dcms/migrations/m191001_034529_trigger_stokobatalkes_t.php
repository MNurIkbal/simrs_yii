<?php

use yii\db\Migration;

/**
 * Class m191001_034529_trigger_stokobatalkes_t
 */
class m191001_034529_trigger_stokobatalkes_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*Trigger: bu_stokobatalkes_t on public.stokobatalkes_t*/

        $this->execute('DROP TRIGGER if exists bu_stokobatalkes_t ON public.stokobatalkes_t;');

        $this->execute('DROP FUNCTION if exists public.bu_stokobatalkes_t();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.bu_stokobatalkes_t()
  RETURNS trigger AS
\$BODY\$
-- @created by yaya
-- before update 
-- di buat 2 untuk menghandle is_deleted
--- Sok mangga mun bade di edit mah
-- 21 Maret 2018

DECLARE
-- Prepare data untuk inser or update ke stokobatalkes_r
  vQtyAwal INTEGER;
  vQtyMasuk INTEGER;
  vQtyKeluar INTEGER;
  vQtySisa INTEGER;
  vIdPeriode INTEGER;
  vQtyTersedia INTEGER;
  vQtyPesan INTEGER;
  vIdStokObatAlkes INTEGER;
  vIsDeleted BOOLEAN;

-- additional attributes for condition
   vIsFlag BOOLEAN;
   vQSisa INTEGER;
   vQKeluar INTEGER;

-- Untuk Nampung dari stokobatalkes_t
    vRuanganId INTEGER; -- @alias ruangan_id
  vObatAlkesId INTEGER; -- @alias obatalkes_id
  vMutasiObatDetail INTEGER; -- @alias mutasiobatdetail_id
  vTerimaMutasiDetailId INTEGER; -- @alias terimamutasidetail_id
  vQtyIn INTEGER; -- @alias qtystok_in
  vQtyOut INTEGER; -- @alias qtystok_out

BEGIN
  -- ini untuk redeclare varaiable yang di perlukan untuk kondisi
  vRuanganId := NEW.ruangan_id;
  vObatAlkesId := NEW.obatalkes_id;
  vMutasiObatDetail := NEW.mutasiobatdetail_id;
  vTerimaMutasiDetailId := NEW.terimamutasidetail_id;
  vQtyIn := NEW.qtystok_in;
  vQtyOut := NEW.qtystok_out;
  vIsDeleted := NEW.is_deleted;
    
    IF (vIsDeleted = FALSE)
      THEN
          RETURN NEW;
  END IF;

-- declare untuk ke tabel stokobatalkes_r
    SELECT 
       stokobatr_id,
       qty_masuk,
       qty_keluar,
       qty_sisa,
       qty_dipesan,
       qty_tersedia,
       periodestokobat_id
    INTO
       vIdStokObatAlkes,
       vQtyMasuk,
       vQtyKeluar,
       vQtySisa,
       vQtyPesan,
       vQtyTersedia,
       vIdPeriode
    FROM stokobatalkes_r
    WHERE obatalkes_id = vObatAlkesId AND ruangan_id = vRuanganId  AND is_periode = true;

    -- Kondisi untuk ada mutasi maka Qty Pemesanan Bertambah
vQSisa :=  (vQtySisa + (vQtyOut - vQtyIn));
vQKeluar := vQtyKeluar - (vQtyOut - vQtyIn);
  IF (NEW.mutasiobatdetail_id IS NOT NULL)
      THEN 
       vQtyPesan := vQtyPesan - (vQtyOut - vQtyIn);
       vQtyTersedia := vQtyTersedia + (vQtyOut - vQtyIn);
       vQSisa := vQtySisa;
       vQKeluar := vQtyKeluar;
 ELSEIF (NEW.terimamutasidetail_id IS NOT NULL)
      THEN
 -- Ketika mutasi tsb sudah di terimamutasiobatdetail_t
       vQtyPesan := vQtyPesan + vQtyIn;
       vQtyTersedia := vQtyTersedia - vQtyIn;
 ELSE
      vQtyTersedia :=  (vQtySisa + (vQtyOut - vQtyIn));
 END IF;

-- Data sudah ada berarti di update datanya execute data
    IF (vIdStokObatAlkes != 0)
         THEN
                                        UPDATE stokobatalkes_r SET 
                                        qty_masuk = vQtyMasuk - vQtyIn, 
                                        qty_keluar = vQKeluar, 
                                        qty_sisa = vQSisa, 
                                        qty_tersedia = vQtyTersedia, 
                                        qty_dipesan = vQtyPesan 
                                        WHERE stokobatr_id = vIdStokObatAlkes;
                    
                                            -- SET value baru buat stok
                        NEW.stok := vQSisa;
    END IF;

       
RETURN NEW;

END;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.bu_stokobatalkes_t()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER bu_stokobatalkes_t
  BEFORE UPDATE OF is_deleted
  ON public.stokobatalkes_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.bu_stokobatalkes_t();');

/*Trigger: stokobatalkes_r on public.stokobatalkes_t*/

        $this->execute('DROP TRIGGER if exists stokobatalkes_r ON public.stokobatalkes_t;');

        $this->execute('DROP FUNCTION if  exists public.stokobatalkes_r();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.stokobatalkes_r()
  RETURNS trigger AS
\$BODY\$
            -- @created by yaya
            -- before insert
            --- Sok mangga mun bade di edit mah
            -- 7 Maret 2018

            DECLARE
            -- Prepare data untuk inser or update ke stokobatalkes_r
                vQtyAwal FLOAT;
                vQtyMasuk FLOAT;
                vQtyKeluar FLOAT;
                vQtySisa FLOAT;
                vIdPeriode INTEGER;
                vQtyTersedia FLOAT;
                vQtyPesan FLOAT;
                vIdStokObatAlkes INTEGER;
                vParamJson VARCHAR;
                -- additional attributes for condition
                vIsFlag BOOLEAN;
                vQSisa FLOAT;
                vQKeluar FLOAT;
                vStokAwal FLOAT; --dipake di transaksi so, ketika dia stok awal
                vJumlahStokAwal INTEGER; -- dipake di transaksi so, nampung total stok awal nya obat
                -- attribute untuk periode dari periodeposting_m
                vPeriodeId INTEGER; -- @alias periodeposting_id

                -- Untuk Nampung dari stokobatalkes_t
                vRuanganId INTEGER; -- @alias ruangan_id
                vObatAlkesId INTEGER; -- @alias obatalkes_id
                vMutasiObatDetail INTEGER; -- @alias mutasiobatdetail_id
                vTerimaMutasiDetailId INTEGER; -- @alias terimamutasidetail_id
                vQtyIn FLOAT; -- @alias qtystok_in
                vQtyOut FLOAT; -- @alias qtystok_out
                
                -- untuk set harga nettos asumsi tidak terset harga netto
                vparent_id INTEGER;
                vharganetto FLOAT;
                vjmlppn FLOAT;
                
                -- Set untuk implementasi di ranap
                vImplementQty FLOAT;
              vJumlahImplemet FLOAT;
            BEGIN
                -- ini untuk redeclare varaiable yang di perlukan untuk kondisi
                vRuanganId := NEW.ruangan_id;
                vObatAlkesId := NEW.obatalkes_id;
                vMutasiObatDetail := NEW.mutasiobatdetail_id;
                vTerimaMutasiDetailId := NEW.terimamutasidetail_id;
                vQtyIn := NEW.qtystok_in;
                vQtyOut := NEW.qtystok_out;
                vparent_id := NEW.stokobatalkesasal_id;
                vImplementQty := 0;
                
                IF (NEW.stokopnamedetail_id IS NOT NULL AND NEW.additional_data IS NOT NULL) 
                THEN
                    vParamJson := NEW.additional_data;
                    vJumlahStokAwal := vParamJson::json->>'total_stok';
                    UPDATE stokobatalkes_r SET qty_awal = vJumlahStokAwal, qty_masuk = 0, qty_keluar = 0, qty_sisa = 0, qty_tersedia = 0, qty_dipesan = 0
                    WHERE obatalkes_id = vObatAlkesId AND ruangan_id = vRuanganId;

                    NEW.additional_data := null;
                END IF;

                -- declare untuk periode id
                SELECT 
                    periodestokobat_id
                INTO
                    vPeriodeId
                FROM periodestokobat_m --rubah tabel 
                WHERE current_date BETWEEN tglperiodestok_awal AND tglperiodestok_akhir;

                -- declare untuk ke tabel stokobatalkes_r
                SELECT 
                    stokobatr_id,
                    qty_masuk,
                    qty_keluar,
                    qty_sisa,
                    qty_dipesan,
                    qty_tersedia,
                    periodestokobat_id
                INTO
                    vIdStokObatAlkes,
                    vQtyMasuk,
                    vQtyKeluar,
                    vQtySisa,
                    vQtyPesan,
                    vQtyTersedia,
                    vIdPeriode
                FROM stokobatalkes_r
                WHERE obatalkes_id = vObatAlkesId 
                AND ruangan_id = vRuanganId  
                AND is_periode = true;
                
                IF (NEW.obatalkespasien_id IS NOT NULL AND NEW.additional_data IS NOT NULL) 
                THEN
                            vParamJson := NEW.additional_data;
                            vJumlahImplemet := COALESCE((vParamJson::json->>'total_implement')::FLOAT, 0);
                            vQtyPesan := vQtyPesan - vJumlahImplemet;
                            vImplementQty := vJumlahImplemet; 
                            NEW.additional_data := vJumlahImplemet;
                END IF;
                -- Kondisi untuk ada mutasi maka Qty Pemesanan Bertambah
                vQSisa := ((vQtyMasuk + vQtyIn) - (vQtyKeluar + vQtyOut));
                vQKeluar := vQtyKeluar + vQtyOut;
                IF (NEW.mutasiobatdetail_id IS NOT NULL)
                THEN 
                    vQtyPesan := vQtyPesan - vQtyOut;
                ELSEIF (NEW.terimamutasidetail_id IS NOT NULL)
                THEN
                    --        vQtyPesan := vQtyPesan - vQtyIn;
                    vQtyTersedia := vQtyTersedia + vQtyIn;
                ELSE
                    IF (vQtyIn != 0)
                    THEN
                        vQtyTersedia := vQtyTersedia + vQtyIn;
                    END IF;

                    IF (vQtyOut != 0)
                    THEN
                        vQtyTersedia := (vQtyTersedia - vQtyOut) + vImplementQty;
                    END IF;
                END IF;

                -- Data sudah ada berarti di update datanya execute data
                IF (vIdStokObatAlkes != 0)
                THEN
                    -- check periode yang berlaku apakah sama dengan peridode di stokobatalkes_r
                    IF (vIdPeriode != vPeriodeId)
                    THEN
                        UPDATE stokobatalkes_r SET 
                        is_periode = FALSE 
                        WHERE stokobatr_id = vIdStokObatAlkes;
                        -- SET value baru buat stok
                        NEW.stok := (vQtySisa - vQtyOut);
                        -- INSERT data baru ini dari pengadaan atau mutasi obat alkes dan belum ada recordnya
                        -- Membuat Stok awal yang baru
                        INSERT INTO stokobatalkes_r (
                            ruangan_id,obatalkes_id,qty_awal,qty_masuk,qty_keluar,qty_sisa,periodestokobat_id,qty_tersedia,qty_dipesan
                        )
                        VALUES (
                            vRuanganId, vObatAlkesId, vQtySisa, vQtySisa, vQtyOut, (vQtySisa - vQtyOut), vPeriodeId,vQtyTersedia,vQtyPesan
                        );                                      
                    ELSE
                        UPDATE stokobatalkes_r SET 
                        qty_masuk = vQtyMasuk + vQtyIn, 
                        qty_keluar = vQKeluar, 
                        qty_sisa = vQSisa, 
                        qty_tersedia = vQtyTersedia, 
                        qty_dipesan = vQtyPesan 
                        WHERE stokobatr_id = vIdStokObatAlkes;
                        -- SET value baru buat stok
                        NEW.stok := vQSisa;
                    END IF; 
                ELSEIF (vQtyIn != 0)
                THEN
                    -- SET value baru buat stok
                    NEW.stok := vQtyIn;
                    -- INSERT data baru ini dari pengadaan atau mutasi obat alkes dan belum ada recordnya
                    -- Membuat Stok awal yang baru
                    INSERT INTO stokobatalkes_r (
                        ruangan_id,obatalkes_id,qty_awal,qty_masuk,qty_keluar,qty_sisa,periodestokobat_id,qty_tersedia,qty_dipesan
                    )
                    VALUES (
                        vRuanganId, vObatAlkesId, vQtyIn, vQtyIn, 0, vQtyIn, vPeriodeId,vQtyIn,0
                    );
                END IF;
                    
                -- Set harga netto
            --  IF(COALESCE(vparent_id,0) <> 0)
            --  THEN
            --      SELECT harganetto
            --      INTO vharganetto
            --      FROM stokobatalkes_t
            --      WHERE stokobatalkes_id = vparent_id
            --      LIMIT 1;
            --      
            --      NEW.harganetto := vharganetto;
            --  END IF;
            --  
            --  -- Set nilai ppn
            --  SELECT jml_ppn 
            --  INTO vjmlppn
            --  FROM obatalkes_f()
            --  WHERE obatalkes_id = vObatAlkesId;
            --  
            --  NEW.jmlppn := vjmlppn;
            RETURN NEW;

            END;
            \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.stokobatalkes_r()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER stokobatalkes_r
  BEFORE INSERT
  ON public.stokobatalkes_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.stokobatalkes_r();');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191001_034529_trigger_stokobatalkes_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191001_034529_trigger_stokobatalkes_t cannot be reverted.\n";

        return false;
    }
    */
}
