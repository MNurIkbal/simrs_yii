<?php

use yii\db\Migration;

/**
 * Class m230918_094706_alter_table_sy_kunjungan
 */
class m230918_094706_alter_table_sy_kunjungan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE public.sy_kunjungan 
            ADD IF NOT EXISTS no_pembayaran varchar(100) NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230918_094706_alter_table_sy_kunjungan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230918_094706_alter_table_sy_kunjungan cannot be reverted.\n";

        return false;
    }
    */
}
