<?php

use yii\db\Migration;

/**
 * Class m231005_094840_limit_eklaim_lookuptransaksi
 */
class m231005_094840_limit_eklaim_lookuptransaksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        DELETE FROM lookuptransaksi_m where kode_transaksi = 'limit_cron_global_eklaim'
        ");
        $this->execute("
        INSERT INTO lookuptransaksi_m (kode_transaksi,kode_id,kode_fungsi)
            VALUES ('limit_cron_global_eklaim',50,'Limit data untuk cron global eklaim')
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231005_094840_limit_eklaim_lookuptransaksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231005_094840_limit_eklaim_lookuptransaksi cannot be reverted.\n";

        return false;
    }
    */
}
