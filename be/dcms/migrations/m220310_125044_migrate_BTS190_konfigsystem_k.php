<?php

use yii\db\Migration;

/**
 * Class m220310_125044_migrate_BTS190_konfigsystem_k
 */
class m220310_125044_migrate_BTS190_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
          ADD COLUMN IF NOT EXISTS "is_support_jkn" bool DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220310_125044_migrate_BTS190_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220310_125044_migrate_BTS190_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
