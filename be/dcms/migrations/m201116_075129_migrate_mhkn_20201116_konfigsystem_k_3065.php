<?php

use yii\db\Migration;

/**
 * Class m201116_075129_migrate_mhkn_20201116_konfigsystem_k_3065
 */
class m201116_075129_migrate_mhkn_20201116_konfigsystem_k_3065 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
  ADD IF NOT EXISTS "is_nourut" bool NOT NULL DEFAULT false');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201116_075129_migrate_mhkn_20201116_konfigsystem_k_3065 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201116_075129_migrate_mhkn_20201116_konfigsystem_k_3065 cannot be reverted.\n";

        return false;
    }
    */
}
