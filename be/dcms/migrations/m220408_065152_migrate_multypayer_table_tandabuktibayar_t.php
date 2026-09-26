<?php

use yii\db\Migration;

/**
 * Class m220408_065152_migrate_multypayer_table_tandabuktibayar_t
 */
class m220408_065152_migrate_multypayer_table_tandabuktibayar_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."tandabuktibayar_t" ADD COLUMN IF NOT EXISTS "pembayaran_id" int4;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_065152_migrate_multypayer_table_tandabuktibayar_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_065152_migrate_multypayer_table_tandabuktibayar_t cannot be reverted.\n";

        return false;
    }
    */
}
