<?php

use yii\db\Migration;

/**
 * Class m231030_101119_migrate_DSV742_lookuptransaksi_m
 */
class m231030_101119_migrate_DSV742_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        - $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'instalasi_bedah';");
        - $this->execute("INSERT INTO public.lookuptransaksi_m
        (kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan)
        VALUES('instalasi_bedah', 12, 'instalasi bedah untuk exception qty bmhp bedah (retur pendaftaran)', NULL, NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231030_101119_migrate_DSV742_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231030_101119_migrate_DSV742_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
