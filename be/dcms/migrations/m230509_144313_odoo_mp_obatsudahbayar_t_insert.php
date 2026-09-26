<?php

use yii\db\Migration;

/**
 * Class m230509_144313_odoo_mp_obatsudahbayar_t_insert
 */
class m230509_144313_odoo_mp_obatsudahbayar_t_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE OR REPLACE FUNCTION public.obatsudahbayar_t_insert()
        RETURNS trigger
        LANGUAGE plpgsql
        AS \$function\$
                    BEGIN
                            IF ((NEW.additional_data::json->>'is_penjaminutama')::BOOLEAN IS TRUE) THEN
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
                                    obatalkespasien_id ,
                                    sumberdana_id ,
                                    racikan_id ,
                                    returresepdetail_id ,
                                    tipepaket_id ,
                                    ruangan_id ,
                                    CASE
                                            WHEN (NEW.additional_data::json->>'carabayar_id')::INTEGER IS NULL THEN carabayar_id
                                            ELSE (NEW.additional_data::json->>'carabayar_id')::INTEGER
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
                                            WHEN (NEW.additional_data::json->>'penjamin_id')::INTEGER IS NULL THEN penjamin_id
                                            ELSE (NEW.additional_data::json->>'penjamin_id')::INTEGER
                                    END, -- penjamin_id
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
                                    ((NEW.additional_data::json->>'data_dijamin')::json->>'harusbayar')::float,
                                    (NEW.additional_data::json->>'tarif_diskon')::float,
                                    NEW.pembayaran_id,
                                    (NEW.additional_data::json->>'is_penjaminutama')::BOOLEAN, --is_penjaminutama
                                    ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float, -- dijamin payer 
                                    ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_subpayer')::float,  -- dijamin subpayer
                                    case
                                        when (((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin')::float > 0 and ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float > 0) then  
                                            (NEW.additional_data::json->>'tarif_diskon')::float 
                                        else 0::float
                                    end, -- diskon_payer 
                                    case
                                        when (((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin')::float > 0 and ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_payer')::float = 0 and ((NEW.additional_data::json->>'data_dijamin')::json->>'dijamin_subpayer')::float > 0) then 
                                            (NEW.additional_data::json->>'tarif_diskon')::float 
                                        else 0::float
                                    end, -- diskon_subpayer 
                                    case
                                        when obatalkespasien_t.tarif_dijamin <= 0 and (NEW.additional_data::json->>'tarif_diskon')::float >0 then 
                                            (NEW.additional_data::json->>'tarif_diskon')::float 
                                        else 0::float
                                    end --diskon pasien 
            --                        gross_dijamin_payer, 
            --                        gross_dijamin_subpayer, 
            --                        gross_pasien                                                                                             
                        FROM obatalkespasien_t
                        WHERE obatalkespasien_id = NEW.obatalkespasien_id;
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
        echo "m230509_144313_odoo_mp_obatsudahbayar_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_144313_odoo_mp_obatsudahbayar_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
