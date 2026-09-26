CREATE OR REPLACE FUNCTION public.stokobatalkes_r()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
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
        v_udd_detail_id INT;
        v_obatalkespasien_id INT;
                            
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
        v_obatalkespasien_id := NEW.obatalkespasien_id;

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

        -- Data sudah ada berarti di update datanya execute data
        IF (vIdStokObatAlkes != 0)
        THEN
            -- check periode yang berlaku apakah sama dengan peridode di stokobatalkes_r
            IF (vIdPeriode != vPeriodeId)
            THEN
                UPDATE stokobatalkes_r SET is_periode = FALSE 
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
            END IF; 
        ELSEIF (vQtyIn != 0)
        THEN
            -- INSERT data baru ini dari pengadaan atau mutasi obat alkes dan belum ada recordnya
            -- Membuat Stok awal yang baru
            INSERT INTO stokobatalkes_r (
                ruangan_id,obatalkes_id,qty_awal,qty_masuk,qty_keluar,qty_sisa,periodestokobat_id,qty_tersedia,qty_dipesan
            )
            VALUES (
                vRuanganId, vObatAlkesId, vQtyIn, vQtyIn, 0, vQtyIn, vPeriodeId,vQtyIn,0
            );
        END IF;

        RETURN NEW;

        END;
    $function$
;