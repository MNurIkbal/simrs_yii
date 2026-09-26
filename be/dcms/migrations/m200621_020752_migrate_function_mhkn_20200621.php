<?php

use yii\db\Migration;

/**
 * Class m200621_020752_migrate_function_mhkn_20200621
 */
class m200621_020752_migrate_function_mhkn_20200621 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_noreseptur\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 27; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    -- perubahan stok qty dipesan
    var_status_reseptur INTEGER;
    var_ruangan_id INTEGER;
    var_obatalkes_id INTEGER;
    var_reseptur_id INTEGER;
    var_qty_before INTEGER;
    var_stokobatr_id INTEGER;
    var_qty_tersedia INTEGER;
    var_qty_dipesan INTEGER;
    var_qty_tersedia_count INTEGER;
    var_qty_dipesan_count INTEGER;
    var_total_detail INTEGER;
    
BEGIN
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vDate,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vDate || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;
    
    NEW.noresep = v_Nomor;
    -- penambahan update qty_dipesan
    var_reseptur_id := new.reseptur_id;
    var_status_reseptur := new.status_reseptur;
    var_ruangan_id := NEW.ruangan_id;
    
    IF (var_status_reseptur = 347)
            THEN                                        
                    UPDATE stokobatalkes_r
                    SET qty_dipesan = (qty_dipesan - resepturdetail.total_qty  ), qty_tersedia = (qty_sisa - (qty_dipesan- resepturdetail.total_qty ))
                    FROM (
                            SELECT reseptur_id as resid,obatalkes_id, SUM(qty_konversi) as total_qty from resepturdetail_t where reseptur_id = var_reseptur_id and is_deleted = false GROUP BY reseptur_id, obatalkes_id
                    ) as resepturdetail
                    WHERE resepturdetail.resid = var_reseptur_id AND (stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id) = (resepturdetail.obatalkes_id, var_ruangan_id);
        END IF;
    
    RETURN NEW;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"ins_noantrian_konfig\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE
    v_konfigantrian int;
    v_prefix varchar;
    v_noantrian varchar;
    v_last varchar;
    v_id int;
    v_tgl_antrian DATE;
    v_ruangan int;
    v_jenisantrian int;
    v_jadwaldokter_id int4;
    v_jadwalbukapoli_id int4;
    v_fungsiantrian_id int4;
    v_jenisantriandetail_id int4;
    v_is_keteranganpasien BOOLEAN;
    
BEGIN
    v_konfigantrian := NEW.konfigantrian_id;
    v_tgl_antrian := NEW.tgl_antrian::DATE;
    v_ruangan := NEW.ruangan_id;
    v_jadwaldokter_id := NEW.jadwaldokter_id;
    v_jadwalbukapoli_id := NEW.jadwalbukapoli_id;
    v_jenisantrian := NEW.jenisantrian_id;
    v_fungsiantrian_id := NEW.fungsiantrian_id;
    v_jenisantriandetail_id := NEW.jenisantriandetail_id;
    
    SELECT is_keteranganpasien INTO v_is_keteranganpasien
    FROM konfigsystem_k 
    WHERE konfigsystem_id = 1;
    
    IF(v_is_keteranganpasien IS FALSE AND v_jenisantrian = 177)
    THEN
        v_prefix := '';
        
        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
        FROM antrian_t 
        WHERE jenisantrian_id = v_jenisantrian
        AND tgl_antrian::DATE = v_tgl_antrian
        AND jenisantriandetail_id = v_jenisantriandetail_id;
    ELSE
            
        IF (COALESCE(v_konfigantrian,0)=0)
        THEN
        -- > jenis_antrian = 'PENUNJANG' <=============================================
            IF (v_jenisantrian = 179) 
            THEN
                SELECT 
                    kode_antrian,
                    konfigantrian_id
                 INTO
                    v_prefix,
                    v_konfigantrian
                FROM konfigantrian_m 
                WHERE jenisantrian_id = 179
                and ruangan_id = v_ruangan 
                limit 1;
                
                NEW.konfigantrian_id = v_konfigantrian;
                
                SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                FROM antrian_t 
                WHERE jenisantrian_id = 179
                AND tgl_antrian::DATE = v_tgl_antrian
                AND ruangan_id=v_ruangan;
                        
        -- > jenis_antrian = 'FARMASI' <=============================================
            ELSE
                SELECT 
                    kode_antrian
                 INTO
                    v_prefix
                FROM konfigantrian_m 
                WHERE jenisantrian_id = v_jenisantrian
                and fungsiantrian_id = v_fungsiantrian_id 
                limit 1;
                    
                SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                FROM antrian_t 
                WHERE jenisantrian_id = v_jenisantrian
                AND fungsiantrian_id = v_fungsiantrian_id
                AND tgl_antrian::DATE = v_tgl_antrian
                AND ruangan_id=v_ruangan;
            END IF;
            
        ELSE
            SELECT 
                kode_antrian
            INTO
                v_prefix
            FROM konfigantrian_m 
            WHERE konfigantrian_id = v_konfigantrian;


            IF(v_jenisantrian = 312)
            THEN
        -- > jenis_antrian = 'Poliklinik langsung' <=============================================   
                IF(COALESCE(v_jadwaldokter_id,0)=0 AND COALESCE(v_jadwalbukapoli_id,0)=0) 
                THEN
                    SELECT  
                        jadwalbukapoli_m.jadwalbukapoli_id INTO v_jadwalbukapoli_id 
                    FROM jadwalbukapoli_m
                    JOIN 
                    (
                        SELECT 
                            ruangan_id,
                            tgl_antrian,
                            CASE
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'monday' THEN '75' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'tuesday' THEN '76' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'wednesday' THEN '77' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'thursday' THEN '78' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'friday' THEN '79' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'saturday' THEN '80' 
                                WHEN trim(to_char(antrian_t.tgl_antrian, 'day'::text)) = 'sunday' THEN '81' 
                            END AS hari
                        FROM antrian_t 
                        -- WHERE konfigantrian_id = v_konfigantrian
                        WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                        AND ruangan_id=v_ruangan
                        and jadwalbukapoli_id is null 
                        and jadwaldokter_id is null
                        AND tgl_antrian::DATE = v_tgl_antrian
                    ) antrian_t ON jadwalbukapoli_m.ruangan_id = antrian_t.ruangan_id 
                     and jadwalbukapoli_m.hari::int = antrian_t.hari::int
                     and jadwalbukapoli_m.is_active=TRUE 
                     and jadwalbukapoli_m.is_deleted=false
                     and antrian_t.tgl_antrian::time BETWEEN jadwalbukapoli_m.jam_mulai and jadwalbukapoli_m.jam_tutup;
                     
                        
                    NEW.jadwalbukapoli_id = v_jadwalbukapoli_id;
                        
                    SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                    FROM antrian_t 
                    -- WHERE konfigantrian_id = v_konfigantrian
                    WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                    AND tgl_antrian::DATE = v_tgl_antrian
                    AND jadwalbukapoli_id = v_jadwalbukapoli_id
                    AND ruangan_id=v_ruangan;
                        
        --                  SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
        --                  FROM antrian_t 
        --                  WHERE konfigantrian_id = v_konfigantrian
        --                  AND ruangan_id=v_ruangan
        --                  AND tgl_antrian::DATE = v_tgl_antrian;
                ELSE
        -- > jenis_antrian = 'Poliklinik lewat antrian' <=============================================
                    IF(COALESCE(v_jadwaldokter_id,0)<>0)
                    THEN            
                        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                        FROM antrian_t 
                        -- WHERE konfigantrian_id = v_konfigantrian
                        WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                        AND tgl_antrian::DATE = v_tgl_antrian
                        AND jadwaldokter_id = v_jadwaldokter_id
                        AND ruangan_id=v_ruangan;
                    ELSE
                        SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                        FROM antrian_t 
                        -- WHERE konfigantrian_id = v_konfigantrian
                        WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                        AND tgl_antrian::DATE = v_tgl_antrian
                        AND jadwalbukapoli_id = v_jadwalbukapoli_id
                        AND ruangan_id=v_ruangan;
                    END IF;
                END IF;
            
        -- > jenis_antrian = 'xxxxxx' <=============================================    
            ELSE
                SELECT RIGHT( '000'|| COALESCE((MAX(RIGHT(no_antrian,3)::int)),0) + 1, 3) INTO v_last
                FROM antrian_t 
                -- WHERE konfigantrian_id = v_konfigantrian
                WHERE regexp_replace(no_antrian, '[1234567890]', '', 'gi') = v_prefix
                AND tgl_antrian::DATE = v_tgl_antrian
                AND jenisantriandetail_id = v_jenisantriandetail_id;
            END IF;
        END IF;
    END IF;
    
    v_noantrian = v_prefix || v_last;

    SELECT MAX(antrian_id)
    INTO v_id
    FROM antrian_t;

