<?php

use yii\db\Migration;

/**
 * Class m201020_061825_oddo_penyesuaianfunction_20201020
 */
class m201020_061825_oddo_penyesuaianfunction_20201020 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_no_pemakaianobat\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 29; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    v_Nomor varchar;
    v_day VARCHAR;
    v_reset VARCHAR;
    
BEGIN
    SELECT date_part('DAY',now()) INTO v_day;
    
    IF(v_day = '1')
    THEN
        SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) AS VARCHAR(5)), 5, '0'))
            INTO v_reset
        FROM penomoran_k WHERE penomoran_id = vId;
        
        IF(v_reset <> '00001')
        THEN
            UPDATE penomoran_k SET 
                last_generate = '00001'
            WHERE penomoran_id = vId;
        END IF;
        
    END IF;
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(5)), 5, '0')) last_no
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

    NEW.nopemakaian_obat = v_Nomor;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_obatalkespasien\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 70; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    
BEGIN
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(5)), 5, '0')) last_no
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
    
    NEW.no_obatalkespasien = v_Nomor;

    RETURN NEW;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_tindakanpelayanan\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 69; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    
BEGIN
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(5)), 5, '0')) last_no
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
    
    NEW.no_tindakanpelayanan = v_Nomor;

    RETURN NEW;
END\$BODY\$
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
                                                                                jenisnontunai_id,
                                                                                edclist_id,
                                                                                nama_edc,
                                                                                created_by,
                                                                                pendaftaran_id

                     ) VALUES (
                                        vPembayaran_id,
                                        (vrow->>'metode_bayar')::VARCHAR,
                                        (vrow->>'no_kartu')::VARCHAR,
                                        (vrow->>'total_dibayar')::FLOAT,                     
                                        (vrow->>'jenisnontunai_id')::INTEGER,                     
                                        (vrow->>'edclist_id')::INTEGER,                     
                                        (vrow->>'label_edc')::VARCHAR,
                                                                                NEW.created_by,
                                                                                NEW.pendaftaran_id
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
                                                    total_diskon,
                                                                                                        alasan
                            ) VALUES (
                                                    vPembayaran_id,
                                                    (vrow->>'pegawai_id')::INTEGER,
                                                    (vrow->>'komponentarif_id')::INTEGER,
                                                    (vrow->>'total_komponentarif')::FLOAT,
                                                    (vrow->>'total_diskon')::FLOAT,
                                                    (vrow->>'alasan')::TEXT
                            );
                            END IF;
                    END LOOP;
                    
                    
                RETURN NEW;
            END
            \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"komponen_sudahbayar\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$-- author yaya

DECLARE
    paramJson VARCHAR;
    vKompenen VARCHAR;
BEGIN

  paramJson := NEW.additional_data;  
  vKompenen := paramJson::json->>'data';
    
    DELETE FROM tindakankomponen_t WHERE tindakanpelayanan_id = NEW.tindakanpelayanan_id;
    
    -- Ini untuk insert ke tindakankomponen_t
  IF (json_array_length(vKompenen::json) > 0)  THEN
            INSERT INTO tindakankomponen_t (
                            komponentarif_id,
                            tindakanpelayanan_id,
                            tarif_kompsatuan,
                            tarif_tindakankomp,
                            tarifcyto_tindakankomp,
                            subsidiasuransikomp,
                            subsidipemerintahkomp,
                            iurbiayakomp,
                            created_by
         ) SELECT 
                            komponentarif_id,
                            NEW.tindakanpelayanan_id as tindakanpelayanan_id, 
                            tarif_kompsatuan,
                            tarif_tindakankomp,
                            tarifcyto_tindakankomp,
                            subsidiasuransikomp,
                            subsidipemerintahkomp,
                            iurbiayakomp,
              NEW.created_by as created_by
            FROM json_populate_recordset(null::tindakankomponen_t,vKompenen::json);
    END IF;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_pembayaranpelayanan_t\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$-- author yaya

DECLARE
    vId INTEGER := 1;  
    vPrefix VARCHAR;
    vLast VARCHAR;
    vYear VARCHAR;
    vMonth VARCHAR;
    vNomor VARCHAR;
        v_day VARCHAR;
        v_reset VARCHAR;
    paramJson VARCHAR;

  vTPembayaran FLOAT;
  vAdmin FLOAT;
  vNamaBkm VARCHAR;
    vShifId INTEGER;
  vKeterangan VARCHAR;
  vCaraPembayaran VARCHAR;
  vTandaBuktiBayarId INTEGER;
  vPegawaiId INTEGER;
  vDataObat VARCHAR;
  vDataTindakan VARCHAR;
  vIdPendaftaran INTEGER;
  vJumlahUangMuka INTEGER;
    vUangDiterima FLOAT;
  vIdPemakaianUang INTEGER;
    vUangKembalian FLOAT;
BEGIN
    SELECT date_part('DAY',now()) INTO v_day;
    
    IF(v_day = '1')
    THEN
        SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) AS VARCHAR(5)), 5, '0'))
            INTO v_reset
        FROM penomoran_k WHERE penomoran_id = vId;
        
        IF(v_reset <> '00001')
        THEN
            UPDATE penomoran_k SET 
                last_generate = '00001'
            WHERE penomoran_id = vId;
        END IF;
        
    END IF;

 -- Untuk Penomoran 
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(5)), 5, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    vNomor = vPrefix || vYear || vMonth || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = vNomor
    WHERE penomoran_id = vId;

    NEW.no_pembayaran = vNomor;
 -- End Penomoran

  -- NEW.total_bayartindakan := NEW.total_biayapelayanan - (NEW.total_subsidirs + NEW.total_subsidiasuransi + NEW.total_subsidipemerintah);
  NEW.total_iurbiaya := NEW.total_biayapelayanan - (NEW.total_subsidirs + NEW.total_subsidiasuransi + NEW.total_subsidipemerintah);
  NEW.total_terbayar := NEW.total_iurbiaya;
  NEW.total_sisatagihan = NEW.total_iurbiaya - NEW.total_bayartindakan;
  vUangKembalian := NEW.total_bayartindakan - NEW.total_iurbiaya;
  IF (NEW.total_bayartindakan > NEW.total_iurbiaya) THEN
      NEW.total_sisatagihan = 0;
  END IF;

  NEW.statusbayar = 349;
  NEW.is_lunas = FALSE;
    
  IF (NEW.total_iurbiaya > NEW.total_bayartindakan) THEN
      NEW.total_terbayar := NEW.total_bayartindakan;
            IF (vCaraPembayaran = '31') THEN
                    NEW.is_lunas = FALSE;
     END IF;
