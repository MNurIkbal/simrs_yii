<?php

use yii\db\Migration;

/**
 * Class m220107_064231_migrate_konfigsystem_k
 */
class m220107_064231_migrate_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN if not exists "is_expertise_desc" bool DEFAULT true;');

        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN if not exists "is_expertise_kesan" bool DEFAULT true;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220107_064231_migrate_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220107_064231_migrate_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