--     UPDATE antrian_t
--     SET no_antrian = v_noantrian
--     WHERE antrian_id = v_id;
    NEW.no_antrian = v_noantrian;
    NEW.is_keteranganpasien = v_is_keteranganpasien;
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"ins_pembayaran\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
                
            DECLARE
                paramJson VARCHAR;
                vPembayaranpelayanan json;
                vPembayaranpenjamin json;
                vPembayaranmetode json;
                    vPembayarandiskon json;
                vPembayaran_id INTEGER;
                vrow json;
                
                
            BEGIN
                paramJson := NEW.additional_data;
                vPembayaranpelayanan := paramJson::json->>'pembayaran_pelayanan';
                vPembayaranpenjamin := paramJson::json->>'pembayaran_penjamin';
                vPembayaranmetode := paramJson::json->>'pembayaran_jenis_pembayaran';
                    vPembayarandiskon := paramJson::json->>'pembayaran_diskon';
                vPembayaran_id := NEW.pembayaran_id;


            -- insert ke pembayaranpelayanan_t
                FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpelayanan)
              LOOP
                    IF (vrow->>'penjamin_id' IS NOT NULL) THEN
                        INSERT INTO pembayaranpelayanan_t (
                                        pembayaran_id,
                                        carabayar_id,
                                        ruangan_id,
                                        penjamin_id,
                                        pendaftaran_id,
                                        tandabuktibayar_id,
                                        pasien_id,
                                        pasienadmisi_id,
                                        ruangan_pelakhir_id,
                                        no_pembayaran,
                                        tgl_pembayaran, 
                                        total_biayaoa,
                                        total_biayatindakan,
                                        total_biayapelayanan,
                                        total_subsidiasuransi,
                                        total_subsidipemerintah,
                                        total_subsidirs,
                                        total_iurbiaya,
                                        total_bayartindakan,
                                        total_discount,
                                        total_pembebasan,
                                        total_sisatagihan,
                                        statusbayar,
                                        penjualanresep_id,  
                                        biaya_administrasi,
                                        e_collection,
                                        no_rekening,
                                        nama_pemrekening,
                                        penggunaan_uangmuka,
                                        total_terbayar,
                                        pembulatan,
                                        additional_data

                     ) VALUES (
                                vPembayaran_id,
                                (vrow->>'carabayar_id')::INTEGER,
                                (vrow->>'ruangan_id')::INTEGER,
                                (vrow->>'penjamin_id')::INTEGER,
                                (vrow->>'pendaftaran_id')::INTEGER,
                                (vrow->>'tandabuktibayar_id')::INTEGER,
                                (vrow->>'pasien_id')::INTEGER,
                                (vrow->>'pasienadmisi_id')::INTEGER,
                                (vrow->>'ruangan_pelakhir_id')::INTEGER,
                                (vrow->>'no_pembayaran')::INTEGER,
                                (vrow->>'tgl_pembayaran')::TIMESTAMP,
                                (vrow->>'total_biayaoa')::FLOAT,
                                (vrow->>'total_biayatindakan')::FLOAT,
                                (vrow->>'total_biayapelayanan')::FLOAT,
                                (vrow->>'total_subsidiasuransi')::FLOAT,
                                (vrow->>'total_subsidipemerintah')::FLOAT,
                                (vrow->>'total_subsidirs')::FLOAT,
                                (vrow->>'total_iurbiaya')::FLOAT,
                                (vrow->>'total_bayartindakan')::FLOAT,
                                (vrow->>'total_discount')::FLOAT,
                                (vrow->>'total_pembebasan')::FLOAT,
                                (vrow->>'total_sisatagihan')::FLOAT,
                                (vrow->>'statusbayar')::VARCHAR,
                                CASE WHEN vrow->>'pendaftaran_id' IS NOT NULL THEN
                                                                    NULL
                                                                ELSE
                                                                    (vrow->>'penjualanresep_id')::INTEGER
                                                                END,
                                (vrow->>'biaya_administrasi')::FLOAT,
                                (vrow->>'e_collection')::BOOLEAN,
                                (vrow->>'no_rekening')::VARCHAR,
                                (vrow->>'nama_pemrekening')::VARCHAR,
                                (vrow->>'penggunaan_uangmuka')::FLOAT,
                                (vrow->>'total_terbayar')::FLOAT,
                                (vrow->>'pembulatan')::FLOAT,
                                (vrow->>'additional_data')::TEXT
                        );
                    END IF;
                END LOOP;
                
                    
            -- insert ke pembayaranpenjamin_t
                FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpenjamin)
              LOOP
                    IF (vrow->>'penjamin_id' IS NOT NULL) THEN
                        INSERT INTO pembayaranpenjamin_t (
                                        pembayaran_id,
                                        penjamin_id,
                                        penjamin_nama,
                                        no_kartu,
                                        total_dijamin

                     ) VALUES (
                                        vPembayaran_id,
                                        (vrow->>'penjamin_id')::INTEGER,
                                        (vrow->>'penjamin_nama')::VARCHAR,
                                        (vrow->>'no_kartu')::VARCHAR,
                                        (vrow->>'total_dijamin')::FLOAT                         
                                );
                    END IF;
                END LOOP;
                
                -- insert ke pembayaranmetode_t
                FOR vrow IN SELECT * FROM json_array_elements(vPembayaranmetode)
              LOOP
                    IF (vrow->>'metode_bayar' IS NOT NULL) THEN
                        INSERT INTO pembayaranmetode_t (
                                        pembayaran_id,
                                        metode_bayar,
                                        no_kartu,
                                        total_dibayar,
                                                                                jenisnontunai_id

                     ) VALUES (
                                        vPembayaran_id,
                                        (vrow->>'metode_bayar')::VARCHAR,
                                        (vrow->>'no_kartu')::VARCHAR,
                                        (vrow->>'total_dibayar')::FLOAT,                     
                                        (vrow->>'jenisnontunai_id')::INTEGER                     
                                );
                    END IF;
                END LOOP;


                    -- insert ke pembayarandiskon_t
                    FOR vrow IN SELECT * FROM json_array_elements(vPembayarandiskon)
                    LOOP 
                        IF (vrow->>'total_diskon' IS NOT NULL) THEN
                            INSERT INTO pembayarandiskon_t (
                                                    pembayaran_id,
                                                    pegawai_id,
                                                    komponentarif_id,
                                                    total_komponentarif,
                                                    total_diskon
                            ) VALUES (
                                                    vPembayaran_id,
                                                    (vrow->>'pegawai_id')::INTEGER,
                                                    (vrow->>'komponentarif_id')::INTEGER,
                                                    (vrow->>'total_komponentarif')::FLOAT,
                                                    (vrow->>'total_diskon')::FLOAT
                            );
                            END IF;
                    END LOOP;
                    
                    
                RETURN NEW;
            END
            \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"kamartempattidur_m_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
