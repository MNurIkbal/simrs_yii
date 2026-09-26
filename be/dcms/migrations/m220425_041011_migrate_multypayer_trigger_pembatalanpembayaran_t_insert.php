<?php

use yii\db\Migration;

/**
 * Class m220425_041011_migrate_multypayer_trigger_pembatalanpembayaran_t_insert
 */
class m220425_041011_migrate_multypayer_trigger_pembatalanpembayaran_t_insert extends Migration
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
                IF NOT EXISTS (
                    SELECT 1
                    FROM pembatalanpembayaran_t
                    WHERE pembayaran_id = OLD.pembayaran_id
                )
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
        echo "m220425_041011_migrate_multypayer_trigger_pembatalanpembayaran_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220425_041011_migrate_multypayer_trigger_pembatalanpembayaran_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
