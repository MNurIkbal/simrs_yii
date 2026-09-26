<?php

use yii\db\Migration;

/**
 * Class m240104_083139_migrate_dsv_1036_lookuptransaksi_m
 */
class m240104_083139_migrate_dsv_1036_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'validasi_button_unduh';");
        $this->execute("
            INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES('validasi_button_unduh', 1, 'Validasi butiuh unduh dokumen.', '[550,551,556,549]', NULL, NULL);
        ");
    }



    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240104_083139_migrate_dsv_1036_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240104_083139_migrate_dsv_1036_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
