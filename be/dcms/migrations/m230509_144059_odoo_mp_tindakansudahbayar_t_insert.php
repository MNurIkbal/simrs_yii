<?php

use yii\db\Migration;

/**
 * Class m230509_144059_odoo_mp_tindakansudahbayar_t_insert
 */
class m230509_144059_odoo_mp_tindakansudahbayar_t_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE OR REPLACE FUNCTION public.tindakansudahbayar_t_insert()
        RETURNS trigger
        LANGUAGE plpgsql
        AS \$function\$         

                    begin
                            IF ((NEW.additional_data::json->>'is_penjaminutama')::BOOLEAN IS TRUE) THEN
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
                                is_penjaminutama,
                                dijamin_payer,
                                dijamin_subpayer,
                                diskon_payer,
                                diskon_subpayer, 
                                diskon_pasien
        --                        gross_dijamin_payer, 
        --                        gross_dijamin_subpayer, 
        --                        gross_pasien
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
                                NEW.tindakansudahbayar_id,
                                CASE
                                    WHEN (NEW.additional_data::json->>'carabayar_id')::INTEGER IS NULL THEN carabayar_id
                                    ELSE (NEW.additional_data::json->>'carabayar_id')::INTEGER
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
                                tgl_tindakan ,
                                tarif_rsakomodasi ,
                                tarif_medis ,
                                tarif_paramedis ,
                                tarif_bhp ,
                                tarif_satuan ,
                                tindakanpelayanan_t.tarif_tindakan ,
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
                                CASE WHEN parent_id IS NOT NULL
                                    THEN  
                                        (tarif_satuan/parent.tarif_tindakan) * ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin')::float 
                                    ELSE 
                                        ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin')::float 
                                END, --tarif_dijamin
                                CASE WHEN parent_id IS NOT NULL
                                    THEN    
                                            ((harga_origin/parent.tarif_tindakan) * ((NEW.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float)
                                    ELSE 
                                        ((NEW.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float
                                END, --tarif_dibayarkan
                                CASE WHEN parent_id IS NOT NULL
                                    THEN    
                                            ((harga_origin/parent.tarif_tindakan) * ((NEW.additional_data::json->>'tarif_diskon')::float))
                                    ELSE  
                                        (NEW.additional_data::json->>'tarif_diskon')::float 
                                end, -- tarif diskon 
                                NEW.pembayaran_id, --pembayaran_id
                                (NEW.additional_data::json->>'is_penjaminutama')::BOOLEAN,
                                CASE WHEN parent_id IS NOT NULL
                                    THEN  
                                        (tarif_satuan/parent.tarif_tindakan) * ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float 
                                    ELSE 
                                        ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float 
                                end, -- dijamin payer 
                                CASE WHEN parent_id IS NOT NULL
                                    THEN  
                                        (tarif_satuan/parent.tarif_tindakan) * ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_subpayer')::float 
                                    ELSE 
                                        ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_subpayer')::float 
                                end, -- dijamin subpayer 
                                case
                                    when (((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin')::float > 0 and ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float > 0) then 
                                        case when parent_id is not null then
                                            (tarif_satuan/parent.tarif_tindakan) * (NEW.additional_data::json->>'tarif_diskon')::float 
                                        else 
                                            (NEW.additional_data::json->>'tarif_diskon')::float 
                                        end 
                                    else 0::float
                                end, -- diskon_payer 
                                case
                                    when (((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin')::float > 0 and ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float = 0 and ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_subpayer')::float > 0) then 
                                        case when parent_id is not null then
                                            (tarif_satuan/parent.tarif_tindakan) * (NEW.additional_data::json->>'tarif_diskon')::float 
                                        else 
                                            (NEW.additional_data::json->>'tarif_diskon')::float 
                                        end 
                                    else 0::float
                                end, -- diskon_subpayer 
                                case
                                    when tindakanpelayanan_t.tarif_dijamin <= 0 and (NEW.additional_data::json->>'tarif_diskon')::float >0 then 
                                        case when parent_id is not null then
                                            (tarif_satuan/parent.tarif_tindakan) * (NEW.additional_data::json->>'tarif_diskon')::float 
                                        else 
                                            (NEW.additional_data::json->>'tarif_diskon')::float 
                                        end
                                    else 0::float
                                end --diskon pasien 
                        FROM tindakanpelayanan_t
                        left join (select tindakanpelayanan_id,tarif_tindakan from tindakanpelayanan_t) parent on parent.tindakanpelayanan_id = tindakanpelayanan_t.parent_id
                        WHERE (tindakanpelayanan_t.tindakanpelayanan_id = NEW.tindakanpelayanan_id OR tindakanpelayanan_t.parent_id = NEW.tindakanpelayanan_id);
                        end if; 
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
        echo "m230509_144059_odoo_mp_tindakansudahbayar_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_144059_odoo_mp_tindakansudahbayar_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
