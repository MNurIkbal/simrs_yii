<?php

use yii\db\Migration;

/**
 * Class m220408_065038_migrate_multypayer_table_pembayaran_t
 */
class m220408_065038_migrate_multypayer_table_pembayaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."pembayaran_t" ADD COLUMN IF NOT EXISTS "no_pembayaran" varchar(255) ;
        ');

        $this->execute('
            ALTER TABLE "public"."pembayaran_t" ADD COLUMN IF NOT EXISTS "no_invoicepasien" varchar(255);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220408_065038_migrate_multypayer_table_pembayaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_065038_migrate_schema_multypayer_table_pembayaran_t cannot be reverted.\n";

        return false;
    }
    */
}