END IF;

  vIdPendaftaran := NEW.pendaftaran_id;
  paramJson := NEW.additional_data;  
    vDataObat := paramJson::json->>'obat';
  vDataTindakan := paramJson::json->>'tindakan';

  -- Insert ke  pembayaranpelayanan_t
  IF (json_array_length(vDataTindakan::json) > 0)  THEN
            INSERT INTO tindakansudahbayar_t (
                            pembayaranpelayanan_id, 
                            tindakanpelayanan_id, 
                            daftartindakan_id, 
                            ruangan_id,
                            qty_tindakan,
                            jmlbiaya_tindakan,
                            jmlsubsidi_asuransi,
                            jmlsubsidi_pemerintah,
                            jmlsubsidi_rs,
                            jmliur_biaya,
                            jml_pembebasan,
                            jmlbayar_tindakan,
                            jml_sisabayar_tindakan,
                            tipepaket_id,
                            pembulatan,
                            additional_data,
                            created_by
         ) SELECT 
                            NEW.pembayaranpelayanan_id as pembayaranpelayanan_id, 
                            tindakanpelayanan_id, 
                            daftartindakan_id, 
                            ruangan_id,
                            qty_tindakan,
                            jmlbiaya_tindakan,
                            jmlsubsidi_asuransi,
                            jmlsubsidi_pemerintah,
                            jmlsubsidi_rs,
                            jmliur_biaya,
                            jml_pembebasan,
                            jmlbayar_tindakan,
                            jml_sisabayar_tindakan,
                            tipepaket_id,
                            pembulatan,
                            additional_data,
              NEW.created_by as created_by
            FROM json_populate_recordset(null::tindakansudahbayar_t,vDataTindakan::json);
     -- Setelah insert updatekan ke tindakanpelayanan_t
    UPDATE tindakanpelayanan_t 
                SET tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id,
                                        carabayar_id = NEW.carabayar_id,
                                        penjamin_id = NEW.penjamin_id,
                    tarif_tindakan = tindakansudahbayar_t.jmlbiaya_tindakan,
                    tarif_satuan = (tindakansudahbayar_t.additional_data::json->>'tarif_satuan')::INTEGER,
                    tarifcyto_tindakan = (tindakansudahbayar_t.additional_data::json->>'tarif_cyto')::INTEGER,
                    tarif_dijamin = ((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin')::float,
                    tarif_dibayarkan = ((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float,
                    is_valid = TRUE
                FROM tindakansudahbayar_t 
    WHERE (tindakanpelayanan_t.tindakansudahbayar_id IS NULL OR tindakanpelayanan_t.tipepaket_id IS NULL)
                AND tindakansudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id
        AND tindakansudahbayar_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id ;
    END IF;

  -- Insert ke  obatsudahbayar_t
  IF (json_array_length(vDataObat::json) > 0)  THEN
            INSERT INTO obatsudahbayar_t (
                             pembayaranpelayanan_id, 
                            ruangan_id, 
                            obatalkes_id, 
                            obatalkespasien_id, 
                            qty_obat, 
                            hargasatuan, 
                            jmlsubsidi_asuransi, 
                            jmlsubsidi_pemerintah,
                            jmlsubsidi_rs,
                            jmliurbiaya,
                            jmlbayar_obat,
                            jmlsisabayar_obat,
                            pembulatan,
                                                        created_by,
                                                        additional_data
            ) SELECT 
                            NEW.pembayaranpelayanan_id as pembayaranpelayanan_id, 
                            ruangan_id, 
                            obatalkes_id, 
                            obatalkespasien_id, 
                            qty_obat, 
                            hargasatuan, 
                            jmlsubsidi_asuransi, 
                            jmlsubsidi_pemerintah,
                            jmlsubsidi_rs,
                            jmliurbiaya,
                            jmlbayar_obat,
                            jmlsisabayar_obat,
                            pembulatan,
                                                        NEW.created_by as created_by,
                                                        additional_data
            FROM json_populate_recordset(null::obatsudahbayar_t,vDataObat::json);
     -- Setelah insert updatekan ke obat alkes pasien
    UPDATE obatalkespasien_t 
            SET obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id,
          carabayar_id = NEW.carabayar_id,
          penjamin_id = NEW.penjamin_id,
                    tarif_dijamin = ((obatsudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin')::float,
          tarif_dibayarkan = ((obatsudahbayar_t.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float
      FROM obatsudahbayar_t 
            WHERE obatalkespasien_t.obatsudahbayar_id IS NULL 
                AND obatsudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id
        AND obatsudahbayar_t.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id ;
        -- Update pembayaran Resep
        UPDATE penjualanresep_t 
                SET status_bayar = 348
            FROM obatalkespasien_t
                JOIN obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
            WHERE obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id 
                    AND obatsudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
    END IF;
   
  NEW.additional_data := NULL;
  vAdmin := paramJson::json->>'biayaadministrasi';
  vKeterangan := paramJson::json->>'sebagaipembayaran_bkm';
  vNamaBkm := paramJson::json->>'darinama_bkm';
  vCaraPembayaran := paramJson::json->>'carapembayaran';
  vPegawaiId := paramJson::json->>'pegawai_id';
  vJumlahUangMuka := paramJson::json->>'jumlah_uangmuka';
    vShifId := paramJson::json->>'shift_id';

 -- Ini Status Ketika jaminan kalo perorangan dcheck ada pemakaian uang muka kalo ada insert ke pemakaian uang muka
 -- dan update uangmuka
--   vUangDiterima := NEW.total_terbayar;
--     IF (vCaraPembayaran != '31')
--      THEN
--             NEW.penggunaan_uangmuka := 0;
--       INSERT INTO piutangasuransi_t (
--                         pembayaranpelayanan_id,
--             penjamin_id,
--             carabayar_id,
--             jmlpiutangasuransi,
--             created_by
--         ) VALUES (
--            NEW.pembayaranpelayanan_id,
--            NEW.penjamin_id,
--            NEW.carabayar_id,
--            NEW.total_biayapelayanan,
--            NEW.created_by
--         );
--   ELSE
--             IF (NEW.penggunaan_uangmuka > 0)
--                 THEN
--                         INSERT INTO pemakaianuangmuka_t (
--                             pembayaranpelayanan_id,
--                             pendaftaran_id,
--                             tgl_pemakaian,
--                             total_uangmuka,
--                             pemakaian_uangmuka,
--                             sisa_uangmuka,
--                             created_by
--                         ) VALUES (
--                             NEW.pembayaranpelayanan_id,
--                             vIdPendaftaran,
--                             NEW.tgl_pembayaran,
--                             vJumlahUangMuka,
--                             NEW.penggunaan_uangmuka,
--                             (vJumlahUangMuka - NEW.penggunaan_uangmuka),
--                             NEW.created_by
--                         ) RETURNING pemakaianuangmuka_id INTO vIdPemakaianUang;
--                         
--                         UPDATE bayaruangmuka_t SET
--                             pemakaianuangmuka_id = vIdPemakaianUang
--                         WHERE pendaftaran_id = vIdPendaftaran AND pemakaianuangmuka_id IS NULL;
--             END IF;
--   END IF; 

    vUangDiterima := 0;
    IF (NEW.total_biayapelayanan > NEW.penggunaan_uangmuka)
     THEN
                IF (vCaraPembayaran = '31')
                    THEN
                            vUangDiterima := NEW.total_biayapelayanan -   NEW.penggunaan_uangmuka;
                    END IF;
  END IF;
-- Jika ada pemakaian uang muka
IF (NEW.penggunaan_uangmuka > 0)
        THEN
            IF (NEW.penggunaan_uangmuka > NEW.total_sisatagihan) 
                THEN
                        -- Komen
                        NEW.total_sisatagihan := 0;
                        vUangKembalian := vUangKembalian + NEW.penggunaan_uangmuka;
            ELSE
                        NEW.total_sisatagihan := NEW.total_sisatagihan - NEW.penggunaan_uangmuka;
            END IF;
END IF;

IF (vUangKembalian < 0)
        THEN
        vUangKembalian := 0;
END IF;

  IF (NEW.total_sisatagihan <= 0) THEN
            NEW.statusbayar = 348;
      NEW.is_lunas = TRUE;
    END IF;


 INSERT INTO tandabuktibayar_t (
    ruangan_id, 
    pembayaranpelayanan_id, 
    tglbuktibayar,
    darinama_bkm,
    sebagaipembayaran_bkm,
    jmlpembayaran,
    biayaadministrasi,
    biayamaterai,
    uangditerima,
    uangkembalian,
    namapemilik_rek,
    no_rek,
    carapembayaran,
    pegawai1_id,
    created_by,
    shift_id
  ) VALUES (
    NEW.ruangan_id,
    NEW.pembayaranpelayanan_id,
    NEW.tgl_pembayaran,
    vNamaBkm,
    vKeterangan,
    NEW.total_biayapelayanan,
    NEW.biaya_administrasi,
    0,
    NEW.total_bayartindakan,
    vUangKembalian,
    NEW.nama_pemrekening,
    NEW.no_rekening,
    vCaraPembayaran,
    vPegawaiId,
    NEW.created_by,
    vShifId
  ) RETURNING tandabuktibayar_id INTO vTandaBuktiBayarId;
    NEW.tandabuktibayar_id := vTandaBuktiBayarId;
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembayaranpelayanan_batal\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
  

BEGIN  
----------------------------------pemakaianuangmuka_t-----------------------------
  IF(NEW.is_deleted = TRUE)
    THEN
      UPDATE pemakaianuangmuka_t SET is_deleted = TRUE
      WHERE pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;

  END IF;

----------------------------------pembayaranmetode_t-----------------------------
  IF(NEW.is_deleted = TRUE)
    THEN
      UPDATE pembayaranmetode_t 
                    SET is_deleted = TRUE,
                            deleted_by = NEW.deleted_by
      WHERE pembayaran_id = NEW.pembayaran_id;

  END IF;

RETURN NEW;

END

\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tarifkomponenrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0, "xtipe" varchar=\'pelayanan\'::character varying)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool, "kamarruangan_nokamar" varchar, "kamarruangan_id" int4, "ambulan_id" int4, "no_polisi" varchar, "kelompokpemeriksaanlab_id" int4, "nama_kelompok" varchar, "jenispemeriksaanlab_id" int4, "jenispemeriksaanlab_nama" varchar, "pemeriksaanlab_id" int4, "pemeriksaanlab_nama" varchar, "persen_penyulit" numeric) AS $BODY$ 

DECLARE 

    vpenjamin_id int4;

BEGIN

    IF(xruangan_id = 0)
    THEN
        SELECT default_ruangan INTO xruangan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    IF(xkelaspelayanan_id = 0)
    THEN
        SELECT default_kelas INTO xkelaspelayanan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    SELECT default_penjamin INTO vpenjamin_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xpenjamin_id = vpenjamin_id) 
        THEN xpenjamin_id := 0;
    END IF;
    
    
IF(xtipe = \'pelayanan\')
    THEN
        RETURN QUERY 
            SELECT * FROM 
                (SELECT \'tindakan\'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id,
                r_tindakan.ruangan_nama,
                r_tindakan.instalasi_id,
                ins_tindakan.instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                daftartindakan_m.kelompoktindakan_id,
                kelompoktindakan_m.kelompoktindakan_nama,
                daftartindakan_m.kategoritindakan_id,
                kategoritindakan_m.kategoritindakan_nama,
                COALESCE( tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default,
                daftartindakan_m.is_akomodasi,
                penjamin_m.carabayar_id,
                daftartindakan_m.is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
            FROM tariftindakan_m
                JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
                JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
                LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id          
                LEFT JOIN   (SELECT tariftindakan_m.daftartindakan_id,
                                    tariftindakan_m.tariftindakan_id,
                                    tariftindakan_m.kelaspelayanan_id,
                                    tariftindakan_m.penjamin_id,
                                    tariftindakan_m.harga_tariftindakan,
                                    tariftindakan_m.persencyto_tindakan,
                                    tariftindakan_m.persendiskon_tindakan,
                                    tariftindakan_m.perdatarif_id,
                                    penjamin_m.penjamin_nama,
                                    tariftindakan_m.komponentarif_id
                            FROM tariftindakan_m
                                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                            WHERE komponentarif_m.is_deleted IS FALSE
                                AND perdatarif_m.is_active = TRUE 
                                AND tariftindakan_m.is_deleted = FALSE 
                                AND tariftindakan_m.is_active = TRUE 
                                AND tariftindakan_m.tarifparent_id IS NULL
                                AND tariftindakan_m.komponentarif_id <> 6
                                AND tariftindakan_m.penjamin_id = xpenjamin_id
                            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                    AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
            WHERE tindakanruangan_mp.is_deleted = false 
                AND komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id

       
UNION ALL
        SELECT \'paket\'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id,
                r_paket.ruangan_nama,
                r_paket.instalasi_id,
                ins_paket.instalasi_nama,
                paketruangan_mp.ruangan_id AS ruanganpaket_id,
                r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                NULL::integer AS kelompoktindakan_id,
                NULL::character varying AS kelompoktindakan_nama,
                NULL::integer AS kategoritindakan_id,
                NULL::character varying AS kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id,
                NULL::character varying AS daftartindakan_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                paketruangan_mp.is_default,
                NULL::boolean AS is_akomodasi,
                penjamin_m.carabayar_id,
                false AS is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
        FROM tariftindakan_m
          JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
          JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
          JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
          JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
           JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                        AND perdatarif_m.is_active = TRUE
                                                        AND perdatarif_m.is_deleted = FALSE
          JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                                                                AND paketruangan_mp.is_deleted = FALSE 
          JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
          JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
          LEFT JOIN   (SELECT tariftindakan_m.tipepaket_id,
                                                            tariftindakan_m.daftartindakan_id,
                              tariftindakan_m.tariftindakan_id,
                              tariftindakan_m.kelaspelayanan_id,
                              tariftindakan_m.penjamin_id,
                              tariftindakan_m.harga_tariftindakan,
                              tariftindakan_m.persencyto_tindakan,
                              tariftindakan_m.persendiskon_tindakan,
                              tariftindakan_m.perdatarif_id,
                              penjamin_m.penjamin_nama,
                              tariftindakan_m.komponentarif_id
                      FROM tariftindakan_m
                       JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                    and perdatarif_m.is_active = TRUE
                                                                                    and perdatarif_m.is_deleted = FALSE
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        WHERE tariftindakan_m.is_deleted = FALSE 
                                                    AND tariftindakan_m.is_active = TRUE 
                                                    AND tariftindakan_m.komponentarif_id <> 6
                                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                      ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id
                                                                            AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
        WHERE tariftindakan_m.is_deleted = false 
          AND tariftindakan_m.is_active = true 
          AND tariftindakan_m.komponentarif_id <> 6
          AND tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
        WHERE x.ruangan_id = xruangan_id
          AND x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;

ELSEIF(xtipe = \'kamar\')
    THEN
        RETURN QUERY 
        SELECT * FROM 
          (SELECT \'kamar\'::text AS jenis,
                  COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                  kamarruangan_m.ruangan_id as ruangan_id,
                  ruangan_m.ruangan_nama as ruangan_nama,
                  ruangan_m.instalasi_id as instalasi_id,
                  instalasi_m.instalasi_nama as instalasi_nama,
                  NULL::integer AS ruanganpaket_id,
                  NULL::character varying AS ruanganpaket_nama,
                  COALESCE(tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id,
                  perdatarif_m.perdanama_sk as perdanama_sk,
                  tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                  kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                  COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id,
                  COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                  daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                  kelompoktindakan_m.kelompoktindakan_nama as kelompoktindakan_nama,
                  daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                  kategoritindakan_m.kategoritindakan_nama as kategoritindakan_nama,
                  COALESCE(tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id,
                  daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                  NULL::integer AS tipepaket_id,
                  NULL::character varying AS tipepaket_nama,
                  tariftindakan_m.komponentarif_id as komponentarif_id,
                  komponentarif_m.komponentarif_nama as komponentarif_nama,
                  COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan,
                  COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan,
                  COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan,
                  NULL::BOOLEAN AS is_default,
                  daftartindakan_m.is_akomodasi as is_akomodasi,
                  penjamin_m.carabayar_id as carabayar_id,
                  daftartindakan_m.is_konsultasi as is_konsultasi,
                  kamarruangan_m.kamarruangan_nokamar as  kamarruangan_nokamar,
                  tariftindakan_m.kamarruangan_id as kamarruangan_id,
                  NULL::int4 as ambulan_id,
                  NULL::VARCHAR as no_polisi,
                  NULL::integer as kelompokpemeriksaanlab_id,
                  NULL::character varying AS nama_kelompok,
                  NULL::integer AS jenispemeriksaanlab_id,
                  NULL::character varying AS jenispemeriksaanlab_nama,
                  NULL::integer AS pemeriksaanlab_id,
                  NULL::character varying AS pemeriksaanlab_nama,
                  COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
          FROM tariftindakan_m
            JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
            JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
            JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
            LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                              tariftindakan_m.tariftindakan_id,
                              tariftindakan_m.kelaspelayanan_id,
                              tariftindakan_m.penjamin_id,
                              tariftindakan_m.harga_tariftindakan,
                              tariftindakan_m.persencyto_tindakan,
                              tariftindakan_m.persendiskon_tindakan,
                              tariftindakan_m.perdatarif_id,
                              penjamin_m.penjamin_nama,
                              tariftindakan_m.komponentarif_id,
                              tariftindakan_m.kamarruangan_id
                      FROM tariftindakan_m
                        JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                        JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                        LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                        JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                      WHERE tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL 
                        AND kamarruangan_m.is_deleted = FALSE 
                        AND kamarruangan_m.is_active = TRUE 
                        AND perdatarif_m.is_active = true 
                        AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                 ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                    AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                                    AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id
          WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND tariftindakan_m.tarifparent_id IS NULL
            AND kamarruangan_m.is_deleted = FALSE 
            AND kamarruangan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
          ) AS x
      WHERE x.ruangan_id = xruangan_id
        AND x.kelaspelayanan_id = xkelaspelayanan_id
      GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    
ELSEIF(xtipe = \'ambulan\')
  THEN
    RETURN QUERY 
      SELECT * FROM 
        (SELECT \'ambulan\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id,
                NULL::integer as ruangan_id,
                NULL::character varying as ruangan_nama,
                NULL::integer as instalasi_id,
                NULL::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(tariftindakan_m.kelaspelayanan_id, 3)::integer as kelaspelayanan_id,
                COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'Kelas 3\')::character varying AS kelaspelayanan_nama,
                COALESCE(tariftindakan_m.penjamin_id, 1)::integer AS penjamin_id ,
                COALESCE(penjamin_m.penjamin_nama, \'Perseorangan\')::character varying AS penjamin_nama,
                daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                null::VARCHAR as kelompoktindakan_nama,
                daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                null::VARCHAR as kategoritindakan_nama,
                ambulandetail_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                COALESCE(tariftindakan_m.komponentarif_id, 6) as komponentarif_id,
                COALESCE(komponentarif_m.komponentarif_nama, \'Total Tarif\')::character varying as komponentarif_nama,
                COALESCE(tarif_penjamin.harga_tariftindakan, COALESCE((tariftindakan_m.harga_tariftindakan), 0)) AS harga_tariftindakan,
                COALESCE(tarif_penjamin.persencyto_tindakan, COALESCE((tariftindakan_m.persencyto_tindakan), 0)) AS persencyto_tindakan,
                COALESCE(tarif_penjamin.persendiskon_tindakan, COALESCE((tariftindakan_m.persendiskon_tindakan), 0)) AS persencyto_tindakan,
                ambulandetail_m.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                tariftindakan_m.kamarruangan_id as kamarruangan_id,
                ambulan_m.ambulan_id as ambulan_id,
                ambulan_m.no_polisi as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
        FROM ambulan_m
          JOIN ambulandetail_m ON ambulan_m.ambulan_id = ambulandetail_m.ambulan_id
          JOIN daftartindakan_m ON ambulandetail_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
          LEFT JOIN tariftindakan_m ON ambulandetail_m.daftartindakan_id = tariftindakan_m.daftartindakan_id
          JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
          LEFT JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
          LEFT JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
          JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
          LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                            tariftindakan_m.tariftindakan_id,
                            tariftindakan_m.kelaspelayanan_id,
                            tariftindakan_m.penjamin_id,
                            tariftindakan_m.harga_tariftindakan,
                            tariftindakan_m.persencyto_tindakan,
                            tariftindakan_m.persendiskon_tindakan,
                            tariftindakan_m.perdatarif_id,
                            penjamin_m.penjamin_nama,
                            tariftindakan_m.komponentarif_id
                    FROM tariftindakan_m
                      JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                      JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                      JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND perdatarif_m.is_active = TRUE 
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                    ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                       AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
            WHERE ambulan_m.is_deleted = FALSE 
              AND ambulan_m.is_active = TRUE
              AND ambulandetail_m.is_deleted = FALSE 
              AND ambulandetail_m.is_active = TRUE 
              AND tariftindakan_m.is_deleted = FALSE 
              AND tariftindakan_m.is_active = TRUE 
              AND perdatarif_m.is_active = TRUE 
              AND perdatarif_m.is_deleted = FALSE 
              AND tariftindakan_m.komponentarif_id <> 6 
              AND tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
      WHERE x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;

ELSEIF(xtipe = \'penunjang\')
  THEN
    RETURN QUERY
      SELECT * FROM 
        (SELECT \'lab\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                pemeriksaanlab_m.kelompokpemeriksaanlab_id::int4,
                kelompokpemeriksaanlab_m.nama_kelompok::character varying ,
                pemeriksaanlab_m.jenispemeriksaanlab_id::int4,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama::character varying ,
                pemeriksaanlab_m.pemeriksaanlab_id::int4,
                pemeriksaanlab_m.pemeriksaanlab_nama::character varying ,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
        FROM tariftindakan_m
          JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
          JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                AND pemeriksaanlab_m.is_deleted = FALSE
          JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
          JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
          JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
          JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                            AND perdatarif_m.is_deleted = FALSE 
                            AND perdatarif_m.is_active = TRUE
          JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
          JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
          JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                  AND tindakanruangan_mp.is_deleted = FALSE
          JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
          LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                            tariftindakan_m.tariftindakan_id,
                            tariftindakan_m.kelaspelayanan_id,
                            tariftindakan_m.penjamin_id,
                            tariftindakan_m.harga_tariftindakan,
                            tariftindakan_m.persencyto_tindakan,
                            tariftindakan_m.persendiskon_tindakan,
                            tariftindakan_m.perdatarif_id,
                            penjamin_m.penjamin_nama,
                            tariftindakan_m.komponentarif_id
                    FROM tariftindakan_m
                      JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                      JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                      JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        WHERE komponentarif_m.is_deleted IS FALSE
          AND perdatarif_m.is_active = TRUE 
          AND tariftindakan_m.is_deleted = FALSE 
          AND tariftindakan_m.is_active = TRUE 
          AND tariftindakan_m.komponentarif_id <> 6
          AND tariftindakan_m.penjamin_id = xpenjamin_id
        ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
        WHERE  tariftindakan_m.is_deleted = FALSE 
          AND tariftindakan_m.is_active = TRUE 
          AND tariftindakan_m.tarifparent_id IS NULL 
          AND perdatarif_m.is_active = TRUE 
          AND tariftindakan_m.komponentarif_id <> 6
          AND tariftindakan_m.penjamin_id = vpenjamin_id
UNION ALL         
    SELECT 
    \'paket_lab\'::text AS jenis,
    COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
    paketruangan_mp.ruangan_id as ruangan_id,
    ruangan_m.ruangan_nama as ruangan_nama,
    ruangan_m.instalasi_id as instalasi_id,
    null::character varying AS instalasi_nama,
    NULL::integer AS ruanganpaket_id,
    NULL::character varying AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id AS perdatarif_id ,
    perdatarif_m.perdanama_sk as perdanama_sk,
    tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
    COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
    COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
    null::integer as kelompoktindakan_id,
    null::character varying  as kelompoktindakan_nama,
    null::integer as kategoritindakan_id,
    null::character varying  as kategoritindakan_nama,
    tariftindakan_m.daftartindakan_id  AS daftartindakan_id ,
    daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
    tariftindakan_m.tipepaket_id  AS tipepaket_id,
    tipepaket_m.tipepaket_nama AS tipepaket_nama,
    tariftindakan_m.komponentarif_id as komponentarif_id,
    komponentarif_m.komponentarif_nama as komponentarif_nama,
    COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
    COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
    COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
    paketruangan_mp.is_default AS is_default,
    null::boolean as is_akomodasi,
    penjamin_m.carabayar_id as carabayar_id,
    null::boolean as is_konsultasi,
    null::character varying  as  kamarruangan_nokamar,
    null::integer as kamarruangan_id,
    null::integer as ambulan_id,
    null::character varying  as no_polisi,
    kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id as kelompokpemeriksaanlab_id,
    kelompokpemeriksaanlab_m.nama_kelompok AS nama_kelompok,
    pemeriksaanlab_m.jenispemeriksaanlab_id AS jenispemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenispemeriksaanlab_nama,
    pemeriksaanlab_m.pemeriksaanlab_id AS pemeriksaanlab_id,
    pemeriksaanlab_m.pemeriksaanlab_nama AS pemeriksaanlab_nama,
    COALESCE(tariftindakan_m.persen_penyulit, 0::numeric) AS persen_penyulit  
FROM tariftindakan_m
    JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
    JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
  JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                       AND paketruangan_mp.is_deleted = FALSE
  JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
  JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                     AND perdatarif_m.is_deleted = FALSE 
                   AND perdatarif_m.is_active = TRUE
    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
    JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                        and pemeriksaanlab_m.is_deleted = FALSE
    LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
    LEFT JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
    LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
                                      tariftindakan_m.tariftindakan_id,
                                      tariftindakan_m.kelaspelayanan_id,
                                      tariftindakan_m.penjamin_id,
                                      tariftindakan_m.harga_tariftindakan,
                                      tariftindakan_m.persencyto_tindakan,
                                      tariftindakan_m.persendiskon_tindakan,
                                      tariftindakan_m.perdatarif_id,
                                      penjamin_m.penjamin_nama,
                                      tariftindakan_m.komponentarif_id,
                                      pemeriksaanlab_m.kelompokpemeriksaanlab_id,
                                      pemeriksaanlab_m.jenispemeriksaanlab_id,
                                      pemeriksaanlab_m.pemeriksaanlab_id
                        FROM tariftindakan_m
                          JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                             AND perdatarif_m.is_deleted = FALSE 
                               AND perdatarif_m.is_active = TRUE
                            JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                                                        AND pemeriksaanlab_m.is_deleted = FALSE
                        WHERE   tariftindakan_m.is_deleted = FALSE 
              AND tariftindakan_m.is_active = TRUE 
              AND   perdatarif_m.is_active = TRUE 
              AND tariftindakan_m.komponentarif_id <> 6
                            AND tariftindakan_m.penjamin_id = xpenjamin_id
                      ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                    AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                        AND pemeriksaanlab_m.pemeriksaanlab_id = tarif_penjamin.pemeriksaanlab_id
    WHERE tariftindakan_m.is_deleted = FALSE 
    AND tariftindakan_m.is_active = TRUE 
    AND perdatarif_m.is_active = TRUE 
    AND tariftindakan_m.komponentarif_id <> 6 
    AND tariftindakan_m.penjamin_id = vpenjamin_id 
UNION ALL
  SELECT  \'rad\'::text AS jenis,
          COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
          tindakanruangan_mp.ruangan_id as ruangan_id,
          ruangan_m.ruangan_nama as ruangan_nama,
          ruangan_m.instalasi_id as instalasi_id,
          NULL::character varying as instalasi_nama,
          NULL::integer AS ruanganpaket_id,
          NULL::character varying AS ruanganpaket_nama,
          tariftindakan_m.perdatarif_id AS perdatarif_id ,
          perdatarif_m.perdanama_sk as perdanama_sk,
          tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
          kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
          COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
          COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
          NULL::integer as kelompoktindakan_id,
          NULL::character varying as kelompoktindakan_nama,
          NULL::integer as kategoritindakan_id,
          NULL::character varying as kategoritindakan_nama,
          tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
          daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
          NULL::integer AS tipepaket_id,
          NULL::character varying AS tipepaket_nama,
          tariftindakan_m.komponentarif_id as komponentarif_id,
          komponentarif_m.komponentarif_nama as komponentarif_nama,
          COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
          COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
          COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
          tindakanruangan_mp.is_default AS is_default,
          daftartindakan_m.is_akomodasi as is_akomodasi,
          penjamin_m.carabayar_id as carabayar_id,
          daftartindakan_m.is_konsultasi as is_konsultasi,
          NULL::character varying as  kamarruangan_nokamar,
          NULL::integer as kamarruangan_id,
          NULL::integer as ambulan_id,
          NULL::character varying as no_polisi,
          pemeriksaanrad_m.kelompokpemeriksaanrad_id as kelompokpemeriksaanlab_id,
          kelompokpemeriksaanrad_m.nama_kelompok as nama_kelompok,
          pemeriksaanrad_m.jenispemeriksaanrad_id as jenispemeriksaanlab_id,
          jenispemeriksaanrad_m.jenispemeriksaanrad_nama as jenispemeriksaanlab_nama,
          pemeriksaanrad_m.pemeriksaanradiologi_id as pemeriksaanlab_id,
          pemeriksaanrad_m.pemeriksaanrad_nama  as pemeriksaanlab_nama,
          COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
  FROM tariftindakan_m
    JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
    JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                          AND pemeriksaanrad_m.is_deleted = FALSE
    JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
    JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                      AND perdatarif_m.is_deleted = FALSE 
                      AND perdatarif_m.is_active = TRUE
    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
    JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                            AND tindakanruangan_mp.is_deleted = FALSE
    JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
    LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                      tariftindakan_m.tariftindakan_id,
                      tariftindakan_m.kelaspelayanan_id,
                      tariftindakan_m.penjamin_id,
                      tariftindakan_m.harga_tariftindakan,
                      tariftindakan_m.persencyto_tindakan,
                      tariftindakan_m.persendiskon_tindakan,
                      tariftindakan_m.perdatarif_id,
                      penjamin_m.penjamin_nama,
                      tariftindakan_m.komponentarif_id
              FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
              WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
              ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                    AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
  WHERE tariftindakan_m.is_deleted = FALSE 
    AND tariftindakan_m.is_active = TRUE 
    AND perdatarif_m.is_active = TRUE 
    AND tariftindakan_m.komponentarif_id <> 6
    AND tariftindakan_m.penjamin_id = vpenjamin_id
UNION ALL
  SELECT  \'paket_rad\'::text AS jenis,
            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
            paketruangan_mp.ruangan_id as ruangan_id,
            ruangan_m.ruangan_nama as ruangan_nama,
            ruangan_m.instalasi_id as instalasi_id,
            null::VARCHAR as instalasi_nama,
            NULL::INTEGER AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            tariftindakan_m.perdatarif_id AS perdatarif_id ,
            perdatarif_m.perdanama_sk as perdanama_sk,
            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
            COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
            COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
            null::INTEGER as kelompoktindakan_id,
            null::VARCHAR as kelompoktindakan_nama,
            null::INTEGER as kategoritindakan_id,
            null::VARCHAR as kategoritindakan_nama,
            tariftindakan_m.daftartindakan_id  AS daftartindakan_id ,
            tipepaket_m.tipepaket_nama as daftartindakan_nama,
            tariftindakan_m.tipepaket_id  AS tipepaket_id,
            tipepaket_m.tipepaket_nama AS tipepaket_nama,
            tariftindakan_m.komponentarif_id as komponentarif_id,
            komponentarif_m.komponentarif_nama as komponentarif_nama,
            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
            paketruangan_mp.is_default AS is_default,
            null::BOOLEAN as is_akomodasi,
            penjamin_m.carabayar_id as carabayar_id,
            null::BOOLEAN as is_konsultasi,
            null::VARCHAR as  kamarruangan_nokamar,
            null::INTEGER as kamarruangan_id,
            null::INTEGER as ambulan_id,
            null::VARCHAR as no_polisi,
            kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id as kelompokpemeriksaanlab_id,
            kelompokpemeriksaanrad_m.nama_kelompok AS nama_kelompok,
            pemeriksaanrad_m.jenispemeriksaanrad_id AS jenispemeriksaanlab_id,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenispemeriksaanlab_nama,
            pemeriksaanrad_m.pemeriksaanradiologi_id AS pemeriksaanlab_id,
            pemeriksaanrad_m.pemeriksaanrad_nama AS pemeriksaanlab_nama,
            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit      
  FROM tariftindakan_m
        JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
        JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                                                 AND paketruangan_mp.is_deleted = FALSE
        JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                          AND perdatarif_m.is_deleted = FALSE 
                                          AND perdatarif_m.is_active = TRUE
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
        JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                                    AND pemeriksaanrad_m.is_deleted = FALSE
        LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
        LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id   
        LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
                                            tariftindakan_m.tariftindakan_id,
                                            tariftindakan_m.kelaspelayanan_id,
                                            tariftindakan_m.penjamin_id,
                                            tariftindakan_m.harga_tariftindakan,
                                            tariftindakan_m.persencyto_tindakan,
                                            tariftindakan_m.persendiskon_tindakan,
                                            tariftindakan_m.perdatarif_id,
                                            penjamin_m.penjamin_nama,
                                            tariftindakan_m.komponentarif_id,
                                            pemeriksaanrad_m.kelompokpemeriksaanrad_id,
                                            pemeriksaanrad_m.jenispemeriksaanrad_id,
                                            pemeriksaanrad_m.pemeriksaanradiologi_id
                            FROM tariftindakan_m
                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                    AND perdatarif_m.is_deleted = FALSE 
                                                                    AND perdatarif_m.is_active = TRUE
                                JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                                                            AND pemeriksaanrad_m.is_deleted = FALSE
                            WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6 
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                            ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                             AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                             AND pemeriksaanrad_m.pemeriksaanradiologi_id = tarif_penjamin.pemeriksaanradiologi_id
    WHERE tariftindakan_m.is_deleted = FALSE 
    AND tariftindakan_m.is_active = TRUE 
    AND perdatarif_m.is_active = TRUE 
    AND tariftindakan_m.komponentarif_id <> 6 
    AND tariftindakan_m.penjamin_id = vpenjamin_id                  
UNION ALL
  SELECT \'operasi\'::text AS jenis,
         COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
         tindakanruangan_mp.ruangan_id as ruangan_id,
         ruangan_m.ruangan_nama as ruangan_nama,
         ruangan_m.instalasi_id as instalasi_id,
         NULL::character varying instalasi_nama,
         NULL::integer AS ruanganpaket_id,
         NULL::character varying AS ruanganpaket_nama,
         tariftindakan_m.perdatarif_id AS perdatarif_id ,
         perdatarif_m.perdanama_sk as perdanama_sk,
         tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
         kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
         COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
         COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
         null::integer as kelompoktindakan_id,
         NULL::character varying as kelompoktindakan_nama,
         null::integer as kategoritindakan_id,
         NULL::character varying as kategoritindakan_nama,
         tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
         daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
         NULL::integer AS tipepaket_id,
         NULL::character varying AS tipepaket_nama,
         tariftindakan_m.komponentarif_id as komponentarif_id,
         komponentarif_m.komponentarif_nama as komponentarif_nama,
         COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
         COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
         COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
         tindakanruangan_mp.is_default AS is_default,
         daftartindakan_m.is_akomodasi as is_akomodasi,
         penjamin_m.carabayar_id as carabayar_id,
         daftartindakan_m.is_konsultasi as is_konsultasi,
         NULL::character varying as  kamarruangan_nokamar,
         null::integer as kamarruangan_id,
         null::integer as ambulan_id,
         NULL::character varying as no_polisi,
         operasi_m.golonganoperasi_id as kelompokpemeriksaanlab_id,
         golonganoperasi_m.golonganoperasi_nama as nama_kelompok,
         operasi_m.kegiatanoperasi_id as jenispemeriksaanlab_id,
         kegiatanoperasi_m.kegiatanoperasi_nama as jenispemeriksaanlab_nama,
         operasi_m.operasi_id as pemeriksaanlab_id,
         operasi_m.operasi_nama  as pemeriksaanlab_nama,
         COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
  FROM tariftindakan_m
    JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
    JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                   AND operasi_m.is_deleted = FALSE
    JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
    JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                      AND perdatarif_m.is_deleted = FALSE 
                      AND perdatarif_m.is_active = TRUE
    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
    JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                            AND tindakanruangan_mp.is_deleted = FALSE
    JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
    LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                      tariftindakan_m.tariftindakan_id,
                      tariftindakan_m.kelaspelayanan_id,
                      tariftindakan_m.penjamin_id,
                      tariftindakan_m.harga_tariftindakan,
                      tariftindakan_m.persencyto_tindakan,
                      tariftindakan_m.persendiskon_tindakan,
                      tariftindakan_m.perdatarif_id,
                      penjamin_m.penjamin_nama,
                      tariftindakan_m.komponentarif_id
              FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
              WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
              ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                         AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
              WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
UNION ALL
  SELECT  \'paket_operasi\'::text AS jenis,
          COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
          paketruangan_mp.ruangan_id as ruangan_id,
          ruangan_m.ruangan_nama as ruangan_nama,
          ruangan_m.instalasi_id as instalasi_id,
          null::VARCHAR as instalasi_nama,
          NULL::integer AS ruanganpaket_id,
          NULL::character varying AS ruanganpaket_nama,
          tariftindakan_m.perdatarif_id AS perdatarif_id ,
          perdatarif_m.perdanama_sk as perdanama_sk,
          tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
          kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
          COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
          COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
          null::INTEGER as kelompoktindakan_id,
          null::VARCHAR as kelompoktindakan_nama,
          null::INTEGER as kategoritindakan_id,
          null::VARCHAR as kategoritindakan_nama,
          null::INTEGER AS daftartindakan_id ,
          null::VARCHAR as daftartindakan_nama,
          tariftindakan_m.daftartindakan_id  AS tipepaket_id,
          tipepaket_m.tipepaket_nama AS tipepaket_nama,
          tariftindakan_m.komponentarif_id as komponentarif_id,
          komponentarif_m.komponentarif_nama as komponentarif_nama,
          COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
          COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
          COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
          paketruangan_mp.is_default AS is_default,
          null::BOOLEAN as is_akomodasi,
          penjamin_m.carabayar_id as carabayar_id,
          null::BOOLEAN as is_konsultasi,
          null::VARCHAR as  kamarruangan_nokamar,
          null::INTEGER as kamarruangan_id,
          null::INTEGER as ambulan_id,
          null::VARCHAR as no_polisi,
          operasi_m.golonganoperasi_id as kelompokpemeriksaanlab_id,
          golonganoperasi_m.golonganoperasi_nama AS nama_kelompok,
          operasi_m.kegiatanoperasi_id AS jenispemeriksaanlab_id,
          kegiatanoperasi_m.kegiatanoperasi_nama AS jenispemeriksaanlab_nama,
          operasi_m.operasi_id AS pemeriksaanlab_id,
          operasi_m.operasi_nama AS pemeriksaanlab_nama,
          COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
    FROM tariftindakan_m
        JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
        JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                         AND paketruangan_mp.is_deleted = FALSE
        JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                               AND perdatarif_m.is_deleted = FALSE 
                     AND perdatarif_m.is_active = TRUE
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
        JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                   AND operasi_m.is_deleted = FALSE
        LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
        LEFT JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id          
        LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        tariftindakan_m.penjamin_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.perdatarif_id,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        operasi_m.golonganoperasi_id,
                        operasi_m.kegiatanoperasi_id,
                        operasi_m.operasi_id
                FROM tariftindakan_m
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                          AND perdatarif_m.is_deleted = FALSE 
                                  AND perdatarif_m.is_active = TRUE
                    JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                                                 AND operasi_m.is_deleted = FALSE
                WHERE   tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                      ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                       AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                       AND operasi_m.operasi_id = tarif_penjamin.operasi_id
    WHERE  tariftindakan_m.is_deleted = FALSE 
    AND tariftindakan_m.is_active = TRUE 
    AND perdatarif_m.is_active = TRUE 
    AND tariftindakan_m.komponentarif_id <> 6 
    AND tariftindakan_m.penjamin_id = vpenjamin_id 
) x
WHERE x.ruangan_id = xruangan_id
  AND x.kelaspelayanan_id = xkelaspelayanan_id
GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    ELSEIF(xtipe = \'makanan\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT
                \'makanan\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                makanandiet_m.jenisdiet_id::int4 AS kelompokpemeriksaanlab_id,
                jenisdiet_m.jenisdiet_nama::character varying nama_kelompok ,
                makanandiet_m.jenisdiet_id::int4 AS jenispemeriksaanlab_id,
                jenisdiet_m.jenisdiet_nama::character varying AS jenispemeriksaanlab_nama ,
                makanandiet_m.makanandiet_id::int4 AS pemeriksaanlab_id,
                makanandiet_m.makanandiet_nama::character varying AS pemeriksaanlab_nama ,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN makanandiet_m ON tariftindakan_m.daftartindakan_id = makanandiet_m.daftartindakan_id AND makanandiet_m.is_deleted = FALSE
            JOIN jenisdiet_m ON makanandiet_m.jenisdiet_id = jenisdiet_m.jenisdiet_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                            AND perdatarif_m.is_deleted = FALSE 
                                                            AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                        AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                                            tariftindakan_m.tariftindakan_id,
                                                            tariftindakan_m.kelaspelayanan_id,
                                                            tariftindakan_m.penjamin_id,
                                                            tariftindakan_m.harga_tariftindakan,
                                                            tariftindakan_m.persencyto_tindakan,
                                                            tariftindakan_m.persendiskon_tindakan,
                                                            tariftindakan_m.perdatarif_id,
                                                            penjamin_m.penjamin_nama,
                                                            tariftindakan_m.komponentarif_id
                        FROM tariftindakan_m
                                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                        AND perdatarif_m.is_active = TRUE 
                                                                                        AND perdatarif_m.is_deleted = FALSE
                                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                                WHERE tariftindakan_m.is_deleted = FALSE 
                          AND tariftindakan_m.is_active = TRUE 
                          AND tariftindakan_m.tarifparent_id IS NULL
                          AND tariftindakan_m.komponentarif_id <> 6
                          AND tariftindakan_m.penjamin_id = xpenjamin_id
                       ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
         WHERE tariftindakan_m.is_deleted = FALSE 
        AND tariftindakan_m.is_active = TRUE 
        AND tariftindakan_m.tarifparent_id IS NULL 
        AND tariftindakan_m.komponentarif_id <> 6
        AND tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
        WHERE x.kelaspelayanan_id = xkelaspelayanan_id
        AND x.ruangan_id = xruangan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    
END IF;
END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;');

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tariftotalrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0, "xtipe" varchar=\'pelayanan\'::character varying)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool, "kamarruangan_nokamar" varchar, "kamarruangan_id" int4, "ambulan_id" int4, "no_polisi" varchar, "kelompokpemeriksaanlab_id" int4, "nama_kelompok" varchar, "jenispemeriksaanlab_id" int4, "jenispemeriksaanlab_nama" varchar, "pemeriksaanlab_id" int4, "pemeriksaanlab_nama" varchar, "persen_penyulit" numeric) AS $BODY$ 
DECLARE vpenjamin_id int4;
BEGIN
    IF(xruangan_id = 0)
    THEN
        SELECT default_ruangan INTO xruangan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    IF(xkelaspelayanan_id = 0)
    THEN
        SELECT default_kelas INTO xkelaspelayanan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    SELECT default_penjamin INTO vpenjamin_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xpenjamin_id = vpenjamin_id)
    THEN
        xpenjamin_id := 0;
    END IF;
    
    IF(xtipe = \'pelayanan\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT \'tindakan\'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id,
                r_tindakan.ruangan_nama,
                r_tindakan.instalasi_id,
                ins_tindakan.instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                daftartindakan_m.kelompoktindakan_id,
                kelompoktindakan_m.kelompoktindakan_nama,
                daftartindakan_m.kategoritindakan_id,
                kategoritindakan_m.kategoritindakan_nama,
                COALESCE( tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default,
                daftartindakan_m.is_akomodasi,
                penjamin_m.carabayar_id,
                daftartindakan_m.is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
             FROM tariftindakan_m
                 JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                 JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
                 JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
                 LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id           
                 LEFT JOIN (
                        SELECT tariftindakan_m.daftartindakan_id,
                            tariftindakan_m.tariftindakan_id,
                            tariftindakan_m.kelaspelayanan_id,
                            tariftindakan_m.penjamin_id,
                            tariftindakan_m.harga_tariftindakan,
                            tariftindakan_m.persencyto_tindakan,
                            tariftindakan_m.persendiskon_tindakan,
                            tariftindakan_m.perdatarif_id,
                            penjamin_m.penjamin_nama,
                            tariftindakan_m.komponentarif_id
                        FROM tariftindakan_m
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        WHERE komponentarif_m.is_deleted IS FALSE
                        AND perdatarif_m.is_active = true 
                        AND tariftindakan_m.is_deleted = false 
                        AND tariftindakan_m.is_active = true 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                 ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                WHERE tindakanruangan_mp.is_deleted = false 
                AND komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
        UNION ALL
         SELECT \'paket\'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id,
                r_paket.ruangan_nama,
                r_paket.instalasi_id,
                ins_paket.instalasi_nama,
                paketruangan_mp.ruangan_id AS ruanganpaket_id,
                r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                NULL::integer AS kelompoktindakan_id,
                NULL::character varying AS kelompoktindakan_nama,
                NULL::integer AS kategoritindakan_id,
                NULL::character varying AS kategoritindakan_nama,
                NULL::integer AS daftartindakan_id,
                NULL::character varying AS daftartindakan_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                paketruangan_mp.is_default,
                NULL::boolean AS is_akomodasi,
                penjamin_m.carabayar_id,
                false AS is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
             FROM tariftindakan_m
                 JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                    AND tipepaket_m.is_deleted= FALSE
                                                                    AND tipepaket_m.is_active = TRUE
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                        AND perdatarif_m.is_active = TRUE 
                 JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                                                                            AND paketruangan_mp.is_deleted = FALSE
                 JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
                 JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
                 LEFT JOIN (
                        SELECT tariftindakan_m.daftartindakan_id,
                            tariftindakan_m.tariftindakan_id,
                            tariftindakan_m.kelaspelayanan_id,
                            tariftindakan_m.penjamin_id,
                            tariftindakan_m.harga_tariftindakan,
                            tariftindakan_m.persencyto_tindakan,
                            tariftindakan_m.persendiskon_tindakan,
                            tariftindakan_m.perdatarif_id,
                            penjamin_m.penjamin_nama,
                            tariftindakan_m.komponentarif_id,
                            tariftindakan_m.tipepaket_id
                        FROM tariftindakan_m
                                                JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                                AND tipepaket_m.is_deleted= FALSE
                                                                                AND tipepaket_m.is_active = TRUE
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                    AND perdatarif_m.is_active = TRUE 
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        WHERE tariftindakan_m.is_deleted = FALSE
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                 ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                    AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
            WHERE tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NULL
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
        UNION ALL
         SELECT \'paket\'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id,
                r_paket.ruangan_nama,
                r_paket.instalasi_id,
                ins_paket.instalasi_nama,
                paketruangan_mp.ruangan_id AS ruanganpaket_id,
                r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                perdatarif_m.perdanama_sk,
                tariftindakan_m.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                NULL::integer AS kelompoktindakan_id,
                NULL::character varying AS kelompoktindakan_nama,
                NULL::integer AS kategoritindakan_id,
                NULL::character varying AS kategoritindakan_nama,
                NULL::integer AS daftartindakan_id,
                NULL::character varying AS daftartindakan_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                paketruangan_mp.is_default,
                NULL::boolean AS is_akomodasi,
                penjamin_m.carabayar_id,
                false AS is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
             FROM tariftindakan_m
                 JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                    AND tipepaket_m.is_deleted= FALSE
                                                                    AND tipepaket_m.is_active = TRUE
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                 JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
                                                                                AND paketruangan_mp.is_deleted = FALSE 
                 JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
                 LEFT JOIN (
                        SELECT tariftindakan_m.daftartindakan_id,
                            tariftindakan_m.tipepaket_id,
                            tariftindakan_m.tariftindakan_id,
                            tariftindakan_m.kelaspelayanan_id,
                            tariftindakan_m.penjamin_id,
                            tariftindakan_m.harga_tariftindakan,
                            tariftindakan_m.persencyto_tindakan,
                            tariftindakan_m.persendiskon_tindakan,
                            tariftindakan_m.perdatarif_id,
                            penjamin_m.penjamin_nama,
                            tariftindakan_m.komponentarif_id
                        FROM tariftindakan_m
                                                JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                                AND tipepaket_m.is_deleted= FALSE
                                                                                AND tipepaket_m.is_active = TRUE
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                    AND perdatarif_m.is_deleted= FALSE
                                                                                    AND perdatarif_m.is_active = TRUE
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        WHERE tariftindakan_m.is_deleted = false 
                        AND tariftindakan_m.is_active = true 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                 ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            WHERE tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NOT NULL
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
        WHERE x.ruangan_id = xruangan_id
        AND x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    ELSEIF(xtipe = \'kamar\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
                SELECT      
                    \'kamar\'::text AS jenis,
                    COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                    kamarruangan_m.ruangan_id as ruangan_id,
                    ruangan_m.ruangan_nama as ruangan_nama,
                    ruangan_m.instalasi_id as instalasi_id,
                    instalasi_m.instalasi_nama as instalasi_nama,
                    NULL::integer AS ruanganpaket_id,
                    NULL::character varying AS ruanganpaket_nama,
                    COALESCE(tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id,
                     perdatarif_m.perdanama_sk as perdanama_sk,
                    tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                    COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id,
                    COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                    daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                    kelompoktindakan_m.kelompoktindakan_nama as kelompoktindakan_nama,
                    daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                    kategoritindakan_m.kategoritindakan_nama as kategoritindakan_nama,
                    COALESCE(tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id,
                    daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                    NULL::integer AS tipepaket_id,
                    NULL::character varying AS tipepaket_nama,
                    tariftindakan_m.komponentarif_id as komponentarif_id,
                    komponentarif_m.komponentarif_nama as komponentarif_nama,
                    COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan,
                    COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan,
                    COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan,
                    NULL::BOOLEAN AS is_default,
                    daftartindakan_m.is_akomodasi as is_akomodasi,
                    penjamin_m.carabayar_id as carabayar_id,
                    daftartindakan_m.is_konsultasi as is_konsultasi,
                    kamarruangan_m.kamarruangan_nokamar as  kamarruangan_nokamar,
                    tariftindakan_m.kamarruangan_id as kamarruangan_id,
                    NULL::int4 as ambulan_id,
                    NULL::VARCHAR as no_polisi,
                    NULL::integer as kelompokpemeriksaanlab_id,
                    NULL::character varying AS nama_kelompok,
                    NULL::integer AS jenispemeriksaanlab_id,
                    NULL::character varying AS jenispemeriksaanlab_nama,
                    NULL::integer AS pemeriksaanlab_id,
                    NULL::character varying AS pemeriksaanlab_nama,
                    COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
             FROM tariftindakan_m
                 JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                 JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                 LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                 JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                 JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN (
                        SELECT tariftindakan_m.daftartindakan_id,
                            tariftindakan_m.tariftindakan_id,
                            tariftindakan_m.kelaspelayanan_id,
                            tariftindakan_m.penjamin_id,
                            tariftindakan_m.harga_tariftindakan,
                            tariftindakan_m.persencyto_tindakan,
                            tariftindakan_m.persendiskon_tindakan,
                            tariftindakan_m.perdatarif_id,
                            penjamin_m.penjamin_nama,
                            tariftindakan_m.komponentarif_id,
                            tariftindakan_m.kamarruangan_id
                        FROM tariftindakan_m
                     JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        WHERE 
                        tariftindakan_m.is_deleted = false AND
                        tariftindakan_m.is_active = true AND
                        tariftindakan_m.tarifparent_id IS NULL AND
                        kamarruangan_m.is_deleted = FALSE AND
                        kamarruangan_m.is_active = TRUE AND
                        perdatarif_m.is_active = true AND
                        tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                 ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id
            WHERE 
                    tariftindakan_m.is_deleted = false AND
                    tariftindakan_m.is_active = true AND
                    tariftindakan_m.tarifparent_id IS NULL AND
                    kamarruangan_m.is_deleted = FALSE AND
                    kamarruangan_m.is_active = TRUE AND
                    perdatarif_m.is_active = true AND
                    tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
        WHERE x.ruangan_id = xruangan_id
        AND x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    ELSEIF(xtipe = \'ambulan\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT 
                \'ambulan\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id,
                NULL::integer as ruangan_id,
                NULL::character varying as ruangan_nama,
                NULL::integer as instalasi_id,
                NULL::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(tariftindakan_m.kelaspelayanan_id, 3)::integer as kelaspelayanan_id,
                 COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'Kelas 3\')::character varying AS kelaspelayanan_nama,
                 COALESCE(tariftindakan_m.penjamin_id, 1)::integer AS penjamin_id ,
                COALESCE(penjamin_m.penjamin_nama, \'Perseorangan\')::character varying AS penjamin_nama,
                daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                null::VARCHAR as kelompoktindakan_nama,
                daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                null::VARCHAR as kategoritindakan_nama,
                ambulandetail_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                 COALESCE(tariftindakan_m.komponentarif_id, 6) as komponentarif_id,
                COALESCE(komponentarif_m.komponentarif_nama, \'Total Tarif\')::character varying as komponentarif_nama,
                COALESCE(tarif_penjamin.harga_tariftindakan, COALESCE((tariftindakan_m.harga_tariftindakan), 0)) AS harga_tariftindakan,
                COALESCE(tarif_penjamin.persencyto_tindakan, COALESCE((tariftindakan_m.persencyto_tindakan), 0)) AS persencyto_tindakan,
                COALESCE(tarif_penjamin.persendiskon_tindakan, COALESCE((tariftindakan_m.persendiskon_tindakan), 0)) AS persencyto_tindakan,
                ambulandetail_m.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                tariftindakan_m.kamarruangan_id as kamarruangan_id,
                ambulan_m.ambulan_id as ambulan_id,
                ambulan_m.no_polisi as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
             FROM ambulan_m
                 JOIN ambulandetail_m ON ambulan_m.ambulan_id = ambulandetail_m.ambulan_id
                 JOIN daftartindakan_m ON ambulandetail_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 LEFT JOIN tariftindakan_m ON ambulandetail_m.daftartindakan_id = tariftindakan_m.daftartindakan_id
                 JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                 LEFT JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 LEFT JOIN (
                        SELECT tariftindakan_m.daftartindakan_id,
                            tariftindakan_m.tariftindakan_id,
                            tariftindakan_m.kelaspelayanan_id,
                            tariftindakan_m.penjamin_id,
                            tariftindakan_m.harga_tariftindakan,
                            tariftindakan_m.persencyto_tindakan,
                            tariftindakan_m.persendiskon_tindakan,
                            tariftindakan_m.perdatarif_id,
                            penjamin_m.penjamin_nama,
                            tariftindakan_m.komponentarif_id
                        FROM tariftindakan_m
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        WHERE komponentarif_m.is_deleted IS FALSE
                        AND perdatarif_m.is_active = true 
                        AND tariftindakan_m.is_deleted = false 
                        AND tariftindakan_m.is_active = true 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                 ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            WHERE 
                 ambulan_m.is_deleted = false AND
                 ambulan_m.is_active = true AND
                 ambulandetail_m.is_deleted = false AND
                 ambulandetail_m.is_active = true AND
                 tariftindakan_m.is_deleted=false AND
                 tariftindakan_m.is_active=true AND
                 perdatarif_m.is_active = true AND 
                 perdatarif_m.is_deleted = false AND
                 tariftindakan_m.komponentarif_id = 6 AND
                 tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
        WHERE x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    ELSEIF(xtipe = \'penunjang\')
    THEN
        RETURN QUERY
        SELECT *FROM (
             SELECT
                \'lab\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                pemeriksaanlab_m.kelompokpemeriksaanlab_id::int4,
                kelompokpemeriksaanlab_m.nama_kelompok::character varying ,
                pemeriksaanlab_m.jenispemeriksaanlab_id::int4,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama::character varying ,
                pemeriksaanlab_m.pemeriksaanlab_id::int4,
                pemeriksaanlab_m.pemeriksaanlab_nama::character varying ,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
          FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                                                    AND pemeriksaanlab_m.is_deleted = FALSE
            JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
            JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                            AND perdatarif_m.is_deleted = FALSE 
                                                            AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                        AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                                            tariftindakan_m.tariftindakan_id,
                                                            tariftindakan_m.kelaspelayanan_id,
                                                            tariftindakan_m.penjamin_id,
                                                            tariftindakan_m.harga_tariftindakan,
                                                            tariftindakan_m.persencyto_tindakan,
                                                            tariftindakan_m.persendiskon_tindakan,
                                                            tariftindakan_m.perdatarif_id,
                                                            penjamin_m.penjamin_nama,
                                                            tariftindakan_m.komponentarif_id
                        FROM tariftindakan_m
                                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                        AND perdatarif_m.is_active = TRUE 
                                                                                        AND perdatarif_m.is_deleted = FALSE
                                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                                WHERE tariftindakan_m.is_deleted = FALSE 
                          AND tariftindakan_m.is_active = TRUE 
                          AND tariftindakan_m.tarifparent_id IS NULL
                          AND tariftindakan_m.komponentarif_id = 6
                          AND tariftindakan_m.penjamin_id = xpenjamin_id
                       ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
         WHERE tariftindakan_m.is_deleted = FALSE 
                     AND tariftindakan_m.is_active = TRUE 
                     AND tariftindakan_m.tarifparent_id IS NULL 
                     AND tariftindakan_m.komponentarif_id = 6
           AND tariftindakan_m.penjamin_id = vpenjamin_id
UNION ALL
    SELECT  \'rad\'::text AS jenis,
                    COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                    tindakanruangan_mp.ruangan_id as ruangan_id,
                    ruangan_m.ruangan_nama as ruangan_nama,
                    ruangan_m.instalasi_id as instalasi_id,
                    NULL::character varying as instalasi_nama,
                    NULL::integer AS ruanganpaket_id,
                    NULL::character varying AS ruanganpaket_nama,
                    tariftindakan_m.perdatarif_id AS perdatarif_id ,
                    perdatarif_m.perdanama_sk as perdanama_sk,
                    tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                    COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                    COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                    NULL::integer as kelompoktindakan_id,
                    NULL::character varying as kelompoktindakan_nama,
                    NULL::integer as kategoritindakan_id,
                    NULL::character varying as kategoritindakan_nama,
                    tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                    daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                    NULL::integer AS tipepaket_id,
                    NULL::character varying AS tipepaket_nama,
                    tariftindakan_m.komponentarif_id as komponentarif_id,
                    komponentarif_m.komponentarif_nama as komponentarif_nama,
                    COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                    COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                    COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                    tindakanruangan_mp.is_default AS is_default,
                    daftartindakan_m.is_akomodasi as is_akomodasi,
                    penjamin_m.carabayar_id as carabayar_id,
                    daftartindakan_m.is_konsultasi as is_konsultasi,
                    NULL::character varying as  kamarruangan_nokamar,
                    NULL::integer as kamarruangan_id,
                    NULL::integer as ambulan_id,
                    NULL::character varying as no_polisi,
                    pemeriksaanrad_m.kelompokpemeriksaanrad_id as kelompokpemeriksaanlab_id,
                    kelompokpemeriksaanrad_m.nama_kelompok as nama_kelompok,
                    pemeriksaanrad_m.jenispemeriksaanrad_id as jenispemeriksaanlab_id,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama as jenispemeriksaanlab_nama,
                    pemeriksaanrad_m.pemeriksaanradiologi_id as pemeriksaanlab_id,
                    pemeriksaanrad_m.pemeriksaanrad_nama  as pemeriksaanlab_nama,
                    COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
                FROM tariftindakan_m
                    JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                    JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                                                AND pemeriksaanrad_m.is_deleted = FALSE
                    JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                    JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                        AND perdatarif_m.is_deleted = FALSE 
                                                        AND perdatarif_m.is_active = TRUE
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                    JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                    AND tindakanruangan_mp.is_deleted = FALSE
                    JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                    LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                                        tariftindakan_m.tariftindakan_id,
                                                        tariftindakan_m.kelaspelayanan_id,
                                                        tariftindakan_m.penjamin_id,
                                                        tariftindakan_m.harga_tariftindakan,
                                                        tariftindakan_m.persencyto_tindakan,
                                                        tariftindakan_m.persendiskon_tindakan,
                                                        tariftindakan_m.perdatarif_id,
                                                        penjamin_m.penjamin_nama,
                                                        tariftindakan_m.komponentarif_id
                                                FROM tariftindakan_m
                                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                        AND perdatarif_m.is_active = TRUE
                                                                                        AND perdatarif_m.is_deleted = FALSE
                                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                                WHERE tariftindakan_m.is_deleted = FALSE 
                                                    AND tariftindakan_m.is_active = TRUE 
                                                    AND tariftindakan_m.tarifparent_id IS NULL
                                                    AND tariftindakan_m.komponentarif_id = 6
                                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                                            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
            WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL 
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
UNION ALL
   SELECT \'operasi\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                NULL::character varying instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                null::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                null::integer as kategoritindakan_id,
                NULL::character varying as kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                null::integer as kamarruangan_id,
                null::integer as ambulan_id,
                NULL::character varying as no_polisi,
                operasi_m.golonganoperasi_id as kelompokpemeriksaanlab_id,
                golonganoperasi_m.golonganoperasi_nama as nama_kelompok,
                operasi_m.kegiatanoperasi_id as jenispemeriksaanlab_id,
                kegiatanoperasi_m.kegiatanoperasi_nama as jenispemeriksaanlab_nama,
                operasi_m.operasi_id as pemeriksaanlab_id,
                operasi_m.operasi_nama  as pemeriksaanlab_nama,
                 COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
                 FROM ((((((((((tariftindakan_m
                     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                     JOIN operasi_m ON (((tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id) AND (operasi_m.is_deleted = false))))
                     JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
                     JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
                     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                     JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_deleted = false) AND (perdatarif_m.is_active = true))))
                     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
                     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
                     JOIN tindakanruangan_mp ON (((tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id) AND (tindakanruangan_mp.is_deleted = false))))
                     JOIN ruangan_m ON ((tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
                     LEFT JOIN (
                                    SELECT tariftindakan_m.daftartindakan_id,
                                        tariftindakan_m.tariftindakan_id,
                                        tariftindakan_m.kelaspelayanan_id,
                                        tariftindakan_m.penjamin_id,
                                        tariftindakan_m.harga_tariftindakan,
                                        tariftindakan_m.persencyto_tindakan,
                                        tariftindakan_m.persendiskon_tindakan,
                                        tariftindakan_m.perdatarif_id,
                                        penjamin_m.penjamin_nama,
                                        tariftindakan_m.komponentarif_id
                                    FROM tariftindakan_m
                                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    WHERE komponentarif_m.is_deleted IS FALSE
                                    AND perdatarif_m.is_active = true 
                                    AND tariftindakan_m.is_deleted = false 
                                    AND tariftindakan_m.is_active = true 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                WHERE  tariftindakan_m.is_deleted = false AND
                         tariftindakan_m.is_active = true AND
                         tariftindakan_m.tarifparent_id IS NULL AND
                         perdatarif_m.is_active = true AND
                         tariftindakan_m.komponentarif_id = 6
                         AND tariftindakan_m.penjamin_id = vpenjamin_id
UNION ALL
SELECT 
        CASE
                WHEN (ruangan_m.instalasi_id = 4) THEN \'paket_lab\'
                WHEN (ruangan_m.instalasi_id = 5) THEN \'paket_rad\'
                WHEN (ruangan_m.instalasi_id = 21) THEN \'paket_mcu\'
                ELSE \'paket_operasi\'
        END AS jenis,
        COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
        paketruangan_mp.ruangan_id as ruangan_id,
        ruangan_m.ruangan_nama as ruangan_nama,
        ruangan_m.instalasi_id as instalasi_id,
        null::character varying AS instalasi_nama,
        NULL::integer AS ruanganpaket_id,
        NULL::character varying AS ruanganpaket_nama,
        tariftindakan_m.perdatarif_id AS perdatarif_id ,
        perdatarif_m.perdanama_sk as perdanama_sk,
        tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
        kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
        COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
        COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
        null::integer as kelompoktindakan_id,
        null::character varying  as kelompoktindakan_nama,
        null::integer as kategoritindakan_id,
        null::character varying  as kategoritindakan_nama,
        tariftindakan_m.tipepaket_id  AS daftartindakan_id ,
        tipepaket_m.tipepaket_nama as daftartindakan_nama,
        tariftindakan_m.tipepaket_id  AS tipepaket_id,
        tipepaket_m.tipepaket_nama AS tipepaket_nama,
        tariftindakan_m.komponentarif_id as komponentarif_id,
        komponentarif_m.komponentarif_nama as komponentarif_nama,
        COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
        COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
        COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
        paketruangan_mp.is_default AS is_default,
        null::boolean as is_akomodasi,
        penjamin_m.carabayar_id as carabayar_id,
        null::boolean as is_konsultasi,
        null::character varying  as  kamarruangan_nokamar,
        null::integer as kamarruangan_id,
        null::integer as ambulan_id,
        null::character varying  as no_polisi,
        null as kelompokpemeriksaanlab_id,
        null AS nama_kelompok,
        null AS jenispemeriksaanlab_id,
        null AS jenispemeriksaanlab_nama,
        null AS pemeriksaanlab_id,
        null AS pemeriksaanlab_nama,
        COALESCE(tariftindakan_m.persen_penyulit, 0::numeric) AS persen_penyulit  
FROM tariftindakan_m
    JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id                 
    JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                                                 AND paketruangan_mp.is_deleted = FALSE
    JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                            AND perdatarif_m.is_deleted = FALSE 
                                            AND perdatarif_m.is_active = TRUE
    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
    LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
                                            tariftindakan_m.tariftindakan_id,
                                            tariftindakan_m.kelaspelayanan_id,
                                            tariftindakan_m.penjamin_id,
                                            tariftindakan_m.harga_tariftindakan,
                                            tariftindakan_m.persencyto_tindakan,
                                            tariftindakan_m.persendiskon_tindakan,
                                            tariftindakan_m.perdatarif_id,
                                            tariftindakan_m.komponentarif_id,
                                            penjamin_m.penjamin_nama
                            FROM tariftindakan_m
                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                    AND perdatarif_m.is_deleted = FALSE 
                                                                    AND perdatarif_m.is_active = TRUE
              WHERE tariftindakan_m.is_deleted = FALSE 
                                    AND tariftindakan_m.is_active = TRUE 
                                    AND tariftindakan_m.komponentarif_id = 6
                  AND tariftindakan_m.penjamin_id = xpenjamin_id
                            ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                             AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
        WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id          
) x
  WHERE x.ruangan_id = xruangan_id
    AND x.kelaspelayanan_id = xkelaspelayanan_id
  GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    
    ELSEIF(xtipe = \'makanan\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT
                \'makanan\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                makanandiet_m.jenisdiet_id::int4 AS kelompokpemeriksaanlab_id,
                jenisdiet_m.jenisdiet_nama::character varying nama_kelompok ,
                makanandiet_m.jenisdiet_id::int4 AS jenispemeriksaanlab_id,
                jenisdiet_m.jenisdiet_nama::character varying AS jenispemeriksaanlab_nama ,
                makanandiet_m.makanandiet_id::int4 AS pemeriksaanlab_id,
                makanandiet_m.makanandiet_nama::character varying AS pemeriksaanlab_nama ,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN makanandiet_m ON tariftindakan_m.daftartindakan_id = makanandiet_m.daftartindakan_id AND makanandiet_m.is_deleted = FALSE
            JOIN jenisdiet_m ON makanandiet_m.jenisdiet_id = jenisdiet_m.jenisdiet_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                            AND perdatarif_m.is_deleted = FALSE 
                                                            AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                        AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
                                                            tariftindakan_m.tariftindakan_id,
                                                            tariftindakan_m.kelaspelayanan_id,
                                                            tariftindakan_m.penjamin_id,
                                                            tariftindakan_m.harga_tariftindakan,
                                                            tariftindakan_m.persencyto_tindakan,
                                                            tariftindakan_m.persendiskon_tindakan,
                                                            tariftindakan_m.perdatarif_id,
                                                            penjamin_m.penjamin_nama,
                                                            tariftindakan_m.komponentarif_id
                        FROM tariftindakan_m
                                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                        AND perdatarif_m.is_active = TRUE 
                                                                                        AND perdatarif_m.is_deleted = FALSE
                                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                                WHERE tariftindakan_m.is_deleted = FALSE 
                          AND tariftindakan_m.is_active = TRUE 
                          AND tariftindakan_m.tarifparent_id IS NULL
                          AND tariftindakan_m.komponentarif_id = 6
                          AND tariftindakan_m.penjamin_id = xpenjamin_id
                       ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
         WHERE tariftindakan_m.is_deleted = FALSE 
        AND tariftindakan_m.is_active = TRUE 
        AND tariftindakan_m.tarifparent_id IS NULL 
        AND tariftindakan_m.komponentarif_id = 6
        AND tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
        WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                AND x.ruangan_id = xruangan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    END IF;
END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"tindakanpelayanan_penunjang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$-- author yaya

DECLARE
    vTindakanPelayananId INT;
    paramJson VARCHAR;
    vKompenen VARCHAR;
    vketerangan VARCHAR;
    
BEGIN

  paramJson := NEW.additional_data;  
    vTindakanPelayananId := paramJson::json->>'permintaankepenunjang_id';
  vKompenen := paramJson::json->>'list_komponen';
    
    -- Ini untuk Kondisi dimana mengupdate permintaaan kepenunjang
    IF(vTindakanPelayananId > 0) THEN
        UPDATE permintaankepenunjang_t set tindakanpelayanan_id = NEW.tindakanpelayanan_id where permintaankepenunjang_id = vTindakanPelayananId;
    NEW.additional_data = NULL;
    END IF;
    
    -- Ini untuk insert ke tindakankomponen_t
  IF (json_array_length(vKompenen::json) > 0)  THEN
            INSERT INTO tindakankomponen_t (
                            komponentarif_id,
                            tindakanpelayanan_id,
                            tarif_kompsatuan,
                            tarif_tindakankomp,
                            tarifcyto_tindakankomp,
                            tarifpenyulit_komponen,
                            subsidiasuransikomp,
                            subsidipemerintahkomp,
                            iurbiayakomp,
                            created_by
         ) SELECT 
                            komponentarif_id,
                            NEW.tindakanpelayanan_id as tindakanpelayanan_id, 
                            tarif_kompsatuan,
                            tarif_tindakankomp,
                            tarifcyto_tindakankomp,
                            tarifpenyulit_komponen,
                            subsidiasuransikomp,
                            subsidipemerintahkomp,
                            iurbiayakomp,
              NEW.created_by as created_by
            FROM json_populate_recordset(null::tindakankomponen_t,vKompenen::json);
    END IF;
        
        IF(NEW.daftartindakan_id = 99990)
        THEN 
            vketerangan := 'PEMBULATAN';
        ELSEIF(NEW.daftartindakan_id = 99991)
        THEN
            vketerangan := 'DISKON';
        ELSE
            vketerangan := 'ACCRUAL';
        END IF;
        
        -- INSERT table history tindakanpelayanan_r ACCRUAL
        INSERT INTO tindakanpelayanan_r (       
            tindakanpelayanan_id ,
            shift_id ,
            kelaspelayanan_id ,
            kelastanggungan_id ,
            pasien_id ,
            rencanaoperasi_id ,
            instalasi_id ,
            daftartindakan_id ,
            alatmedis_id ,
            tipepaket_id ,
            tindakansudahbayar_id ,
            carabayar_id ,
            pendaftaran_id ,
            hasilpemeriksaanrad_id ,
            jeniskasuspenyakit_id ,
            hasilpemeriksaanrm_id ,
            ruangan_id ,
            konsulpoli_id ,
            pasienmasukpenunjang_id ,
            hasilpemeriksaanlabdetail_id ,
            penjamin_id ,
            pasienadmisi_id ,
            verifikasitagihan_id ,
            jurnalrekening_id ,
            instruksitindakan_id ,
            tgl_tindakan ,
            tarif_rsakomodasi ,
            tarif_medis ,
            tarif_paramedis ,
            tarif_bhp ,
            tarif_satuan ,
            tarif_tindakan ,
            tarifcyto_tindakan ,
            satuan_tindakan ,
            qty_tindakan ,
            cyto_tindakan ,
            dokterpenanggungjawab_id ,
            dokterpelaksana_id ,
            dokteranastesi_id ,
            dokterdelegasi_id ,
            bidan1_id ,
            bidan2_id ,
            perawat1_id ,
            perawat2_id ,
            discount_tindakan ,
            pembebasan_tindakan ,
            subsidiasuransi_tindakan ,
            subsidipemerintah_tindakan ,
            subsisidirumahsakit_tindakan ,
            uangditerima_tindakan ,
            keterangantindakan ,
            pembulatan ,
            implementasi_id ,
            is_dilakukan ,
            pemakaianambulan_id ,
            is_penatajasa ,
            additional_riwayat ,
            is_valid ,
            kamarruangan_id ,
            kamartempattidur_id ,
            penyulit_tindakan ,
            tarifpenyulit_tindakan ,
            additional_data ,
            created_date ,
            created_by ,
            keterangan,
            no_tindakanpelayanan
        )VALUES(
            NEW.tindakanpelayanan_id ,
            NEW.shift_id ,
            NEW.kelaspelayanan_id ,
            NEW.kelastanggungan_id ,
            NEW.pasien_id ,
            NEW.rencanaoperasi_id ,
            NEW.instalasi_id ,
            NEW.daftartindakan_id ,
            NEW.alatmedis_id ,
            NEW.tipepaket_id ,
            NEW.tindakansudahbayar_id ,
            NEW.carabayar_id ,
            NEW.pendaftaran_id ,
            NEW.hasilpemeriksaanrad_id ,
            NEW.jeniskasuspenyakit_id ,
            NEW.hasilpemeriksaanrm_id ,
            NEW.ruangan_id ,
            NEW.konsulpoli_id ,
            NEW.pasienmasukpenunjang_id ,
            NEW.hasilpemeriksaanlabdetail_id ,
            NEW.penjamin_id ,
            NEW.pasienadmisi_id ,
            NEW.verifikasitagihan_id ,
            NEW.jurnalrekening_id ,
            NEW.instruksitindakan_id ,
            NEW.tgl_tindakan ,
            NEW.tarif_rsakomodasi ,
            NEW.tarif_medis ,
            NEW.tarif_paramedis ,
            NEW.tarif_bhp ,
            NEW.tarif_satuan ,
            NEW.tarif_tindakan ,
            NEW.tarifcyto_tindakan ,
            NEW.satuan_tindakan ,
            NEW.qty_tindakan ,
            NEW.cyto_tindakan ,
            NEW.dokterpenanggungjawab_id ,
            NEW.dokterpelaksana_id ,
            NEW.dokteranastesi_id ,
            NEW.dokterdelegasi_id ,
            NEW.bidan1_id ,
            NEW.bidan2_id ,
            NEW.perawat1_id ,
            NEW.perawat2_id ,
            NEW.discount_tindakan ,
            NEW.pembebasan_tindakan ,
            NEW.subsidiasuransi_tindakan ,
            NEW.subsidipemerintah_tindakan ,
            NEW.subsisidirumahsakit_tindakan ,
            NEW.uangditerima_tindakan ,
            NEW.keterangantindakan ,
            NEW.pembulatan ,
            NEW.implementasi_id ,
            NEW.is_dilakukan ,
            NEW.pemakaianambulan_id ,
            NEW.is_penatajasa ,
            NEW.additional_riwayat ,
            NEW.is_valid ,
            NEW.kamarruangan_id ,
            NEW.kamartempattidur_id ,
            NEW.penyulit_tindakan ,
            NEW.tarifpenyulit_tindakan ,
            NEW.additional_data ,
            NEW.created_date ,
            NEW.created_by ,
            vketerangan,
            NEW.no_tindakanpelayanan
        );
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
        echo "m201020_061825_oddo_penyesuaianfunction_20201020 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201020_061825_oddo_penyesuaianfunction_20201020 cannot be reverted.\n";

        return false;
    }
    */
}
