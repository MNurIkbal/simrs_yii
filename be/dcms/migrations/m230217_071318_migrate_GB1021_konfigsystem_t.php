<?php

use yii\db\Migration;

/**
 * Class m230217_071318_migrate_GB1021_konfigsystem_t
 */
class m230217_071318_migrate_GB1021_konfigsystem_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN IF NOT EXISTS "is_allow_create_pasien_jkn" bool NULL DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230217_071318_migrate_GB1021_konfigsystem_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230217_071318_migrate_GB1021_konfigsystem_t cannot be reverted.\n";

        return false;
    }
    */
}
