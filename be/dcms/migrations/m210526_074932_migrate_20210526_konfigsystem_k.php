<?php

use yii\db\Migration;

/**
 * Class m210526_074932_migrate_20210526_konfigsystem_k
 */
class m210526_074932_migrate_20210526_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
  ADD COLUMN if not exists "support_multipayer" bool DEFAULT false;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210526_074932_migrate_20210526_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210526_074932_migrate_20210526_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
