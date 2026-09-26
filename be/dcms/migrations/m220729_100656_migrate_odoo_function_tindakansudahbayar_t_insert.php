<?php

use yii\db\Migration;

/**
 * Class m220729_100656_migrate_odoo_function_tindakansudahbayar_t_insert
 */
class m220729_100656_migrate_odoo_function_tindakansudahbayar_t_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tindakansudahbayar_t_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                         
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
                        tarif_dibayarkan,
                                                tarif_diskon,
                                                pembayaran_id,
                                                is_penjaminutama
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
                                                CASE
                                                    WHEN (NEW.additional_data::json->>\'carabayar_id\')::INTEGER IS NULL THEN carabayar_id
                                                    ELSE (NEW.additional_data::json->>\'carabayar_id\')::INTEGER
                                                END, -- carabayar_id
                        pendaftaran_id ,
                        hasilpemeriksaanrad_id ,
                        jeniskasuspenyakit_id ,
                        hasilpemeriksaanrm_id ,
                        ruangan_id ,
                        konsulpoli_id ,
                        pasienmasukpenunjang_id ,
                        hasilpemeriksaanlabdetail_id ,
                                                CASE
                                                    WHEN (NEW.additional_data::json->>\'penjamin_id\')::INTEGER IS NULL THEN penjamin_id
                                                    ELSE (NEW.additional_data::json->>\'penjamin_id\')::INTEGER
                                                END, -- penjamin_id
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
                        \'BILLING\',
                        no_tindakanpelayanan,
                                                CASE WHEN parent_id IS NOT NULL
                                                    THEN    
                                                        (((NEW.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float / NEW.jmlbiaya_tindakan) * harga_origin
                                                    ELSE 
                                                        ((NEW.additional_data::json->>\'data_dijamin\')::json->>\'dijamin\')::float 
                                                END, --tarif_dijamin
                                                CASE WHEN parent_id IS NOT NULL
                                                    THEN    
                                                        (((NEW.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::float / NEW.jmlbiaya_tindakan) * harga_origin
                                                    ELSE 
                                                        ((NEW.additional_data::json->>\'data_dijamin\')::json->>\'harusbayar\')::float
                                                END, --tarif_dibayarkan
                                                CASE WHEN parent_id IS NOT NULL
                                                    THEN    
                                                        ((NEW.additional_data::json->>\'tarif_diskon\')::float / NEW.jmlbiaya_tindakan) * harga_origin
                                                    ELSE 
                                                        (NEW.additional_data::json->>\'tarif_diskon\')::float
                                                END, --tarif_diskon                                                                             
                                                NEW.pembayaran_id,
                                                (NEW.additional_data::json->>\'is_penjaminutama\')::BOOLEAN
                FROM tindakanpelayanan_t
                WHERE (tindakanpelayanan_id = NEW.tindakanpelayanan_id OR parent_id = NEW.tindakanpelayanan_id);
                
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
        echo "m220729_100656_migrate_odoo_function_tindakansudahbayar_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_100656_migrate_odoo_function_tindakansudahbayar_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
