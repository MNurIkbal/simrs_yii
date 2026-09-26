<?php

use yii\db\Migration;

/**
 * Class m230717_043619_migrate_table_konfigsystem_k
 */
class m230717_043619_migrate_table_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigsystem_k ADD IF NOT EXISTS hide_instruksi_soap BOOLEAN DEFAULT TRUE;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230717_043619_migrate_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230717_043619_migrate_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
