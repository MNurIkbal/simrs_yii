<?php

use yii\db\Migration;

/**
 * Class m210129_093304_migrate_20210129_konfigfarmasi_k
 */
class m210129_093304_migrate_20210129_konfigfarmasi_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN IF NOT EXISTS "is_marginkhusus" bool DEFAULT false;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210129_093304_migrate_20210129_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210129_093304_migrate_20210129_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }
    */
}