-- @created by ikbal 15 Februari 2019

DECLARE
    vtgl_tthistory TIMESTAMP;
    vruangan_id int4;
    vkamarruangan_id int4;
    vkamartempattidur_id int4;
    vno_tempattidur VARCHAR;
    vketerangan VARCHAR;
    vcreated_by int4;

BEGIN
  
    vtgl_tthistory := CURRENT_TIMESTAMP;
    vkamarruangan_id := NEW.kamarruangan_id;
    vkamartempattidur_id := NEW.kamartempattidur_id;
    vno_tempattidur := NEW.no_tempattidur;
    vcreated_by := NEW.created_by;
    
    
    SELECT  ruangan_id
    INTO        vruangan_id
    FROM kamarruangan_m
    WHERE kamarruangan_id = vkamarruangan_id;
    
    IF (NEW.is_deleted <> OLD.is_deleted)
    THEN
        IF (NEW.is_deleted = TRUE)
        THEN
            -- vketerangan := 'Penghapusan';
            vketerangan := '614';
            
            INSERT INTO kamartempattidur_r(
                            tgl_tthistory, ruangan_id, kamarruangan_id, kamartempattidur_id, 
                            no_tempattidur, is_activehistory, is_deletedhistory, keterangan,
                            last_modified_by)
            VALUES (vtgl_tthistory, vruangan_id, vkamarruangan_id, vkamartempattidur_id, 
                            vno_tempattidur, NEW.is_active, NEW.is_deleted, vketerangan,
                            vcreated_by);
        END IF;
    ELSEIF(NEW.is_active <> OLD.is_active)
    THEN
        IF (NEW.is_active = FALSE)
        THEN
            --vketerangan := 'Pengnon-Aktifan';
            vketerangan := '616';
        ELSE
            vketerangan := '615';
            --vketerangan := 'Peng-Aktifan';
        END IF;
        
        INSERT INTO kamartempattidur_r(
            tgl_tthistory, ruangan_id, kamarruangan_id, kamartempattidur_id, 
            no_tempattidur, is_activehistory, is_deletedhistory, keterangan,
            last_modified_by)
    VALUES (vtgl_tthistory, vruangan_id, vkamarruangan_id, vkamartempattidur_id, 
            vno_tempattidur, NEW.is_active, NEW.is_deleted, vketerangan,
                        vcreated_by);
    ELSEIF(NEW.kamarruangan_id <> OLD.kamarruangan_id)
    THEN
        vketerangan := '618';
        
        SELECT  ruangan_id
        INTO        vruangan_id
        FROM kamarruangan_m
        WHERE kamarruangan_id = OLD.kamarruangan_id;
        
        INSERT INTO kamartempattidur_r(
            tgl_tthistory, ruangan_id, kamarruangan_id, kamartempattidur_id, 
            no_tempattidur, is_activehistory, is_deletedhistory, keterangan,
            last_modified_by)
    VALUES (vtgl_tthistory, vruangan_id, OLD.kamarruangan_id, vkamartempattidur_id, 
            vno_tempattidur, NEW.is_active, NEW.is_deleted, vketerangan,
                        vcreated_by);
        
        vketerangan := '617';
        
        SELECT  ruangan_id
        INTO        vruangan_id
        FROM kamarruangan_m
        WHERE kamarruangan_id = NEW.kamarruangan_id;
        
        INSERT INTO kamartempattidur_r(
            tgl_tthistory, ruangan_id, kamarruangan_id, kamartempattidur_id, 
            no_tempattidur, is_activehistory, is_deletedhistory, keterangan,
            last_modified_by)
    VALUES (vtgl_tthistory, vruangan_id, NEW.kamarruangan_id, vkamartempattidur_id, 
            vno_tempattidur, NEW.is_active, NEW.is_deleted, vketerangan,
                        vcreated_by);
    END IF;
    
