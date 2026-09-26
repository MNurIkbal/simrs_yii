<?php

use yii\db\Migration;

/**
 * Class m210906_073254_migrate_US1336_konfigsystem_k
 */
class m210906_073254_migrate_US1336_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
          ADD COLUMN IF NOT EXISTS "is_hide_ruangan" bool DEFAULT false;
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210906_073254_migrate_US1336_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210906_073254_migrate_US1336_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
