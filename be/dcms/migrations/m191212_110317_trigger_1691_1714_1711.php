<?php

use yii\db\Migration;

/**
 * Class m191212_110317_trigger_1691_1714_1711
 */
class m191212_110317_trigger_1691_1714_1711 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER if exists no_pembayaran ON public.pembayaranpelayanan_t;');

        $this->execute('DROP FUNCTION if exists public.no_pembayaranpelayanan_t();');

        $this->execute("
                    CREATE OR REPLACE FUNCTION public.no_pembayaranpelayanan_t()
  RETURNS trigger AS
\$BODY\$-- author yaya

DECLARE
    vId INTEGER := 1;  
    vPrefix VARCHAR;
    vLast VARCHAR;
    vYear VARCHAR;
    vMonth VARCHAR;
    vNomor VARCHAR;
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
 -- Untuk Penomoran 
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
                SET tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id ,
                carabayar_id = NEW.carabayar_id,
                penjamin_id = NEW.penjamin_id,
                                tarif_tindakan = tindakansudahbayar_t.jmlbiaya_tindakan,
                                tarif_satuan = (tindakansudahbayar_t.additional_data::json->>'tarif_satuan')::INTEGER,
                                tarifcyto_tindakan = (tindakansudahbayar_t.additional_data::json->>'tarif_cyto')::INTEGER,
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
              created_by
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
              NEW.created_by as created_by
            FROM json_populate_recordset(null::obatsudahbayar_t,vDataObat::json);
     -- Setelah insert updatekan ke obat alkes pasien
    UPDATE obatalkespasien_t 
                SET obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id,
                carabayar_id = NEW.carabayar_id,
                penjamin_id = NEW.penjamin_id
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
  vUangDiterima := NEW.total_terbayar;
    IF (vCaraPembayaran != '31')
     THEN
            NEW.penggunaan_uangmuka := 0;
      INSERT INTO piutangasuransi_t (
                        pembayaranpelayanan_id,
            penjamin_id,
            carabayar_id,
            jmlpiutangasuransi,
            created_by
        ) VALUES (
           NEW.pembayaranpelayanan_id,
           NEW.penjamin_id,
           NEW.carabayar_id,
           NEW.total_biayapelayanan,
           NEW.created_by
        );
  ELSE
            IF (NEW.penggunaan_uangmuka > 0)
                THEN
                        INSERT INTO pemakaianuangmuka_t (
                            pembayaranpelayanan_id,
                            pendaftaran_id,
                            tgl_pemakaian,
                            total_uangmuka,
                            pemakaian_uangmuka,
                            sisa_uangmuka,
                            created_by
                        ) VALUES (
                            NEW.pembayaranpelayanan_id,
                            vIdPendaftaran,
                            NEW.tgl_pembayaran,
                            vJumlahUangMuka,
                            NEW.penggunaan_uangmuka,
                            (vJumlahUangMuka - NEW.penggunaan_uangmuka),
                            NEW.created_by
                        ) RETURNING pemakaianuangmuka_id INTO vIdPemakaianUang;
                        
                        UPDATE bayaruangmuka_t SET
                            pemakaianuangmuka_id = vIdPemakaianUang
                        WHERE pendaftaran_id = vIdPendaftaran AND pemakaianuangmuka_id IS NULL;
            END IF;
  END IF; 

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

        $this->execute('ALTER FUNCTION public.no_pembayaranpelayanan_t()
  OWNER TO postgres;');

        $this->execute('CREATE TRIGGER no_pembayaran
                      BEFORE INSERT
                      ON public.pembayaranpelayanan_t
                      FOR EACH ROW
                      EXECUTE PROCEDURE public.no_pembayaranpelayanan_t();');

        $this->execute('DROP TRIGGER if exists delete_pembayaran ON public.pembayaranpelayanan_t;');

        $this->execute('DROP FUNCTION if exists public.delete_tindakansudahbayar();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.delete_tindakansudahbayar()
  RETURNS trigger AS
\$BODY\$-- author: Rizqi Febian

            DECLARE

            varPembayaranPelayananId INT;
            varPendaftaranId INT;
            isDeleted BOOLEAN;
            dateDeleted DATE;
            deletedBy INT;

            BEGIN
            varPembayaranPelayananId := NEW.pembayaranpelayanan_id;
            varPendaftaranId := NEW.pendaftaran_id;
            isDeleted := NEW.is_deleted;
            dateDeleted := NEW.deleted_date;
            deletedBy := NEW.deleted_by;

            IF isDeleted = TRUE THEN
                        UPDATE tandabuktibayar_t set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE piutangasuransi_t set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE tindakanpelayanan_t SET tindakansudahbayar_id  = NULL FROM (SELECT tindakanpelayanan_id, tindakansudahbayar_id FROM tindakansudahbayar_t where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = FALSE) as subquery WHERE tindakanpelayanan_t.tindakanpelayanan_id = subquery.tindakanpelayanan_id;
                        UPDATE obatalkespasien_t SET obatsudahbayar_id  = NULL FROM (SELECT obatalkespasien_id, obatsudahbayar_id FROM obatsudahbayar_t where pembayaranpelayanan_id = varPembayaranPelayananId) as subquery WHERE obatalkespasien_t.obatalkespasien_id = subquery.obatalkespasien_id;
                        UPDATE obatsudahbayar_t  set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE tindakansudahbayar_t  set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE bayaruangmuka_t set pemakaianuangmuka_id = NULL, jumlah_uangmuka = subquery.jumlahuangmuka FROM (SELECT pemakaianuangmuka_id,SUM(pemakaian_uangmuka) as jumlahuangmuka FROM pemakaianuangmuka_t WHERE pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = FALSE GROUP BY pemakaianuangmuka_id, pemakaian_uangmuka) as subquery WHERE bayaruangmuka_t.pemakaianuangmuka_id = subquery.pemakaianuangmuka_id;
                        UPDATE pemakaianuangmuka_t set is_deleted = TRUE,  deleted_date = dateDeleted, deleted_by = deletedBy where pembayaranpelayanan_id = varPembayaranPelayananId and is_deleted = false;
                        UPDATE pendaftaran_t set status_bayar = 349  WHERE pendaftaran_id = varPendaftaranId;
                        -- Add Ikbal 2019-03-15 update status bayar table penjualanresep_t
                        UPDATE penjualanresep_t x
                        SET status_bayar = 349
                        FROM obatalkespasien_t AS y,
                        obatsudahbayar_t z
                        WHERE x.penjualanresep_id = y.penjualanresep_id
                        AND y.obatalkespasien_id = z.obatalkespasien_id
                        AND z.pembayaranpelayanan_id = varPembayaranPelayananId;
            END IF;

            RETURN NEW;

            END
            \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.delete_tindakansudahbayar()
                    OWNER TO postgres;');

        $this->execute('CREATE TRIGGER delete_pembayaran
                      AFTER UPDATE
                      ON public.pembayaranpelayanan_t
                      FOR EACH ROW
                      EXECUTE PROCEDURE public.delete_tindakansudahbayar();
                    ');

        $this->execute('DROP TRIGGER if exists pembayaran_t ON public.pembayaran_t;');

        $this->execute('DROP FUNCTION if exists public.ins_pembayaran();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.ins_pembayaran()
  RETURNS trigger AS
\$BODY\$
    
DECLARE
    paramJson VARCHAR;
    vPembayaranpelayanan json;
    vPembayaranpenjamin json;
    vPembayaranmetode json;
    vPembayaran_id INTEGER;
    vrow json;
    
    
BEGIN
    paramJson := NEW.additional_data;
    vPembayaranpelayanan := paramJson::json->>'pembayaran_pelayanan';
    vPembayaranpenjamin := paramJson::json->>'pembayaran_penjamin';
    vPembayaranmetode := paramJson::json->>'pembayaran_jenis_pembayaran';
    vPembayaran_id := NEW.pembayaran_id;


-- insert ke pembayaranpelayanan_t
    FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpelayanan)
  LOOP
        IF (vrow->>'pasien_id' IS NOT NULL) THEN
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
                    (vrow->>'penjualanresep_id,')::INTEGER,
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
                            total_dibayar

         ) VALUES (
                            vPembayaran_id,
                            (vrow->>'metode_bayar')::VARCHAR,
                            (vrow->>'no_kartu')::VARCHAR,
                            (vrow->>'total_dibayar')::FLOAT                     
                    );
        END IF;
    END LOOP;



    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION public.ins_pembayaran()
                    OWNER TO postgres;');

        $this->execute('CREATE TRIGGER pembayaran_t
                      BEFORE INSERT
                      ON public.pembayaran_t
                      FOR EACH ROW
                      EXECUTE PROCEDURE public.ins_pembayaran();
                    ');

    
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191212_110317_trigger_1691_1714_1711 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191212_110317_trigger_1691_1714_1711 cannot be reverted.\n";

        return false;
    }
    */
}
