<?php

use yii\db\Migration;

/**
 * Class m200304_113007_migrate_trigger_20200304
 */
class m200304_113007_migrate_trigger_20200304 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"upd_no_pembatalan\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    v_next_number INT;
    v_prefix VARCHAR(50); 
    v_no_pembatalan VARCHAR(50);
    
BEGIN
    
    v_prefix =  CONCAT('PBR', date_part('YEAR',now()) , RIGHT(CONCAT('0', date_part('MONTH',now())),2),RIGHT(CONCAT('0', date_part('DAY',now())),2) );

    
    IF NOT EXISTS(
        SELECT 
            1
        FROM pembatalanresep_t
        WHERE LEFT(no_pembatalan, 11) = v_prefix
        LIMIT 1
    )
    THEN 
        v_next_number = 1;
    ELSE
        SELECT 
            MAX(RIGHT(COALESCE(no_pembatalan,'0'),5)::int4) + 1 INTO v_next_number
        FROM pembatalanresep_t
        WHERE LEFT(no_pembatalan, 11) = v_prefix;
    END IF;
    
    v_no_pembatalan =  CONCAT(v_prefix, RIGHT(CONCAT('00000', v_next_number),5));
    
    NEW.no_pembatalan = v_no_pembatalan;
--  NEW.no_pembatalan = v_prefix;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"stokobatalkes_r\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
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
                                v_stok FLOAT;
                                v_stok_tersedia FLOAT;
                                v_tgl_kadaluarsa DATE;
                                v_no_batch VARCHAR;
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
                                v_tgl_kadaluarsa := NEW.tglkadaluarsa;
                                v_no_batch := NEW.nobatch;
                                
                                SELECT (COALESCE(SUM(qtystok_in) ,0) + vQtyIn) - (COALESCE(SUM(qtystok_out) ,0) + vQtyOut)  INTO v_stok
                                FROM stokobatalkes_t
                                WHERE ruangan_id = vRuanganId 
                                AND obatalkes_id = vObatAlkesId
                                AND tglkadaluarsa = v_tgl_kadaluarsa
--                              AND nobatch = v_no_batch
--                              AND stokoa_aktif IS TRUE 
                                ;
                                
                                SELECT (COALESCE(SUM(qtystok_in) ,0) + vQtyIn) - (COALESCE(SUM(qtystok_out) ,0) + vQtyOut)  INTO v_stok_tersedia
                                FROM stokobatalkes_t
                                WHERE ruangan_id = vRuanganId 
                                AND obatalkes_id = vObatAlkesId;
                                
--                              SELECT stok INTO v_stok_terpakai
--                              FROM stokobatalkes_t
--                              WHERE ruangan_id = vRuanganId 
--                              AND obatalkes_id = vObatAlkesId
--                              AND tglkadaluarsa = v_tgl_kadaluarsa
-- --                               AND nobatch = v_no_batch
--                              ORDER BY stokobatalkes_id DESC
--                              LIMIT 1;
--                              
--                              IF(qtystok_in = 0)
--                              THEN
--                                  v_stok = v_stok - v_stok_terpakai;
--                 ELSE
--                                  v_stok = v_stok + v_stok_terpakai;
--                              END IF;
                                
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
--                         NEW.stok := vQSisa;
                        NEW.stok := v_stok;
                        NEW.stok_tersedia := v_stok_tersedia;
                    END IF; 
                ELSEIF (vQtyIn != 0)
                THEN
                    -- SET value baru buat stok
--                     NEW.stok := vQtyIn;
                                        NEW.stok := v_stok;
                                        NEW.stok_tersedia := v_stok_tersedia;
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

            END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"update_stokoa\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE
v_stokobatalkes_id int4;
v_stok float8;
v_stokoa_aktif boolean;
v_ruangan_id int4;
v_obatalkes_id int4;
BEGIN
    v_stokobatalkes_id := NEW.stokobatalkes_id;
    v_stok := NEW.stok;
    v_stokoa_aktif := NEW.stokoa_aktif;
    v_ruangan_id := NEW.ruangan_id;
    v_obatalkes_id := NEW.obatalkes_id;
    
    IF(v_stokoa_aktif IS FALSE)
    THEN
