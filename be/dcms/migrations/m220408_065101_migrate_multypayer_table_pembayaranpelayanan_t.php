<?php

use yii\db\Migration;

/**
 * Class m220408_065101_migrate_multypayer_table_pembayaranpelayanan_t
 */
class m220408_065101_migrate_multypayer_table_pembayaranpelayanan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."pembayaranpelayanan_t" ADD COLUMN IF NOT EXISTS "is_penjaminutama" bool DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_065101_migrate_multypayer_table_pembayaranpelayanan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_065101_migrate_multypayer_table_pembayaranpelayanan_t cannot be reverted.\n";

        return false;
    }
    */
}
