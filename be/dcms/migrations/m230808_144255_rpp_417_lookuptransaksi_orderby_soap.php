<?php

use yii\db\Migration;

/**
 * Class m230808_144255_rpp_417_lookuptransaksi_orderby_soap
 */
class m230808_144255_rpp_417_lookuptransaksi_orderby_soap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'orderby_soap';");

        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('orderby_soap', 1, 'Urutan default saat SOAP/CPPT dibuka. Value yg dapat diisi asc atau desc', 'desc');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230808_144255_rpp_417_lookuptransaksi_orderby_soap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230808_144255_rpp_417_lookuptransaksi_orderby_soap cannot be reverted.\n";

        return false;
    }
    */
}
