<?php

use yii\db\Migration;

/**
 * Class m200917_043757_migrate_20200917_trg_nopembayaranpelayanan
 */
class m200917_043757_migrate_20200917_trg_nopembayaranpelayanan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"no_pembayaranpelayanan_t\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$-- author yaya

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

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200917_043757_migrate_20200917_trg_nopembayaranpelayanan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200917_043757_migrate_20200917_trg_nopembayaranpelayanan cannot be reverted.\n";

        return false;
    }
    */
}
