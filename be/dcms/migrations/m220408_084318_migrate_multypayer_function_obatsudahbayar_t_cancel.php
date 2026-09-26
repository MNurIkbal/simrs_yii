<?php

use yii\db\Migration;

/**
 * Class m220408_084318_migrate_multypayer_function_obatsudahbayar_t_cancel
 */
class m220408_084318_migrate_multypayer_function_obatsudahbayar_t_cancel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."obatsudahbayar_t_cancel"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ 
                
            DECLARE
                    v_keterangan VARCHAR;
                            v_tglbatal TIMESTAMP;
            BEGIN
                        SELECT
                            deleted_date
                            INTO 
                            v_tglbatal
                        FROM pembayaranpelayanan_t
                        WHERE pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
                        
                    IF(NEW.is_deleted IS TRUE)
                    THEN
                        -- INSERT table history obatalkespasien_r menjadi ACCRUAL(+), jika is_deleted=TRUE
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
                                            tarif_dibayarkan,
                                            tarif_diskon,
                                            pembayaran_id
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
                            \'ACCRUAL\',
                                            no_obatalkespasien,
                                            0,
                                            0,
                                            0,
                                            NULL
                            FROM obatalkespasien_t
                        WHERE obatalkespasien_id = NEW.obatalkespasien_id;
                    
                        -- INSERT table history obatalkespasien_r menjadi BILLING CANCEL(-), jika is_deleted=TRUE
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
                                                    tarif_dibayarkan,
                                                    tarif_diskon,
                                                    pembayaran_id,
                                                    is_penjaminutama                                    
                                )
                                SELECT
                                obatalkespasien_id ,
                                sumberdana_id ,
                                racikan_id ,
                                returresepdetail_id ,
                                tipepaket_id ,
                                ruangan_id ,
                                                    CASE
                                                        WHEN (OLD.additional_data::json->>\'carabayar_id\')::INTEGER IS NULL THEN carabayar_id
                                                        ELSE (OLD.additional_data::json->>\'carabayar_id\')::INTEGER
                                                    END, -- carabayar_id
                                pegawai_id ,
                                daftartindakan_id ,
                                tindakanpelayanan_id ,
                                satuankecil_id ,
                                shift_id ,
                                pendaftaran_id ,
                                obatalkes_id ,
                                pasien_id ,
                                                    CASE
                                                        WHEN (OLD.additional_data::json->>\'penjamin_id\')::INTEGER IS NULL THEN penjamin_id
                                                        ELSE (OLD.additional_data::json->>\'penjamin_id\')::INTEGER
                                                    END, -- penjamin_id
                                kelaspelayanan_id ,
                                pasienanastesi_id ,
                                pasienmasukpenunjang_id ,
                                pasienadmisi_id ,
                                NEW.obatsudahbayar_id ,
                                penjualanresep_id ,
                                v_tglbatal ,
                                r ,
                                rke ,
                                permintaan_oa ,
                                jmlkemasan_oa ,
                                kekuatan_oa ,
                                satuankekuatan_oa ,
                                -1 * qty_oa ,
                                hargasatuan_oa ,
                                signa_oa ,
                                harganetto_oa ,
                                -1 * hargajual_oa,  
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
                                -1 * qty_konversi ,
                                is_penatajasa ,
                                -1 * det ,
                                status_bmhp ,
                                -1 * det_konversi ,
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
                                \'BILLING CANCEL\',
                                                    no_obatalkespasien,
                                                    -1 * ((OLD.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float,
                                                    -1 * ((OLD.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::float, 
                                                    -1 * (OLD.additional_data::json->>\'tarif_diskon\')::float,
                                                    OLD.pembayaran_id,
                                                    (OLD.additional_data::json->>\'is_penjaminutama\')::BOOLEAN 
                                FROM obatalkespasien_t
                            WHERE obatalkespasien_id = NEW.obatalkespasien_id;
                            
                END IF;
                
                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_084318_migrate_multypayer_function_obatsudahbayar_t_cancel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_084318_migrate_multypayer_function_obatsudahbayar_t_cancel cannot be reverted.\n";

        return false;
    }
    */
}
