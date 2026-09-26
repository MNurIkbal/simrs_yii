<?php

use yii\db\Migration;

/**
 * Class m230628_070042_migrate_DSV61_konfigsystem_k
 */
class m230628_070042_migrate_DSV61_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE konfigsystem_k ADD IF NOT EXISTS is_print_automatic BOOLEAN DEFAULT FALSE;");
        $this->execute("ALTER TABLE konfigsystem_k ADD IF NOT EXISTS auto_print_port VARCHAR NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230628_070042_migrate_DSV61_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230628_070042_migrate_DSV61_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
