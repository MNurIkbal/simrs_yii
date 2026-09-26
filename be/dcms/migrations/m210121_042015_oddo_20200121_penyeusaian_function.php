<?php

use yii\db\Migration;

/**
 * Class m210121_042015_oddo_20200121_penyeusaian_function
 */
class m210121_042015_oddo_20200121_penyeusaian_function extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_delete\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
DECLARE  
        v_daftartindakan_id int;
        v_ruangan_id int;
        v_penjamin_id int;
        v_instalasi_id int;
        v_pegawai_id int;
    v_totalpembulatan float;
    v_total_diskon float;
    v_total_discountpembayaran float;
    v_totaladministrasi float;
        v_totaltunai float;

BEGIN
        
        SELECT
            pembayaran_t.total_tunai
            INTO
            v_totaltunai
        FROM pembayaran_t
        WHERE pembayaran_t.pembayaran_id = NEW.pembayaran_id;
        
        IF(NEW.is_deleted IS TRUE and v_totaltunai <> 0)
        THEN
            
           -- INSERT table history pembayaran_r, menjadi Deposit Refund
           INSERT INTO pembayaran_r (       
                pembayaran_id,
                pendaftaran_id,
                pasienadmisi_id,
                total_tagihan,
                total_dibayar,
                total_dijamin,
                total_sisatagihan,
                total_kembalian,
                total_administrasi,
                total_pembulatan,
                total_pembebasan,
                penggunaan_uangmuka,
                pemberianpiutang_id,
                total_ditagihkan,
                total_tunai,
                total_nontunai,
                total_discount,
                total_discountpembayaran,
                catatan,
                tipe_pembayaran,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                keterangan
            )
            SELECT
                pembayaran_id,
                pendaftaran_id,
                pasienadmisi_id,
                -1 * total_tagihan ,
                -1 * total_dibayar ,
                -1 * total_dijamin ,
                -1 * total_sisatagihan ,
                -1 * total_kembalian ,
                -1 * total_administrasi ,
                -1 * total_pembulatan ,
                -1 * total_pembebasan ,
                penggunaan_uangmuka ,
                pemberianpiutang_id ,
                -1 * total_ditagihkan ,
                -1 * total_tunai ,
                0,
                -1 * total_discount ,
                -1 * total_discountpembayaran ,
                catatan,
                682,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                'REFUND'
            FROM pembayaran_t
            WHERE pembayaran_id = NEW.pembayaran_id;
    
    END IF;
