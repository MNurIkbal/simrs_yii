<?php

use yii\db\Migration;

/**
 * Class m220418_041954_migrate_ODH396_table_pembayaran_t
 */
class m220318_073430_migrate_ODH396_table_pembayaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."pembayaran_t" ADD COLUMN IF NOT EXISTS "no_invoicepasien" varchar(255);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220318_073430_migrate_ODH396_table_pembayaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_073430_migrate_ODH396_table_pembayaran_t cannot be reverted.\n";

        return false;
    }
    */
}
