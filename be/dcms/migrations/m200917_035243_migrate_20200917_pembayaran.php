<?php

use yii\db\Migration;

/**
 * Class m200917_035243_migrate_20200917_pembayaran
 */
class m200917_035243_migrate_20200917_pembayaran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pembayaran_t" ADD IF NOT EXISTS "sisa_uangmuka" float8 DEFAULT 0;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200917_035243_migrate_20200917_pembayaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200917_035243_migrate_20200917_pembayaran cannot be reverted.\n";

        return false;
    }
    */
}