----------------------------> insert pembulatan ke tindakanpelayanan_r <---------------------------------   
    
            SELECT
                pembayaran_t.total_pembulatan
            INTO
                v_totalpembulatan
            FROM pembayaran_t
            WHERE pembayaran_t.pembayaran_id = NEW.pembayaran_id;
    
    IF(NEW.is_deleted IS TRUE and v_totalpembulatan <> 0)
        THEN

            SELECT 
                pendaftaran_t.ruangan_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id
            INTO
                v_ruangan_id,
                v_penjamin_id,
                v_instalasi_id,
                v_pegawai_id
            FROM pendaftaran_t
            WHERE pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
            
            v_daftartindakan_id:=99990; -- daftartindakan_id untuk pembulatan
                    
        INSERT INTO tindakanpelayanan_r (       
                pendaftaran_id,
                daftartindakan_id,
                tarif_satuan,
                tarif_tindakan,
                qty_tindakan,
                keterangan,
                ruangan_id,
                instalasi_id,
                penjamin_id,
                dokterpenanggungjawab_id,
                tgl_tindakan,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                                tgl_proses,
                pembayaran_id
        )VALUES(
                NEW.pendaftaran_id,
                v_daftartindakan_id,
                v_totalpembulatan,
                v_totalpembulatan,
                '-1',
                'BILLING CANCEL',
                v_ruangan_id,
                v_instalasi_id,
                v_penjamin_id,
                v_pegawai_id,
                NEW.created_date,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.created_date,
                NEW.pembayaran_id
        );
        
    END IF;
    
    ---------------------------> insert diskon ke tindakanpelayanan_r <-------------------------
    
            SELECT
                pembayaran_t.total_discount,
                pembayaran_t.total_discountpembayaran
            INTO
                v_total_diskon,
                v_total_discountpembayaran
            FROM pembayaran_t
            WHERE pembayaran_t.pembayaran_id = NEW.pembayaran_id;
    
    IF(NEW.is_deleted IS TRUE and (COALESCE(v_total_diskon,0) + COALESCE(v_total_discountpembayaran,0) <> 0))
        THEN

            SELECT 
                pendaftaran_t.ruangan_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id
            INTO
                v_ruangan_id,
                v_penjamin_id,
                v_instalasi_id,
                v_pegawai_id
            FROM pendaftaran_t
            WHERE pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
            
            v_daftartindakan_id:=99991; -- daftartindakan_id untuk diskon
            
        INSERT INTO tindakanpelayanan_r (       
                pendaftaran_id,
                daftartindakan_id,
                tarif_satuan,
                tarif_tindakan,
                qty_tindakan,
                keterangan,
                ruangan_id,
                instalasi_id,
                penjamin_id,
                dokterpenanggungjawab_id,
                tgl_tindakan,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                                tgl_proses,
                pembayaran_id
        )VALUES(
                NEW.pendaftaran_id,
                v_daftartindakan_id,
                COALESCE(-1 * v_total_diskon,0)+COALESCE(-1 * v_total_discountpembayaran,0),
                COALESCE(-1 * v_total_diskon,0)+COALESCE(-1 * v_total_discountpembayaran,0),
                '-1',
                'DISCOUNT CANCEL',
                v_ruangan_id,
                v_instalasi_id,
                v_penjamin_id,
                v_pegawai_id,
                NEW.created_date,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.created_date,
                NEW.pembayaran_id
        );
        
    END IF;
    
    -------------------------------------> insert administrasi ke tindakanpelayanan_r <--------------------------------------------
    
            SELECT
                pembayaran_t.total_administrasi
            INTO
                v_totaladministrasi
            FROM pembayaran_t
            WHERE pembayaran_t.pembayaran_id = NEW.pembayaran_id;
    
    IF(NEW.is_deleted IS TRUE and v_totaladministrasi <> 0)
        THEN

            SELECT 
                pendaftaran_t.ruangan_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id
            INTO
                v_ruangan_id,
                v_penjamin_id,
                v_instalasi_id,
                v_pegawai_id
            FROM pendaftaran_t
            WHERE pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
        
            v_daftartindakan_id:=99992; -- daftartindakan_id untuk administrasi
            
        INSERT INTO tindakanpelayanan_r (       
                pendaftaran_id,
                daftartindakan_id,
                tarif_satuan,
                tarif_tindakan,
                qty_tindakan,
                keterangan,
                ruangan_id,
                instalasi_id,
                penjamin_id,
                dokterpenanggungjawab_id,
                tgl_tindakan,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                                tgl_proses,
                pembayaran_id
        )VALUES(
                NEW.pendaftaran_id,
                v_daftartindakan_id,
                -1 * v_totaladministrasi,
                -1 * v_totaladministrasi,
                '1',
                'BILLING CANCEL',
                v_ruangan_id,
                v_instalasi_id,
                v_penjamin_id,
                v_pegawai_id,
                NEW.created_date,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.created_date,
                NEW.pembayaran_id
        );
        
    END IF;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"tindakansudahbayar_t_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

BEGIN
        -- INSERT table history tindakanpelayanan_r BILLING(+)
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
            no_tindakanpelayanan,
            tarif_dijamin,
            tarif_dibayarkan
        )
        SELECT  
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
            NEW.tindakansudahbayar_id,
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
            'BILLING',
            no_tindakanpelayanan,
                        ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin')::float,
                        ((NEW.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float 
    FROM tindakanpelayanan_t
    WHERE tindakanpelayanan_id = NEW.tindakanpelayanan_id;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");


        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"obatsudahbayar_t_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

BEGIN
        -- INSERT table history obatalkespasien_r BILLING(+)
        INSERT INTO obatalkespasien_r (     
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                keterangan,
                no_obatalkespasien,
                                tarif_dijamin,
                                tarif_dibayarkan
        )
        SELECT  
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                NEW.obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                'BILLING',
                no_obatalkespasien,
                                ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin')::float,
                                ((NEW.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float 
    FROM obatalkespasien_t
    WHERE obatalkespasien_id = NEW.obatalkespasien_id;
    
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
        echo "m210121_042015_oddo_20200121_penyeusaian_function cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_042015_oddo_20200121_penyeusaian_function cannot be reverted.\n";

        return false;
    }
    */
}