RETURN NEW;

END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_pembayarantransaksi\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer; 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    v_Nomor varchar;
    vjenis_transaksi integer;
    
BEGIN
    vjenis_transaksi := NEW.jenis_transaksi;
    
--  jenis_transaksi = 668 —> penomoran_id = 36
--  jika jenis_transaksi = 669 —> penomoran_id 37
    
    IF(vjenis_transaksi = 668)
    THEN 
        vId := 36;
    ELSE
        vId := 37;
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

    NEW.no_transaksi = v_Nomor;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
        
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_t\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    vPrefix VARCHAR;
  vNumber VARCHAR;
    -- Penambahan Triger Dari Yaya
    -- Tanggal 08-08-2019
    dataAntrian VARCHAR;
    dataKarcis VARCHAR;
    dataRujukan VARCHAR;
    dataPenanggung VARCHAR;
    dataAsuransi VARCHAR;
    dataPasien VARCHAR;
        dataOrderMcu VARCHAR;
    
    paramJson VARCHAR;
    
    -- Get New Pasien
    pasienId INTEGER;
    
    noAsuransi VARCHAR;
    idAsuransi INTEGER;
    idPasienAsuransi INTEGER;
    -- Generate Id
    rujukanId INTEGER;
    antrianId INTEGER;
    penanggungId INTEGER;
    
    -- Antrian Active
    isActive BOOLEAN;
    
    -- Count Tagihan
    countTagihan INTEGER;
    countPenunjang INTEGER;
    --antrian
    v_konfigantrian INTEGER;
    
    -- Kebutuhan untuk penunjang
    vPenunjang VARCHAR;
    noAntrian VARCHAR;
    idPenunjang INTEGER;
    
    -- Pendaftaran Online
    
    -- Update untuk pasienadmisi ranap
    vadmisi VARCHAR;
    vmasukkamar VARCHAR;
    vpasienadmisi_id int4;
    vuser_id int4;
    vpendaftaran_id int4;
    vcarabayar_id int4;
    vpenjamin_id int4;
    vkettempattidur_id int4; 
    vkelahiran_id int4;
    vpendaftaranasal_id int4;
