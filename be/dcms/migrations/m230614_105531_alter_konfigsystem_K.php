<?php

use yii\db\Migration;

/**
 * Class m230614_105531_alter_konfigsystem_K
 */
class m230614_105531_alter_konfigsystem_K extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigsystem_k ADD IF NOT EXISTS is_approve_lab_new_order bool NOT NULL DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230614_105531_alter_konfigsystem_K cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230614_105531_alter_konfigsystem_K cannot be reverted.\n";

        return false;
    }
    */
}
