<?php

use yii\db\Migration;

/**
 * Class m230822_062527_alter_konfigsystem_k_default_input_bmhp_bedah
 */
class m230822_062527_alter_konfigsystem_k_default_input_bmhp_bedah extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE konfigsystem_k ADD IF NOT EXISTS default_bmhpbedah_ditagihkan bool NULL DEFAULT false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230822_062527_alter_konfigsystem_k_default_input_bmhp_bedah cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_062527_alter_konfigsystem_k_default_input_bmhp_bedah cannot be reverted.\n";

        return false;
    }
    */
}
