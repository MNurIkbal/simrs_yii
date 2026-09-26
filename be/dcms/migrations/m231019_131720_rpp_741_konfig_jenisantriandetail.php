<?php

use yii\db\Migration;

/**
 * Class m231019_131720_rpp_741_konfig_jenisantriandetail
 */
class m231019_131720_rpp_741_konfig_jenisantriandetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m where kode_transaksi = 'konfig_antrian_using_jenisantriandetail' ");
        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi) VALUES
        ('konfig_antrian_using_jenisantriandetail',1,'Konfig untuk Master Display Antrian - auto mapping jenisantriandetail');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231019_131720_rpp_741_konfig_jenisantriandetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231019_131720_rpp_741_konfig_jenisantriandetail cannot be reverted.\n";

        return false;
    }
    */
}
