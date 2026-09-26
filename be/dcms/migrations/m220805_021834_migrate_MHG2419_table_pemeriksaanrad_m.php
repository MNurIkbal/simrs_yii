<?php

use yii\db\Migration;

/**
 * Class m220805_021834_migrate_MHG2419_table_pemeriksaanrad_m
 */
class m220805_021834_migrate_MHG2419_table_pemeriksaanrad_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."pemeriksaanrad_m" ADD IF NOT EXISTS "is_contrast" BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220805_021834_migrate_MHG2419_table_pemeriksaanrad_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220805_021834_migrate_MHG2419_table_pemeriksaanrad_m cannot be reverted.\n";

        return false;
    }
    */
}
