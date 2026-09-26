<?php

use yii\db\Migration;

/**
 * Class m230509_153529_odoo_mp_tindakansudahbayar_t_discount_cancel
 */
class m230509_153529_odoo_mp_tindakansudahbayar_t_discount_cancel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION public.tindakansudahbayar_t_discount_cancel()
            RETURNS trigger
            LANGUAGE plpgsql
            AS \$function\$ 
                                   
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
                               
                           IF(NEW.is_deleted IS TRUE AND (NEW.additional_data::json->>'tarif_diskon')::float <> 0)
                           THEN
                               -- INSERT table history tindakanpelayanan_r menjadi ACCRUAL(+), jika is_deleted=TRUE
                              v_tarif_diskon := (NEW.additional_data::json->>'tarif_diskon')::float;
                              v_tarif_dijamin := ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin')::float;
                              v_is_penjaminutama := (NEW.additional_data::json->>'is_penjaminutama')::BOOLEAN;        
        
                               -- INSERT table history tindakanpelayanan_r menjadi BILLING CANCEL(-), jika is_deleted=TRUE
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
                                       modified_count ,
                                       last_modified_date ,
                                       last_modified_by ,
                                       is_deleted ,
                                       is_active ,
                                       deleted_date ,
                                       deleted_by ,
                                       keterangan,
                                       no_tindakanpelayanan,
                                                                       tarif_dijamin,
                                                                       tarif_dibayarkan,
                                                                       tarif_diskon,
                                                                       pembayaran_id,
                                                                       is_penjaminutama                        
                                       )
                                       SELECT
                                       tindakanpelayanan_t.tindakanpelayanan_id ,
                                       shift_id ,
                                       kelaspelayanan_id ,
                                       kelastanggungan_id ,
                                       pasien_id ,
                                       rencanaoperasi_id ,
                                       instalasi_id ,
                                       daftartindakan_id ,
                                       alatmedis_id ,
                                       tipepaket_id ,
                                       NEW.tindakansudahbayar_id ,
                                                                       CASE
                                                                                       WHEN (OLD.additional_data::json->>'carabayar_id')::INTEGER IS NULL THEN carabayar_id
                                                                                       ELSE (OLD.additional_data::json->>'carabayar_id')::INTEGER
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
                                                                                       WHEN (NEW.additional_data::json->>'penjamin_id')::INTEGER IS NULL THEN penjamin_id
                                                                                       ELSE (NEW.additional_data::json->>'penjamin_id')::INTEGER
                                                                       END, -- penjamin_id
                                       pasienadmisi_id ,
                                       verifikasitagihan_id ,
                                       jurnalrekening_id ,
                                       instruksitindakan_id ,
                                       v_tglbatal ,
                                       0 ,
                                       0 ,
                                       0 ,
                                       0 ,
                                                           
                                       0 - CASE WHEN parent_id IS NOT NULL
                                       THEN    
                                           (tarif_satuan/parent.tarif_tindakan) * v_tarif_diskon 
                                       ELSE 
                                           v_tarif_diskon
                                       END,
                                                                   
                                       0 - CASE WHEN parent_id IS NOT NULL
                                       THEN    
                                           (tarif_satuan/parent.tarif_tindakan) * v_tarif_diskon 
                                       ELSE 
                                           v_tarif_diskon
                                       END,
                                                                   
                                       tarifcyto_tindakan ,
                                       satuan_tindakan ,
                                       -1,
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
                                       modified_count ,
                                       last_modified_date ,
                                       last_modified_by ,
                                       is_deleted ,
                                       is_active ,
                                       deleted_date ,
                                       deleted_by ,
                                       'DISCOUNT CANCEL',
                                       no_tindakanpelayanan,
                                                           
                                       CASE WHEN v_tarif_dijamin <> 0 
                                           THEN CASE WHEN parent_id IS NOT NULL
                                                       THEN    
                                                           (tarif_satuan/parent.tarif_tindakan) * v_tarif_diskon 
                                                       ELSE 
                                                           v_tarif_diskon
                                                       END     
                                           ELSE 0 
                                       END,
                   
                                       
                                       CASE WHEN v_tarif_dijamin <> 0 
                                           THEN 0 
                                           ELSE CASE WHEN parent_id IS NOT NULL
                                                       THEN    
                                                           (tarif_satuan/parent.tarif_tindakan) * v_tarif_diskon 
                                                       ELSE 
                                                           v_tarif_diskon
                                                       END 
                                       END,
                                       0,
                                       OLD.pembayaran_id,
                                                                       v_is_penjaminutama
                                       FROM tindakanpelayanan_t
                                       left join (select tindakanpelayanan_id,tarif_tindakan from tindakanpelayanan_t) parent on parent.tindakanpelayanan_id = tindakanpelayanan_t.parent_id
                                   WHERE (tindakanpelayanan_t.tindakanpelayanan_id = NEW.tindakanpelayanan_id  OR parent_id = NEW.tindakanpelayanan_id);
                                   
                       END IF;
                       
                       RETURN NEW;
       
                   END
                   \$function\$;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_153529_odoo_mp_tindakansudahbayar_t_discount_cancel cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_153529_odoo_mp_tindakansudahbayar_t_discount_cancel cannot be reverted.\n";

        return false;
    }
    */
}
