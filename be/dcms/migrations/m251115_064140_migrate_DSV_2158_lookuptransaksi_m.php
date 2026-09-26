<?php

use yii\db\Migration;

/**
 * Class m251115_064140_migrate_DSV_2158_lookuptransaksi_m
 */
class m251115_064140_migrate_DSV_2158_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO \"public\".\"lookuptransaksi_m\" (\"kode_transaksi\", \"kode_id\", \"kode_fungsi\", \"additional_value\", \"kode_nama\", \"kode_singkatan\") VALUES ('key_enkripsi', 0, 'key enkripsi dipakai untuk enkripsi return response BE', 'test123123', NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251115_064140_migrate_DSV_2158_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251115_064140_migrate_DSV_2158_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
