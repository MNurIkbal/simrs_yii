<?php

use yii\db\Migration;

/**
 * Class m201221_101445_migrate_20201221_fn_pesanbarangdetail
 */
class m201221_101445_migrate_20201221_fn_pesanbarangdetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE OR REPLACE FUNCTION "public"."pesanbarangdetail_t_insert"()
  RETURNS "pg_catalog"."trigger" AS $BODY$
DECLARE
vpesanbarangdetail_id int4;
vpesanbarang_id int4;
vbarang_id int4;
vruangan_id int4;
vqty_pesan int4;
vcreated_by int4;

BEGIN
    SELECT 
        pesanbarangdetail_id ,
        pesanbarang_id,
        barang_id,
        qty_pesan,
        created_by
    INTO
        vpesanbarangdetail_id,
        vpesanbarang_id,
        vbarang_id,
        vqty_pesan,
        vcreated_by
    FROM 
        pesanbarangdetail_t 
    ORDER BY    
        pesanbarangdetail_id 
    DESC LIMIT 1;
    
    SELECT 
        pesanbarang_t.ruangantujuan_id
    INTO
        vruangan_id
    FROM
        pesanbarang_t
    WHERE pesanbarang_t.pesanbarang_id = vpesanbarang_id;
    
    
    IF EXISTS(
        SELECT *
        FROM stokbarang_r
        WHERE barang_id = vbarang_id
        AND ruangan_id = vruangan_id
        LIMIT 1
    )
    THEN
        UPDATE stokbarang_r
        SET 
            qty_dipesan = qty_dipesan + vqty_pesan,
            qty_tersedia = qty_tersedia - vqty_pesan,
            last_modified_date = CURRENT_TIMESTAMP, 
            last_modified_by = vcreated_by
        WHERE barang_id = vbarang_id
        AND ruangan_id = vruangan_id;
    ELSE
        INSERT INTO stokbarang_r (
            ruangan_id, barang_id, qty_awal, qty_masuk, qty_keluar, qty_sisa, qty_tersedia,
            qty_dipesan, created_date, created_by, is_deleted, is_active
        )VALUES(
            vruangan_id, vbarang_id, 0, 0, 0, 0, 0, 
            vqty_pesan, CURRENT_TIMESTAMP, vcreated_by, \'f\', \'t\'
        );
    END IF;

    RETURN NEW;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');
   

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201221_101445_migrate_20201221_fn_pesanbarangdetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201221_101445_migrate_20201221_fn_pesanbarangdetail cannot be reverted.\n";

        return false;
    }
    */
}
