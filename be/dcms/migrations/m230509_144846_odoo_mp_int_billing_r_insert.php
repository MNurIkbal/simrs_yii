<?php

use yii\db\Migration;

/**
 * Class m230509_144846_odoo_mp_int_billing_r_insert
 */
class m230509_144846_odoo_mp_int_billing_r_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE OR REPLACE FUNCTION public.int_billing_r_insert()
        RETURNS trigger
        LANGUAGE plpgsql
        AS \$function\$
        DECLARE
            paramAdditional VARCHAR;
            vPembayaranpelayanan json;
            vrow json;
            vJmlMainPayer float;
            vJmlSubPayer float;
            vCountPayer integer;
        begin
            paramAdditional := NEW.additional_data;
            vPembayaranpelayanan := paramAdditional::json->>'pembayaran_pelayanan';
            
            vCountPayer := 0;
        FOR vrow IN SELECT * FROM json_array_elements(vPembayaranpelayanan)
        loop
                IF ((vrow->>'is_penjaminutama')::boolean IS TRUE) then		
                    IF((vrow->>'total_subsidiasuransi')::float > 0) then 
                    vCountPayer := vCountPayer+1;
                    end if;
                    vJmlMainPayer := (vrow->>'total_subsidiasuransi')::float + (vrow->>'pembulatan')::float;
                else
                    IF((vrow->>'total_subsidiasuransi')::float > 0) then 
                    vCountPayer := vCountPayer+1;
                    end if;
                    vJmlSubPayer := (vrow->>'total_subsidiasuransi')::float + (vrow->>'pembulatan')::float;
                end IF;
        end loop;
            
        -- INSERT table history pembayaran_r--
            INSERT INTO int_billing_r (     
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
                keterangan,
                is_bill_multipayer,
                jumlah_mainpayer,
                jumlah_subpayer
                )VALUES(
                NEW.pembayaran_id ,
                NEW.pendaftaran_id ,
                NEW.pasienadmisi_id ,
                NEW.total_tagihan ,
                NEW.total_dibayar ,
                NEW.total_dijamin,
                NEW.total_sisatagihan ,
                NEW.total_kembalian ,
                NEW.total_administrasi ,
                NEW.total_pembulatan ,
                NEW.total_pembebasan ,
                NEW.penggunaan_uangmuka ,
                NEW.pemberianpiutang_id ,
                NEW.total_ditagihkan ,
                NEW.total_tunai ,
                NEW.total_nontunai,
                NEW.total_discount ,
                NEW.total_discountpembayaran ,
                NEW.catatan ,
                NEW.additional_data ,
                NEW.created_date ,
                NEW.created_by ,
                NEW.modified_count ,
                NEW.last_modified_date ,
                NEW.last_modified_by ,
                NEW.is_deleted ,
                NEW.is_active ,
                NEW.deleted_date ,
                NEW.deleted_by ,
                'RECEIPT',
                (vCountPayer > 1), --json_array_length(new.additional_data::json->'pembayaran_pelayanan') > 1,
                vJmlMainPayer,
                vJmlSubPayer
                );
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
        echo "m230509_144846_odoo_mp_int_billing_r_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_144846_odoo_mp_int_billing_r_insert cannot be reverted.\n";

        return false;
    }
    */
}
