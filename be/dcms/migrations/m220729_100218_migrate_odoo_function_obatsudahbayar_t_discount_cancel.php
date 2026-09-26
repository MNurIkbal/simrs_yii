<?php

use yii\db\Migration;

/**
 * Class m220729_100218_migrate_odoo_function_obatsudahbayar_t_discount_cancel
 */
class m220729_100218_migrate_odoo_function_obatsudahbayar_t_discount_cancel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."obatsudahbayar_t_discount_cancel"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ 
                
            DECLARE
                v_keterangan VARCHAR;
                v_tglbatal TIMESTAMP;
                v_tarif_diskon float;
                v_tarif_dijamin float;
                    v_is_penjaminutama BOOLEAN;

            BEGIN
                SELECT
                    deleted_date
                    INTO 
                    v_tglbatal
                FROM pembayaranpelayanan_t
                WHERE pembayaranpelayanan_id = NEW.pembayaranpelayanan_id;
                        
                    IF(NEW.is_deleted IS TRUE and (NEW.additional_data::json->>\'tarif_diskon\')::float <> 0)
                    THEN
                       v_tarif_diskon := (NEW.additional_data::json->>\'tarif_diskon\')::float;
                       v_tarif_dijamin := ((NEW.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float;
                                 v_is_penjaminutama := (NEW.additional_data::json->>\'is_penjaminutama\')::BOOLEAN;        

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
                                -1 ,
                                0 - v_tarif_diskon ,
                                signa_oa ,
                                harganetto_oa ,
                                0 - v_tarif_diskon ,
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
                                NULL ,
                                is_penatajasa ,
                                NULL ,
                                status_bmhp ,
                                NULL ,
                                signa ,
                                NULL ,
                                created_date ,
                                created_by ,
                                modified_count ,
                                last_modified_date ,
                                last_modified_by ,
                                is_deleted ,
                                is_active ,
                                deleted_date , 
                                deleted_by ,
                                \'DISCOUNT CANCEL\',
                                no_obatalkespasien,
                                (CASE WHEN v_tarif_dijamin <> 0 THEN v_tarif_diskon ELSE 0 END),
                                (CASE WHEN v_tarif_dijamin <> 0 THEN 0 ELSE v_tarif_diskon END), 
                                0::float,
                                OLD.pembayaran_id,
                                                    v_is_penjaminutama
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
        echo "m220729_100218_migrate_odoo_function_obatsudahbayar_t_discount_cancel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_100218_migrate_odoo_function_obatsudahbayar_t_discount_cancel cannot be reverted.\n";

        return false;
    }
    */
}
