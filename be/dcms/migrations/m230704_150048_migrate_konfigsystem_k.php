<?php

use yii\db\Migration;

/**
 * Class m230704_150048_migrate_konfigsystem_k
 */
class m230704_150048_migrate_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigsystem_k ADD IF NOT EXISTS lepas_validasi_jadwal_operasi bool NOT NULL DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230704_150048_migrate_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230704_150048_migrate_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
