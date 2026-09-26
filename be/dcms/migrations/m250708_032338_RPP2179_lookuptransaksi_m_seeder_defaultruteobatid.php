<?php

use yii\db\Migration;

/**
 * Class m250708_032338_RPP2179_lookuptransaksi_m_seeder_defaultruteobatid
 */
class m250708_032338_RPP2179_lookuptransaksi_m_seeder_defaultruteobatid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE FROM lookuptransaksi_m
            WHERE kode_transaksi = 'default_ruteobat_id_is_oral';
        ");

        $this->execute("
            INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi,additional_value,kode_nama,kode_singkatan) VALUES
            ('default_ruteobat_id_is_oral',1,'default ruteobat_id untuk jenis is_oral yang digunakan untuk default select. value kode_id = ruteobat_id',NULL,NULL,NULL);
        ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250708_032338_RPP2179_lookuptransaksi_m_seeder_defaultruteobatid cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250708_032338_RPP2179_lookuptransaksi_m_seeder_defaultruteobatid cannot be reverted.\n";

        return false;
    }
    */
}
