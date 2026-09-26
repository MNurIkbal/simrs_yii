<?php

use yii\db\Migration;

/**
 * Class m230201_092651_migrate_GA115_konfigsystem_t
 */
class m230201_092651_migrate_GA115_konfigsystem_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN IF NOT EXISTS "is_hide_cppt_kosong" bool NULL DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230201_092651_migrate_GA115_konfigsystem_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230201_092651_migrate_GA115_konfigsystem_t cannot be reverted.\n";

        return false;
    }
    */
}
