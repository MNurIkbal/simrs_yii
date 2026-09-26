<?php

use yii\db\Migration;

/**
 * Class m220408_084450_migrate_multypayer_function_tandabuktibayar_t_insert
 */
class m220408_084450_migrate_multypayer_function_tandabuktibayar_t_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tandabuktibayar_t_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ 
                
            DECLARE  


            BEGIN            
                     INSERT INTO tandabuktibayar_t 
                        (       
                                        ruangan_id, 
                                        tglbuktibayar,
                                        darinama_bkm,
                                        sebagaipembayaran_bkm,
                                        jmlpembayaran,
                                        biayaadministrasi,
                                        uangditerima,
                                        uangkembalian,
                                        namapemilik_rek,
                                        no_rek,
                                        carapembayaran,
                                        pegawai1_id,
                                        created_by,
                                        shift_id,
                                        pembayaran_id
                        )
                        VALUES 
                        (
                                        ((NEW.additional_data::json->>\'pembayaran_pelayanan\')::json->0->\'ruangan_id\')::text::int,
                                        NEW.created_date,
                                        (((NEW.additional_data::json->>\'pembayaran_pelayanan\')::json->0->\'additional_data\')::json->>\'darinama_bkm\')::text,
                                        (((NEW.additional_data::json->>\'pembayaran_pelayanan\')::json->0->\'additional_data\')::json->>\'sebagaipembayaran_bkm\')::text,
                                        NEW.total_tagihan,
                                        NEW.total_administrasi,
                                        NEW.total_dibayar,
                                        NEW.total_kembalian,
                                        ((NEW.additional_data::json->>\'pembayaran_pelayanan\')::json->0->\'nama_pemrekening\')::text,
                                        ((NEW.additional_data::json->>\'pembayaran_pelayanan\')::json->0->\'no_rekening\')::text,
                                        (((NEW.additional_data::json->>\'pembayaran_pelayanan\')::json->0->\'additional_data\')::json->>\'carapembayaran\')::text,
                                        (((NEW.additional_data::json->>\'pembayaran_pelayanan\')::json->0->\'additional_data\')::json->>\'pegawai_id\')::text::int,
                                        NEW.created_by,
                                        (((NEW.additional_data::json->>\'pembayaran_pelayanan\')::json->0->\'additional_data\')::json->>\'shift_id\')::text::int,
                                        NEW.pembayaran_id

                        );


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
        echo "m220408_084450_migrate_multypayer_function_tandabuktibayar_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_084450_migrate_multypayer_function_tandabuktibayar_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
