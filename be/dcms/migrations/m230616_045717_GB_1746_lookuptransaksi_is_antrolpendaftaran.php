<?php

use yii\db\Migration;

/**
 * Class m230616_045717_GB_1746_lookuptransaksi_is_antrolpendaftaran
 */
class m230616_045717_GB_1746_lookuptransaksi_is_antrolpendaftaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m where kode_transaksi = 'is_antrolpendaftaran' ");

        $this->execute("
        INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi)
        VALUES ('is_antrolpendaftaran',0,'Konfigurasi Antrol di Pendaftaran Rawat Jalan (aktif/tidak)')
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230616_045717_GB_1746_lookuptransaksi_is_antrolpendaftaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230616_045717_GB_1746_lookuptransaksi_is_antrolpendaftaran cannot be reverted.\n";

        return false;
    }
    */
}
