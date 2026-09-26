<?php

use yii\db\Migration;

/**
 * Class m220322_014931_migrate_odoo_pembayaranpiutang_t_pembayaran
 */
class m220322_014931_migrate_odoo_pembayaranpiutang_t_pembayaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
         $this->execute('DROP TRIGGER if exists "pembayaran_r_insert" ON "public"."pembayaranpiutang_t";');
         
         $this->execute('DROP FUNCTION if exists public.pembayaranpiutang_t_pembayaran;');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembayaranpiutang_t_pembayaran\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 

DECLARE 
    v_pembayaran_id int;
    v_pendaftaran_id int;
    v_pasienadmisi_id int;
    v_total_tagihan float8;
    v_total_dibayar float8;
    v_total_dijamin float8;
    v_total_sisatagihan float8;
    v_total_kembalian float8;
    v_total_administrasi float8;
    v_total_pembulatan float8;  
    v_total_pembebasan float8;  
    v_penggunaan_uangmuka float8;
    v_total_ditagihkan float8;
    v_total_discount float8;
    v_total_discountpembayaran float8;
    v_tipe_pembayaran int;
    

BEGIN

---------------------------------- SETUP META DATA TRANSAKSI --------------------------------------------------------   
        SELECT 
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
                        total_discount,
                        total_discountpembayaran
        INTO 
                        v_pembayaran_id,
                        v_pendaftaran_id,
                        v_pasienadmisi_id,
                        v_total_tagihan,
                        v_total_dibayar,
                        v_total_dijamin,
                        v_total_sisatagihan,
                        v_total_kembalian,
                        v_total_administrasi,
                        v_total_pembulatan ,
                        v_total_pembebasan ,
                        v_penggunaan_uangmuka,
                        v_total_ditagihkan,
                        v_total_discount,
                        v_total_discountpembayaran
        FROM 
          pembayaran_t 
        WHERE pemberianpiutang_id = NEW.pemberianpiutang_id;
                    
                SELECT
                    tipe_pembayaran
                INTO    
                    v_tipe_pembayaran
                FROM jenisnontunai_m
                    WHERE jenisnontunai_id = NEW.jenisnontunai_id;
                    

 --------------------------> insert pembulatan ke pembayaran_r <--------------------------------

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
    )VALUES(
        v_pembayaran_id,
        v_pendaftaran_id,
        v_pasienadmisi_id,
        v_total_tagihan,
        v_total_dibayar,
        v_total_dijamin,
        v_total_sisatagihan,
        v_total_kembalian ,
        v_total_administrasi ,
        v_total_pembulatan ,
        v_total_pembebasan ,
        v_penggunaan_uangmuka ,
        NEW.pemberianpiutang_id ,
        v_total_ditagihkan ,
        CASE
                    WHEN NEW.metode_pembayaran = 28 THEN NEW.total_bayarpiutang
                    ELSE 0
                END,
        CASE
                    WHEN NEW.metode_pembayaran = 27 THEN NEW.total_bayarpiutang
                ELSE 0
                END, 
        v_total_discount,
        v_total_discountpembayaran,
        NEW.catatan ,
        COALESCE(v_tipe_pembayaran,'682'),
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
        'RECEIPT'
   );


RETURN NEW;
END \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

     $this->execute('CREATE TRIGGER "pembayaran_r_insert" AFTER INSERT ON "public"."pembayaranpiutang_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pembayaranpiutang_t_pembayaran"();');

     $this->execute('COMMENT ON TRIGGER "pembayaran_r_insert" ON "public"."pembayaranpiutang_t" IS \'insert ke pembayaran_r\';');



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220322_014931_migrate_odoo_pembayaranpiutang_t_pembayaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220322_014931_migrate_odoo_pembayaranpiutang_t_pembayaran cannot be reverted.\n";

        return false;
    }
    */
}