--          UPDATE stokobatalkes_t 
--          SET stok = 0
--          WHERE stokobatalkes_id = v_stokobatalkes_id;
--          
        IF NOT EXISTS (
            SELECT 1 FROM stokobatalkes_t
            WHERE obatalkes_id = v_obatalkes_id
            AND ruangan_id = v_ruangan_id
            AND stokoa_aktif IS TRUE
        )
        THEN
            UPDATE stokobatalkes_r
            SET qty_sisa = 0,-- qty_sisa - v_stok,
                            qty_tersedia = 0,--qty_tersedia - v_stok,
                            qty_masuk = 0, --qty_masuk - v_stok
                            qty_keluar = 0 --qty_masuk - v_stok
            WHERE obatalkes_id = v_obatalkes_id
            AND ruangan_id = v_ruangan_id;
        END IF;
  END IF;     
    RETURN NEW;

END;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"del_pemesanan_stokobat_r\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
var_ruangan_id INTEGER;
var_obatalkes_id INTEGER;
var_jml_mutasi INTEGER;
var_pesan_obat_id INTEGER;
var_qty_tersedia INTEGER;
var_qty_pesan INTEGER;
var_stokobatr_id INTEGER;
var_qty_tersedia_count INTEGER;
var_qty_pesan_count INTEGER;
vmutasiobatruangan_id INTEGER;
vis_deleted BOOLEAN;

BEGIN
    var_jml_mutasi := NEW.jumlah_mutasi;
    vmutasiobatruangan_id := NEW.mutasiobatruangan_id;
    var_obatalkes_id := NEW.obatalkes_id;
    vis_deleted := NEW.is_deleted;

    IF (vis_deleted IS TRUE)
    THEN
    
        SELECT ruanganasal_id INTO var_ruangan_id
        FROM mutasiobatruangan_t
        WHERE mutasiobatruangan_id = vmutasiobatruangan_id;
        
        SELECT stokobatr_id, qty_tersedia, qty_dipesan 
        INTO var_stokobatr_id, var_qty_tersedia, var_qty_pesan 
        FROM stokobatalkes_r WHERE obatalkes_id = var_obatalkes_id 
        AND ruangan_id = var_ruangan_id;
        
        var_qty_tersedia_count := var_qty_tersedia + var_jml_mutasi;
        var_qty_pesan_count := var_qty_pesan - var_jml_mutasi;
        
        UPDATE stokobatalkes_r 
        SET qty_tersedia = var_qty_tersedia_count, 
                qty_dipesan = var_qty_pesan_count 
        WHERE stokobatr_id = var_stokobatr_id;
    
END IF;

RETURN NEW;
END;

        \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"fgetvaluelookup\"(\"vlookup_id\" int4)
  RETURNS \"pg_catalog\".\"varchar\" AS \$BODY\$
                        DECLARE vlookup_value VARCHAR;
                        BEGIN
                        SELECT lookup_value INTO vlookup_value
                        FROM lookup_m
                        WHERE lookup_id = vlookup_id;

                        RETURN vlookup_value;

                        END
                        \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"harga_jual\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
    NEW.hargajual_oa = NEW.hargasatuan_oa * NEW.qty_oa;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"no_penerimaan\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE
    vId integer; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    v_Nomor varchar;
    v_tipe int;
    
BEGIN

    v_tipe := NEW.is_tipe;
    if (v_tipe = 1)
    THEN
        vId := 30;
    ELSE
        vId := 142;
    
    END IF;
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;

    NEW.no_penerimaan = v_Nomor;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"no_penerimaanbarang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE
    vId integer := 165; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    v_Nomor varchar;
    
BEGIN

    IF (NEW.is_verifikasi = 1) 
            THEN
                    -- Update Validasi untuk bisa melakukan penerimaan lagi
                        UPDATE validasipobarang_t SET 
                            is_verifikasi = FALSE
                        WHERE validasipobarang_id = NEW.validasipobarang_id;

                    -- Ini Update Trigger untuk menyatakan bahwa parentnya sudah di verifikasi
                        UPDATE penerimaanbarangdetail_t SET
                                additional_data  = 'is_verifikasi'
                        WHERE penerimaanbarang_id = NEW.penerimaanbarang_id AND is_deleted = false;
        RETURN NEW;
    ELSEIF (NEW.is_verifikasi = 2)
            THEN
            RETURN NEW;
    END IF;
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;

    NEW.no_penerimaan = v_Nomor;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");


        
 }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200304_113007_migrate_trigger_20200304 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200304_113007_migrate_trigger_20200304 cannot be reverted.\n";

        return false;
    }
    */
}
