<?php

use yii\db\Migration;

/**
 * Class m230818_120823_migrate_DSV_410_lookuptransaksi_m
 */
class m230818_120823_migrate_DSV_410_lookuptransaksi_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookuptransaksi_m WHERE kode_transaksi = 'kronis_limit';");

        $this->execute("INSERT INTO public.lookuptransaksi_m (kode_transaksi, kode_id, kode_fungsi) VALUES('kronis_limit', 30, 'untuk limit resep kronis');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230818_120823_migrate_DSV_410_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230818_120823_migrate_DSV_410_lookuptransaksi_m cannot be reverted.\n";

        return false;
    }
    */
}
