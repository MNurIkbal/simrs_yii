<?php

use yii\db\Migration;

/**
 * Class m230509_163504_odoo_mp_proses_pembayaranpelayanan
 */
class m230509_163504_odoo_mp_proses_pembayaranpelayanan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION public.proses_pembayaranpelayanan()
            RETURNS trigger
            LANGUAGE plpgsql
            AS \$function\$
       
            DECLARE
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
                vNumber VARCHAR;
                vPembayaran_id INTEGER;
            
            BEGIN
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
                        
                IF (NEW.total_iurbiaya > NEW.total_bayartindakan) 
                THEN
                    NEW.total_terbayar := NEW.total_bayartindakan;
                    IF (vCaraPembayaran = '31') 
                    THEN
                        NEW.is_lunas = FALSE;
                    END IF;
                END IF;
            
                vIdPendaftaran := NEW.pendaftaran_id;
                paramJson := NEW.additional_data;  
                vDataObat := paramJson::json->>'obat';
                vDataTindakan := paramJson::json->>'tindakan';
                
                -- Insert ke  pembayaranpelayanan_t
                IF (json_array_length(vDataTindakan::json) > 0)  
                THEN
                        INSERT INTO tindakansudahbayar_t (
                                pembayaranpelayanan_id,
                                pembayaran_id, 
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
                            ) 
                                SELECT 
                                        NEW.pembayaranpelayanan_id as pembayaranpelayanan_id, 
                                        NEW.pembayaran_id as pembayaran_id, 
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
                                FROM json_populate_recordset(null::tindakansudahbayar_t,vDataTindakan::json
                        );
                            
                            -- Setelah insert updatekan ke tindakanpelayanan_t
                        UPDATE tindakanpelayanan_t 
                        SET tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id,
            --  tarif_tindakan = tindakansudahbayar_t.jmlbiaya_tindakan,  ***--> ini fungsinya buat apa yah?
            --  tarif_satuan = (tindakansudahbayar_t.additional_data::json->>'tarif_satuan')::FLOAT, ***--> ini fungsinya buat apa yah?
                        keterangantindakan = (tindakansudahbayar_t.additional_data::json->>'keterangan')::TEXT,
                        tarifcyto_tindakan = (tindakansudahbayar_t.additional_data::json->>'tarif_cyto')::FLOAT,
                        tarif_dijamin = 
                                CASE WHEN parent_id IS NOT NULL
                                        THEN    
                                        0
                                                --((((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin')::float / tindakansudahbayar_t.jmlbiaya_tindakan) * harga_origin)
                                        ELSE ((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin')::float
                                END,
                        dijamin_payer = 
                                CASE WHEN parent_id IS NOT NULL
                                        THEN    
                                                0 --((tarif_satuan) * ((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float)
            --                                    ((((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float / tindakansudahbayar_t.jmlbiaya_tindakan) * harga_origin)
                                        ELSE ((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float
                                END,
                        dijamin_subpayer = 
                                CASE WHEN parent_id IS NOT NULL
                                        THEN    
                                                0 --((tarif_satuan) * ((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin_subpayer')::float)
            --                                    ((((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin_subpayer')::float / tindakansudahbayar_t.jmlbiaya_tindakan) * harga_origin)
                                        ELSE ((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin_subpayer')::float
                                END,
                        tarif_dibayarkan = 
                        CASE WHEN parent_id IS NOT NULL
                        THEN    
                                    0 --((harga_origin/tarif_tindakan) * ((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float)
            --                    ((((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float / tindakansudahbayar_t.jmlbiaya_tindakan) * harga_origin)
                        ELSE ((tindakansudahbayar_t.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float
                        END,                        
                        tarif_diskon = 
                                CASE WHEN parent_id IS NOT NULL
                                THEN    
                                    0 --(tindakansudahbayar_t.additional_data::json->>'tarif_diskon')::FLOAT 
            --                      (((tindakansudahbayar_t.additional_data::json->>'tarif_diskon')::FLOAT / tindakansudahbayar_t.jmlbiaya_tindakan) * harga_origin)
                                ELSE (tindakansudahbayar_t.additional_data::json->>'tarif_diskon')::FLOAT
                                END,
                        is_valid = TRUE,
                        pembayaran_id = tindakansudahbayar_t.pembayaran_id                                      
                        FROM tindakansudahbayar_t 
                        WHERE (tindakanpelayanan_t.tindakansudahbayar_id IS NULL OR tindakanpelayanan_t.tipepaket_id IS NULL)
                        AND tindakansudahbayar_t.pembayaranpelayanan_id = NEW.pembayaranpelayanan_id
                        AND (tindakansudahbayar_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id OR tindakansudahbayar_t.tindakanpelayanan_id = tindakanpelayanan_t.parent_id) ;
                        END IF;
                        
                -- Insert ke  obatsudahbayar_t
                IF (json_array_length(vDataObat::json) > 0)  
                THEN
                        INSERT INTO obatsudahbayar_t (
                                pembayaranpelayanan_id, 
                                pembayaran_id, 
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
                        ) 
                        SELECT 
                                NEW.pembayaranpelayanan_id as pembayaranpelayanan_id, 
                                NEW.pembayaran_id as pembayaran_id, 
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
                                        tarif_diskon = (obatsudahbayar_t.additional_data::json->>'tarif_diskon')::FLOAT,
                                        keterangan = (obatsudahbayar_t.additional_data::json->>'keterangan')::TEXT,
                                        tarif_dijamin = ((obatsudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin')::FLOAT,
                                        dijamin_payer = ((obatsudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::FLOAT,
                                        dijamin_subpayer = ((obatsudahbayar_t.additional_data::json->>'data_dijamin')::json->>'dijamin_subpayer')::FLOAT,
                                        tarif_dibayarkan = ((obatsudahbayar_t.additional_data::json->>'data_dijamin')::json->>'harusbayar')::FLOAT,
                                        pembayaran_id = obatsudahbayar_t.pembayaran_id
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
            --  INSERT INTO tandabuktibayar_t (
            --     ruangan_id, 
            --     pembayaranpelayanan_id, 
            --     tglbuktibayar,
            --     darinama_bkm,
            --     sebagaipembayaran_bkm,
            --     jmlpembayaran,
            --     biayaadministrasi,
            --     biayamaterai,
            --     uangditerima,
            --     uangkembalian,
            --     namapemilik_rek,
            --     no_rek,
            --     carapembayaran,
            --     pegawai1_id,
            --     created_by,
            --     shift_id,
            --      pembayaran_id
            --   ) VALUES (
            --     NEW.ruangan_id,
            --     NULL,
            --     NEW.tgl_pembayaran,
            --     vNamaBkm,
            --     vKeterangan,
            --     NEW.total_biayapelayanan,
            --     NEW.biaya_administrasi,
            --     0,
            --     NEW.total_bayartindakan,
            --     vUangKembalian,
            --     NEW.nama_pemrekening,
            --     NEW.no_rekening,
            --     vCaraPembayaran,
            --     vPegawaiId,
            --     NEW.created_by,
            --     vShifId,
            --      NEW.pembayaran_id
            --   ) RETURNING tandabuktibayar_id INTO vTandaBuktiBayarId;
            --     NEW.tandabuktibayar_id := vTandaBuktiBayarId;
                        RETURN NEW;
            END
            \$function\$
            ;       
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_163504_odoo_mp_proses_pembayaranpelayanan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_163504_odoo_mp_proses_pembayaranpelayanan cannot be reverted.\n";

        return false;
    }
    */
}
