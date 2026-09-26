<?php

use yii\db\Migration;

/**
 * Class m230509_070449_odoo_cutoff_tindakanpelayanan_penunjang
 */
class m230509_070449_odoo_cutoff_tindakanpelayanan_penunjang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TRIGGER IF EXISTS tindakpelayanan_penunjang ON tindakanpelayanan_t;");

        $this->execute("DROP FUNCTION IF EXISTS tindakanpelayanan_penunjang;");

        $this->execute("
        CREATE OR REPLACE FUNCTION public.tindakanpelayanan_penunjang()
        RETURNS trigger
        LANGUAGE plpgsql
        AS \$function\$-- author yaya

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
                    no_tindakanpelayanan,
                    tarif_dijamin,
                    tarif_dibayarkan,
                    tarif_diskon,
                    order_item_no
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
                    NEW.no_tindakanpelayanan,
                    NEW.tarif_dijamin,
                    NEW.tarif_dibayarkan,
                    NEW.tarif_diskon,
                    NEW.order_item_no
                );
            RETURN NEW;

        END
        \$function\$
        ;
        ");

        $this->execute("create trigger tindakpelayanan_penunjang after
        insert
            on
            public.tindakanpelayanan_t for each row execute procedure tindakanpelayanan_penunjang()");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_070449_odoo_cutoff_tindakanpelayanan_penunjang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_070449_odoo_cutoff_tindakanpelayanan_penunjang cannot be reverted.\n";

        return false;
    }
    */
}
