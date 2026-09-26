<?php

use yii\db\Migration;

/**
 * Class m230305_025146_migrate_GB1083_table_konfigsystem_k
 */
class m230305_025146_migrate_GB1083_table_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigsystem_k ADD IF NOT EXISTS is_merge_transaction BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230305_025146_migrate_GB1083_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230305_025146_migrate_GB1083_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
