<?php

use yii\db\Migration;

/**
 * Class m220413_033526_migrate_multypayer_function_pembatalanpembayaran_t_insert
 */
class m220413_033526_migrate_multypayer_function_pembatalanpembayaran_t_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."pembatalanpembayaran_t_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                
                    
            BEGIN
            -- INSERT table pembatalanpembayaran_t untuk batal bayar --
                IF(NEW.is_deleted = TRUE)
                    THEN
                    INSERT INTO pembatalanpembayaran_t (      
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
                                    additional_data,
                                    created_by,
                                    penggunaan_uangmuka,
                                    pemberianpiutang_id,
                                    total_ditagihkan,
                                    total_tunai,
                                    total_nontunai,
                                    total_discount,
                                    total_discountpembayaran,
                                    catatan,
                                    sisa_uangmuka,
                                    no_pembayaran,
                                    no_invoicepasien,
                                    pembulatan,
                                    total_discountadm,
                                    alasan_batal
                    )VALUES(
                                    OLD.pembayaran_id,
                                    OLD.pendaftaran_id,
                                    OLD.pasienadmisi_id,
                                    OLD.total_tagihan,
                                    OLD.total_dibayar,
                                    OLD.total_dijamin,
                                    OLD.total_sisatagihan,
                                    OLD.total_kembalian,
                                    OLD.total_administrasi,
                                    OLD.total_pembulatan,
                                    OLD.total_pembebasan,
                                    OLD.additional_data,
                                    NEW.deleted_by,
                                    OLD.penggunaan_uangmuka,
                                    OLD.pemberianpiutang_id,
                                    OLD.total_ditagihkan,
                                    OLD.total_tunai,
                                    OLD.total_nontunai,
                                    OLD.total_discount,
                                    OLD.total_discountpembayaran,
                                    OLD.catatan,
                                    OLD.sisa_uangmuka,
                                    OLD.no_pembayaran,
                                    OLD.no_invoicepasien,
                                    OLD.pembulatan,
                                    OLD.total_discountadm,
                                    OLD.alasan_batal
                    );
            END IF;


                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100
        ');


        $this->execute('
            DROP TRIGGER if exists "pembatalanpembayaran_t_insert" ON "public"."pembayaran_t";
        ');

        $this->execute('
            CREATE TRIGGER "pembatalanpembayaran_t_insert" BEFORE INSERT ON "public"."pembayaran_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."pembatalanpembayaran_t_insert"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220413_033526_migrate_multypayer_function_pembatalanpembayaran_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220413_033526_migrate_multypayer_function_pembatalanpembayaran_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
