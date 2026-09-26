<?php

use yii\db\Migration;

/**
 * Class m211229_030435_migrate_US2346_konfigsystemkapital
 */
class m211229_030435_migrate_US2346_konfigsystemkapital extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
          ADD COLUMN IF NOT EXISTS "is_kapital" bool DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211229_030435_migrate_US2346_konfigsystemkapital cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211229_030435_migrate_US2346_konfigsystemkapital cannot be reverted.\n";

        return false;
    }
    */
}
