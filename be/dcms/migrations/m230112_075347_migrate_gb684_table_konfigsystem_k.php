<?php

use yii\db\Migration;

/**
 * Class m230112_075347_migrate_gb684_table_konfigsystem_k
 */
class m230112_075347_migrate_gb684_table_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        ALTER TABLE konfigsystem_k ADD IF NOT EXISTS is_set_plafon BOOLEAN DEFAULT TRUE;
    ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230112_075347_migrate_gb684_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230112_075347_migrate_gb684_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
