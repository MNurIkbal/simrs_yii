<?php

use yii\db\Migration;

/**
 * Class m230224_034053_migrate_GB1028_konfigsystem_k
 */
class m230224_034053_migrate_GB1028_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN IF NOT EXISTS "reservasi_akhir_jkn" int2 NULL DEFAULT 7;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230224_034053_migrate_GB1028_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230224_034053_migrate_GB1028_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