BEGIN
    vcarabayar_id := NEW.carabayar_id;
    vpenjamin_id := NEW.penjamin_id;
    
        SELECT 
            CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(substring(no_pendaftaran FROM '[0-9]+')), 6) AS INT), 0) + 1 AS                VARCHAR(6)), 6, '0')) last_no
        INTO 
             vNumber
        FROM pendaftaran_t where instalasi_id = NEW.instalasi_id;
    
        SELECT 
                instalasi_singkatan 
    INTO 
       vPrefix 
    FROM instalasi_m WHERE instalasi_id = NEW.instalasi_id;

        NEW.no_pendaftaran := TRIM(vPrefix) || vNumber;
        
        -- Generate Form Pendaftaran
         paramJson := NEW.additional_data;  
         dataKarcis := paramJson::json->>'tarif';
         dataRujukan := paramJson::json->>'rujukan';
         dataAntrian := paramJson::json->>'antrian';
         dataPenanggung := paramJson::json->>'penanggung_jawab';
                 dataOrderMcu = paramJson::json->>'order_mcu';
         dataAsuransi := paramJson::json->>'asuransi';
         dataPasien := paramJson::json->>'pasien';
         vPenunjang := paramJson::json->>'tarif_penunjang';
         vadmisi := paramJson::json->>'pasien_admisi';
       vmasukkamar := paramJson::json->>'masuk_kamar';
         countTagihan := json_array_length(dataKarcis::json);
         countPenunjang := json_array_length(vPenunjang::json);
         vkelahiran_id := (paramJson::json->>'kelahiran_id')::int4;
         vpendaftaranasal_id := (paramJson::json->>'pendaftaranasal_id')::int4;
         
         IF (dataPasien::json->>'nama_pasien' IS NOT NULL AND NEW.pasien_id IS NULL) THEN
                        INSERT INTO pasien_m (
                            tgl_rekam_medik,
                            jenisidentitas,
                            no_identitas_pasien,
                            namadepan,
                            nama_pasien,
                            nama_bin,
                            jeniskelamin,
                            tempat_lahir,
                            tanggal_lahir,
                            golonganumur_id,
                            alamat_pasien,
                            rt,
                            rw,
                            propinsi_id,
                            kabupaten_id,
                            kecamatan_id,
                            kelurahan_id,
                            pendidikan_id,
                            pekerjaan_id,
                            suku_id,
                            statusperkawinan,
                            agama,
                            golongandarah,
                            rhesus,
                            anakke,
                            jumlah_bersaudara,
                            no_telepon_pasien,
                            no_mobile_pasien,
                            warga_negara,
                            photopasien,
                            alamatemail,
                            nama_ibu,
                            nama_ayah,
                            statusrekammedis,
                            alamat_sekarang,
                            is_aps,
                            created_by
                        ) VALUES (
                            (dataPasien::json->>'tgl_rekam_medik')::DATE,
                            
                            (CASE
                                dataPasien::json->>'jenisidentitas'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jenisidentitas')::INTEGER END),
                            
                            dataPasien::json->>'no_identitas_pasien',
                            
                            (CASE
                                dataPasien::json->>'namadepan'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'namadepan')::INTEGER END),
                            
                            dataPasien::json->>'nama_pasien',
                            dataPasien::json->>'nama_bin',
                            
                            (CASE
                                dataPasien::json->>'jeniskelamin'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jeniskelamin')::INTEGER END),
                            
                            dataPasien::json->>'tempat_lahir',
                            (dataPasien::json->>'tanggal_lahir')::DATE,
                            (dataPasien::json->>'golonganumur_id')::INTEGER,
                            (dataPasien::json->>'alamat_pasien'),
                            (CASE
                                dataPasien::json->>'rt'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rt')::INTEGER END),
                            (CASE
                                dataPasien::json->>'rw'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rw')::INTEGER END),
                            (CASE
                                dataPasien::json->>'propinsi_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'propinsi_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'kabupaten_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kabupaten_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'kecamatan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kecamatan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'kelurahan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kelurahan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'pendidikan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'pendidikan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'pekerjaan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'pekerjaan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'suku_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'suku_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'statusperkawinan'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'statusperkawinan')::INTEGER END),
                            (CASE
                                dataPasien::json->>'agama'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'agama')::INTEGER END),
                            (CASE
                                dataPasien::json->>'golongandarah'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'golongandarah')::INTEGER END),
                            (CASE
                                dataPasien::json->>'rhesus'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rhesus')::INTEGER END),
                            (CASE
                                dataPasien::json->>'anakke'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'anakke')::INTEGER END),
                            (CASE
                                dataPasien::json->>'jumlah_bersaudara'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jumlah_bersaudara')::INTEGER END),
                            dataPasien::json->>'no_telepon_pasien',
                            dataPasien::json->>'no_mobile_pasien',
                            (CASE
                                dataPasien::json->>'warga_negara'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'warga_negara')::INTEGER END),
                            
                            dataPasien::json->>'photopasien',
                            dataPasien::json->>'alamatemail',
                            dataPasien::json->>'nama_ibu',
                            dataPasien::json->>'nama_ayah',
                            336,
                            dataPasien::json->>'alamat_sekarang',
                            (dataPasien::json->>'is_aps')::BOOLEAN,
                            NEW.created_by
                        ) RETURNING pasien_id INTO pasienId;
                        NEW.pasien_id := pasienId;
         END IF;
         
         IF (dataAsuransi::json->>'nokartuasuransi' IS NOT NULL) THEN
                noAsuransi := dataAsuransi::json->>'nokartuasuransi';
                SELECT 
                    asuransipasien_id,
                    pasien_id
                INTO
                    idAsuransi,
                    idPasienAsuransi
                FROM asuransipasien_m
                WHERE nokartuasuransi = noAsuransi
                AND penjamin_id = NEW.penjamin_id
                AND pasien_id = NEW.pasien_id
                AND carabayar_id = NEW.carabayar_id;
                
--              IF (idPasienAsuransi != NEW.pasien_id) THEN
-- -- Sementara case asuranasi
-- --                   RAISE EXCEPTION 'Duplicate No Asuransi: %', dataAsuransi::json->>'nokartuasuransi' 
-- --                           USING HINT = 'No Asuransi Sudah digunakan';
--              ELSE
                IF (idAsuransi IS NULL) THEN
                    INSERT INTO asuransipasien_m (
                        kelastanggunganasuransi_id,
                        namapemilikasuransi,
                        namaperusahaan,
                        nokartuasuransi,
                        nomorpokokperusahaan,
                        status_konfirmasi,
                        tgl_konfirmasi,
                        created_by,
                        pasien_id,
                        penjamin_id,
                        carabayar_id
                    ) VALUES (
                        (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                        END),
                        dataAsuransi::json->>'namapemilikasuransi',
                        dataAsuransi::json->>'namaperusahaan',
                        dataAsuransi::json->>'nokartuasuransi',
                        dataAsuransi::json->>'nomorpokokperusahaan',
                        dataAsuransi::json->>'status_konfirmasi',
                        (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                        NEW.created_by,
                        NEW.pasien_id,
                        NEW.penjamin_id,
                        NEW.carabayar_id
                    ) RETURNING asuransipasien_id INTO idAsuransi;
                    
                    NEW.asuransipasien_id = idAsuransi;
                ELSE 
                        UPDATE asuransipasien_m SET 
                            kelastanggunganasuransi_id = (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                            END), 
                            namapemilikasuransi = dataAsuransi::json->>'namapemilikasuransi',
                            namaperusahaan = dataAsuransi::json->>'namaperusahaan',
                            nomorpokokperusahaan = dataAsuransi::json->>'nomorpokokperusahaan',
                            status_konfirmasi = dataAsuransi::json->>'status_konfirmasi',
                            tgl_konfirmasi = (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                            last_modified_by = NEW.created_by
                        WHERE asuransipasien_id = idAsuransi;           
                        
                        NEW.asuransipasien_id = idAsuransi;
                END IF;
--              END IF;
         END IF;

         IF (dataPenanggung::json->>'pj_pengantar' IS NOT NULL) THEN
                INSERT INTO penanggungjawab_m (
                    pengantar,
                    jenisidentitas,
                    no_identitas,
                    hubungankeluarga,
                    penanggungjawab_nama,
                    penanggungjawab_tempatlahir,
                    penanggungjawab_tgllahir,
                    penanggungjawab_jeniskelamin,
                    penanggungjawab_alamat,
                    penanggungjawab_notelp,
                    penanggungjawab_nohp,
                    pasien_id,
                    created_by
                ) VALUES (
                    dataPenanggung::json->>'pj_pengantar',
                    dataPenanggung::json->>'pj_jenis_identitas',
                    dataPenanggung::json->>'pj_no_identitas',
                    dataPenanggung::json->>'pj_hubungan',
                    dataPenanggung::json->>'pj_nama',
                    dataPenanggung::json->>'pj_tempat_lahir',
                    (dataPenanggung::json->>'pj_tanggal_lahir')::DATE,
                    dataPenanggung::json->>'pj_jk',
                    dataPenanggung::json->>'pj_alamat',
                    dataPenanggung::json->>'pj_no_telepon',
                    dataPenanggung::json->>'pj_no_telepon',
                    NEW.pasien_id,
                    NEW.created_by
                ) RETURNING penanggungjawab_id INTO penanggungId;
                                NEW.penanggungjawab_id = penanggungId;
         END IF;
         
         
                 -- Order MCU
                     IF (dataOrderMcu::json->>'penunjang' IS NOT NULL) THEN
                            IF (json_array_length((dataOrderMcu::json->>'penunjang')::json) > 0) THEN
                                    INSERT INTO pasienmasukpenunjang_t (
                                                                    kelaspelayanan_id,
                                                                    jeniskasuspenyakit_id,
                                                                    pasienadmisi_id,
                                                                    pegawai_id,
                                                                    ruangan_id,
                                                                    pasien_id,
                                                                    pendaftaran_id,
                                                                    ruanganasal_id,
                                                                    tglmasukpenunjang,
                                                                    kunjungan,
                                                                    status_periksa,
                                                                    is_bayar,
                                                                    instalasiasal_id,
                                                                    no_antrian,
                                                                    created_by
                             ) SELECT 
                                                                    kelaspelayanan_id,
                                                                    jeniskasuspenyakit_id,
                                                                    NEW.pasienadmisi_id,
                                                                    pegawai_id,
                                                                    ruangan_id,
                                                                    NEW.pasien_id,
                                                                    NEW.pendaftaran_id,
                                                                    ruanganasal_id,
                                                                    tglmasukpenunjang,
                                                                    kunjungan,
                                                                    status_periksa,
                                                                    is_bayar,
                                                                    instalasiasal_id,
                                                                    no_antrian,
                                                                    NEW.created_by as created_by
                                    FROM json_populate_recordset(null::pasienmasukpenunjang_t,(dataOrderMcu::json->>'penunjang')::json);
                            END IF;
                     END IF;
                     
                     -- Konsul Poli
                     IF (dataOrderMcu::json->>'konsul' IS NOT NULL) THEN
                            IF (json_array_length((dataOrderMcu::json->>'konsul')::json) > 0) THEN
                                    INSERT INTO konsulpoli_t (
                                                                    ruangan_id,
                                                                    pegawai_id,
                                                                    tindakanpelayanan_id,
                                                                    pendaftaran_id,
                                                                    pasien_id,
                                                                    tgl_konsulpoli,
                                                                    asalpoliklinikkonsul_id,
                                                                    status_periksa,
                                                                    no_antriankonsul,
                                                                    created_by
                             ) SELECT 
                                                                    ruangan_id,
                                                                    pegawai_id,
                                                                    tindakanpelayanan_id,
                                                                    NEW.pendaftaran_id,
                                                                    NEW.pasien_id,
                                                                    tgl_konsulpoli,
                                                                    asalpoliklinikkonsul_id,
                                                                    status_periksa,
                                                                    no_antriankonsul,
                                                                    NEW.created_by as created_by
                                    FROM json_populate_recordset(null::konsulpoli_t,(dataOrderMcu::json->>'konsul')::json);
                            END IF;
                     END IF;                
                     
         -- Set Antrian 
         IF (dataAntrian::json->>'jenisantrian_id' IS NOT NULL AND NEW.antrian_id IS NULL) THEN
                     --get konfigantrian
                    SELECT konfigantrian_id INTO v_konfigantrian
                    from konfigantrian_m
                    WHERE jenisantrian_id = (dataAntrian::json->>'jenisantrian_id')::INTEGER 
                    and konfigantrian_m.is_deleted=FALSE 
                    and konfigantrian_m.is_active=true
                    limit 1;
 
                INSERT INTO antrian_t (
                                pasien_id,
                                ruangan_id,
                                carabayar_id,
                                pendaftaran_id,
                                tgl_antrian,
                                penjamin_id,
                                pegawai_id,
                                status_pasien,
                                jenisantrian_id,
                                is_active,
                                created_by,
                                konfigantrian_id
             ) VALUES (
                                NEW.pasien_id,
                                (dataAntrian::json->>'ruangan_id')::INTEGER,
                                (dataAntrian::json->>'carabayar_id')::INTEGER,
                                NEW.pendaftaran_id,
                                (dataAntrian::json->>'tgl_antrian')::TIMESTAMP,
                                (dataAntrian::json->>'penjamin_id')::INTEGER,
                                (dataAntrian::json->>'pegawai_id')::INTEGER,
                                (dataAntrian::json->>'status_pasien')::INTEGER,
                                (dataAntrian::json->>'jenisantrian_id')::INTEGER,
                                FALSE,
                                NEW.created_by,
                                v_konfigantrian
             ) RETURNING antrian_id INTO antrianId;
                NEW.antrian_id = antrianId;
                
                --- Ini Kondisi Penunjang GET nomor antrian untuk pasien masuk penunjang
                IF (NEW.carabayar_id != 5 AND countPenunjang > 0) THEN
                        SELECT 
                            no_antrian
                        INTO 
                            noAntrian
                        FROM antrian_t WHERE antrian_id = antrianId;
                END IF;
         ELSE
                -- Update Antrian Pendaftaran
                UPDATE antrian_t SET 
                    pendaftaran_id = NEW.pendaftaran_id, 
                    pasien_id = NEW.pasien_id 
                WHERE antrian_id = NEW.antrian_id;
                
                IF (countPenunjang > 0) THEN
                        IF (NEW.carabayar_id != 5) THEN
                            SELECT 
                                no_antrian
                            INTO 
                                noAntrian
                            FROM antrian_t WHERE antrian_id = NEW.antrian_id;
                        END IF;
                ELSE
                    -- Validasi Untuk Non Penunjang 
                    isActive := false;
                    IF (NEW.carabayar_id != 5 OR countTagihan <= 0) THEN
                        isActive := true;
                    END IF;
                    -- Update Pendaftan Poli
                    UPDATE antrian_t SET 
                        pendaftaran_id = NEW.pendaftaran_id, 
                        pasien_id = NEW.pasien_id, 
                        ruangan_id = NEW.ruangan_id,
                        carabayar_id = NEW.carabayar_id,
                        is_active = isActive
                    WHERE antrianasal_id = NEW.antrian_id;  
                END IF;
         END IF;
         
         -- Set Rujukan Jika Ada
         IF (dataRujukan::json->>'rujukandari_id' IS NOT NULL) THEN
                INSERT INTO rujukan_t (
                                asalrujukan_id,
                                rujukandari_id,
                                diagnosa_id,
                                no_rujukan,
                                nama_perujuk,
                                tanggal_rujukan,
                                created_by
             ) VALUES (
                                (dataRujukan::json->>'asalrujukan_id')::INTEGER,
                                (dataRujukan::json->>'rujukandari_id')::INTEGER,
                                (CASE
                                    dataRujukan::json->>'diagnosa_id'
                                WHEN NULL 
                                THEN NULL 
                                ELSE (dataPasien::json->>'diagnosa_id')::INTEGER END),
                                dataRujukan::json->>'no_rujukan',
                                dataRujukan::json->>'nama_perujuk',
                                (dataRujukan::json->>'tanggal_rujukan')::TIMESTAMP,
                                NEW.created_by
             ) RETURNING rujukan_id INTO rujukanId;
                NEW.rujukan_id = rujukanId;
         END IF;
         
    --- Kondisi Pendaftaran Penunjang
        IF (countPenunjang > 0) then
                INSERT INTO pasienmasukpenunjang_t (
                        kelaspelayanan_id,
                        jeniskasuspenyakit_id,
                        pegawai_id,
                        ruangan_id,
                        pasien_id,
                        pendaftaran_id,
                        tglmasukpenunjang,
                        no_antrian,
                        status_periksa,
                        ruanganasal_id,
                        instalasiasal_id,
                        created_by
                ) VALUES (
                        NEW.kelaspelayanan_id,
                        NEW.jeniskasuspenyakit_id,
                        NEW.pegawai_id,
                        NEW.ruangan_id,
                        NEW.pasien_id,
                        NEW.pendaftaran_id,
                        NEW.tgl_pendaftaran,
                        noAntrian,
                        CASE new.instalasi_id::int4
                            WHEN 12 THEN 488
                            ELSE 477
                        end,
--                        477,
                        NEW.ruangan_id,
                        new.instalasi_id,
                        NEW.created_by
                ) RETURNING pasienmasukpenunjang_id INTO idPenunjang;
                INSERT INTO tindakanpelayanan_t (
                                pasienmasukpenunjang_id,
                                kelaspelayanan_id,
                                pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                tipepaket_id,
                                carabayar_id,
                                pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                                additional_data,
                                created_by
             ) SELECT 
                                idPenunjang as pasienmasukpenunjang_id,
                                kelaspelayanan_id,
                                NEW.pasien_id as pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                tipepaket_id,
                                carabayar_id,
                                NEW.pendaftaran_id as pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                                additional_data,
                                NEW.created_by as created_by
                FROM json_populate_recordset(null::tindakanpelayanan_t,vPenunjang::json);
        END IF;
    
        -- Pendafatran Online
        IF (paramJson::json->>'pendaftaranol_id' IS NOT NULL) THEN
            UPDATE pendaftaranol_t
                SET pendaftaran_id = NEW.pendaftaran_id,
                status_daftar_ol = 565
            WHERE pendaftaranol_id = (paramJson::json->>'pendaftaranol_id')::INTEGER;           
        END IF;
    
    -- Insert Admisi
     IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
            INSERT INTO pasienadmisi_t (
                            pendaftaran_id,
                            carabayar_id,
                            penjamin_id,
                            ruangan_id,
                            pasien_id,
                            kamarruangan_id,
                            kamartempattidur_id,
                            kelaspelayanan_id,
                            pegawai_id,
                            tgl_admisi,
                            tgl_pendaftaran,
                            kunjungan,
                            bpjs_id,
                            status_ranap,
                            status_verifikasi,
                            is_skd,
                            is_pasientitipan,
                            created_by,
                            is_aps,
                            asuransipasien_id
                    
         ) VALUES (
                            NEW.pendaftaran_id,
                            vcarabayar_id,
                            vpenjamin_id,
                            (vadmisi::json->>'ruangan_id')::INTEGER,
                            NEW.pasien_id,
                            (vadmisi::json->>'kamarruangan_id')::INTEGER,
                            (vadmisi::json->>'kamartempattidur_id')::INTEGER,
                            (vadmisi::json->>'kelaspelayanan_id')::INTEGER,
                            (vadmisi::json->>'pegawai_id')::INTEGER,
                            (vadmisi::json->>'tgl_admisi')::TIMESTAMP,
                            (vadmisi::json->>'tgl_pendaftaran')::TIMESTAMP,
                            (vadmisi::json->>'kunjungan')::INTEGER,
                            (vadmisi::json->>'bpjs_id')::INTEGER,
                            (vadmisi::json->>'status_ranap')::INTEGER,
                            (vadmisi::json->>'status_verifikasi')::INTEGER,
                            (vadmisi::json->>'is_skd')::BOOL,
                            (vadmisi::json->>'is_pasientitipan')::BOOL,
                            NEW.created_by,
                            (vadmisi::json->>'is_aps')::BOOL,
                            idAsuransi
                            
                    
                            
         ) RETURNING pasienadmisi_id INTO vpasienadmisi_id;
            
            NEW.pasienadmisi_id = vpasienadmisi_id;
        

            UPDATE pendaftaran_t
            SET is_ranap = TRUE
            WHERE pendaftaran_id = vpendaftaranasal_id;
             -- Insert Masuk Kamar
            IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
                INSERT INTO masukkamar_t (
                                pasienadmisi_id,
                                carabayar_id,
                                penjamin_id,
                                ruangan_id,
                                pegawai_id,
                                kelaspelayanan_id,
                                kamartempattidur_id,
                                kamarruangan_id,
                                tgl_masukkamar,
                                jam_masukkamar,
                                created_by
             ) VALUES (
                                vpasienadmisi_id,
                                vcarabayar_id,
                                vpenjamin_id,
                                (vmasukkamar::json->>'ruangan_id')::INTEGER,
                                (vmasukkamar::json->>'pegawai_id')::INTEGER,
                                (vmasukkamar::json->>'kelaspelayanan_id')::INTEGER,
                                (vmasukkamar::json->>'kamartempattidur_id')::INTEGER,
                                (vmasukkamar::json->>'kamarruangan_id')::INTEGER,
                                (vmasukkamar::json->>'tgl_masukkamar')::DATE,
                                (vmasukkamar::json->>'jam_masukkamar')::TIME,
                                NEW.created_by
             ) ;
            END IF;
             
            SELECT 
                CASE pasien_m.jeniskelamin::int4
                    WHEN 15 THEN 4
                    ELSE 3
                END INTO vkettempattidur_id
            FROM pasien_m
            WHERE pasien_id = (vadmisi::json->>'pasien_id')::INTEGER;

            UPDATE kamartempattidur_m
            SET status_isi = TRUE,
                        kettempattidur_id = vkettempattidur_id
            WHERE kamartempattidur_id = (vadmisi::json->>'kamartempattidur_id')::INTEGER;

            IF(COALESCE(vkelahiran_id,0) <> 0)
            THEN
                UPDATE kelahiranbayi_t
                SET pendaftaranbaru_id = NEW.pendaftaran_id,
                        last_modified_by = NEW.created_by,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE kelahiranbayi_id = vkelahiran_id;
            END IF;
            
     END IF;
        
        
         -- Tagihan Karcis
         IF (countTagihan > 0) THEN
                INSERT INTO tindakanpelayanan_t (
                                kelaspelayanan_id,
                                pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                                                tipepaket_id,
                                carabayar_id,
                                pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                                pasienadmisi_id,
                                additional_data,
                                created_by
             ) SELECT 
                                kelaspelayanan_id,
                                NEW.pasien_id as pasien_id,
                                instalasi_id,
                                daftartindakan_id,
                                                                tipepaket_id,
                                carabayar_id,
                                NEW.pendaftaran_id as pendaftaran_id,
                                jeniskasuspenyakit_id,
                                ruangan_id,
                                penjamin_id,
                                tgl_tindakan,
                                dokterpenanggungjawab_id,
                                tarif_satuan,
                                qty_tindakan,
                                tarif_tindakan,
                                tarifcyto_tindakan,
                                cyto_tindakan,
                                discount_tindakan,
                NEW.pasienadmisi_id,
                                additional_data,
                                NEW.created_by as created_by
                FROM json_populate_recordset(null::tindakanpelayanan_t,dataKarcis::json);
         END IF;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pesanbarangdetail_t_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE
vpesanbarangdetail_id int4;
vpesanbarang_id int4;
vbarang_id_new int4;
vbarang_id_old int4;
vqty_pesan_new int4;
vqty_pesan_old int4;
vqty_pesan_sisa int4;
vruangan_id int4;
vcreated_by int4;

BEGIN
    vpesanbarangdetail_id := NEW.pesanbarangdetail_id;
    vpesanbarang_id := NEW.pesanbarang_id;
    vbarang_id_new := NEW.barang_id;
    vbarang_id_old := OLD.barang_id;
    vqty_pesan_new := NEW.qty_pesan;
    vqty_pesan_old := OLD.qty_pesan;
    vcreated_by := NEW.created_by;
    
    SELECT 
        pesanbarang_t.ruangantujuan_id
    INTO
        vruangan_id
    FROM
        pesanbarang_t
    WHERE pesanbarang_t.pesanbarang_id = vpesanbarang_id;
    
    IF vbarang_id_new != vbarang_id_old
    THEN
        IF EXISTS(
            SELECT *
            FROM stokbarang_r
            WHERE barang_id = vbarang_id_new
            AND ruangan_id = vruangan_id
            LIMIT 1
        )
        THEN
            UPDATE stokbarang_r
            SET qty_dipesan = qty_dipesan + vqty_pesan_new,
            last_modified_date = CURRENT_TIMESTAMP, 
            last_modified_by = vcreated_by
            WHERE barang_id = vbarang_id_new
            AND ruangan_id = vruangan_id;
            
            UPDATE stokbarang_r
            SET qty_dipesan = qty_dipesan - vqty_pesan_old,
            last_modified_date = CURRENT_TIMESTAMP, 
            last_modified_by = vcreated_by
            WHERE barang_id = vbarang_id_old
            AND ruangan_id = vruangan_id;
            
        ELSE
            UPDATE stokbarang_r 
            SET qty_dipesan = qty_dipesan - vqty_pesan_old
            WHERE ruangan_id = vruangan_id
            AND barang_id = vbarang_id_old;
            
            INSERT INTO stokbarang_r (
                ruangan_id, barang_id, qty_awal, qty_masuk, qty_keluar, qty_sisa, qty_tersedia,
                qty_dipesan, created_date, created_by, is_deleted, is_active
            )VALUES(
                vruangan_id, vbarang_id_new, 0, 0, 0, 0, 0, 
                vqty_pesan_new, CURRENT_TIMESTAMP, vcreated_by, 'f', 't'
            );
        END IF;
    ELSE
        IF vqty_pesan_new >= vqty_pesan_old
        THEN
            vqty_pesan_sisa := vqty_pesan_new - vqty_pesan_old;
            
            UPDATE stokbarang_r
            SET qty_dipesan = qty_dipesan + vqty_pesan_sisa,
            last_modified_date = CURRENT_TIMESTAMP, 
            last_modified_by = vcreated_by
            WHERE barang_id = vbarang_id_new
            AND ruangan_id = vruangan_id;
        
        ELSIF vqty_pesan_new <= vqty_pesan_old
        THEN
            vqty_pesan_sisa :=  vqty_pesan_old - vqty_pesan_new;
            
            UPDATE stokbarang_r
            SET qty_dipesan = qty_dipesan - vqty_pesan_sisa,
            last_modified_date = CURRENT_TIMESTAMP, 
            last_modified_by = vcreated_by
            WHERE barang_id = vbarang_id_new
            AND ruangan_id = vruangan_id;
        ELSE
            RETURN NEW;
        END IF;
    END IF;
    
    RETURN NEW;
END;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('DROP TRIGGER "trigger_update_kamartempattidur_m" ON "public"."kamartempattidur_m";');

        $this->execute('
            CREATE TRIGGER "trigger_update_kamartempattidur_m" AFTER UPDATE OF "kamarruangan_id", "is_deleted", "is_active" ON "public"."kamartempattidur_m"
FOR EACH ROW
EXECUTE PROCEDURE "public"."kamartempattidur_m_update"();');

        $this->execute('DROP TRIGGER "update_reseptur" ON "public"."reseptur_t";');

        $this->execute('CREATE TRIGGER "update_reseptur" AFTER UPDATE OF "status_reseptur" ON "public"."reseptur_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."update_stokobatalkes_r_batalreseptur"();');

        $this->execute('ALTER TABLE "public"."reseptur_t" DISABLE TRIGGER "update_reseptur";');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200621_020752_migrate_function_mhkn_20200621 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200621_020752_migrate_function_mhkn_20200621 cannot be reverted.\n";

        return false;
    }
    */
}
