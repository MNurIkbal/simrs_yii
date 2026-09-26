<?php

use yii\db\Migration;

/**
 * Class m230829_031844_alter_konfigsystem_add_keterangan_jkn
 */
class m230829_031844_alter_konfigsystem_add_keterangan_jkn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE konfigsystem_k ADD IF NOT EXISTS keterangan_jkn text null");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230829_031844_alter_konfigsystem_add_keterangan_jkn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230829_031844_alter_konfigsystem_add_keterangan_jkn cannot be reverted.\n";

        return false;
    }
    */
}
