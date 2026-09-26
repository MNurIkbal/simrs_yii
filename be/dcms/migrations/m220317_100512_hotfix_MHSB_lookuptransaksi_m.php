<?php

use yii\db\Migration;

/**
 * Class m220317_100512_hotfix_MHSB_lookuptransaksi_m
 */
class m220317_100512_hotfix_MHSB_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'pendaftaran_penunjang';");
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'instalasi_ruangan';");
        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('pendaftaran_penunjang', 2, 'Pendaftaran Penunjang', '[4,5,7,12,21]');");
        $this->execute("INSERT INTO lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value) VALUES ('instalasi_ruangan', 9, 'Kode Instalasi Kasir', 'kasir');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220317_100512_hotfix_MHSB_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220317_100512_hotfix_MHSB_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
