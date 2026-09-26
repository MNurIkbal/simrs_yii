<?php

use yii\db\Migration;

/**
 * Class m250820_101734_RPP1966CreateBarangHistoryR
 */
class m250820_101734_RPP1966CreateBarangHistoryR extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS baranghistory_v');
        $this->execute('DROP TABLE IF EXISTS baranghistory_r');

        $this->execute('CREATE TABLE public.baranghistory_r (
            baranghistory_id serial4 NOT NULL,
            tgl_baranghistory timestamp(0) NULL,
            barang_id int4 NOT NULL,
            barang_nama varchar(255) NULL,
            harga_dasar float8 NULL,
            keterangan int2 NULL,
            last_modified_by int4 NULL,
            catatan text NULL,
            CONSTRAINT baranghistory_r_key PRIMARY KEY (baranghistory_id)
        )');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250820_101734_RPP1966CreateBarangHistoryR cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250820_101734_RPP1966CreateBarangHistoryR cannot be reverted.\n";

        return false;
    }
    */
}
