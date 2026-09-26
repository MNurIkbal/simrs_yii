<?php

use yii\db\Migration;

/**
 * Class m210114_071639_migrate_20210114_konfigsystem_k
 */
class m210114_071639_migrate_20210114_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
  ADD COLUMN IF NOT EXISTS "is_hide_alias" bool DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210114_071639_migrate_20210114_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210114_071639_migrate_20210114_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
