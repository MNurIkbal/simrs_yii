<?php

use yii\db\Migration;

/**
 * Class m230131_105930_migrate_gm33_table_konfigsystem_k
 */
class m230131_105930_migrate_gm33_table_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        ALTER TABLE konfigsystem_k ADD IF NOT EXISTS konfig_kelompok_tindakan TEXT
    ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230131_105930_migrate_gm33_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230131_105930_migrate_gm33_table_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
