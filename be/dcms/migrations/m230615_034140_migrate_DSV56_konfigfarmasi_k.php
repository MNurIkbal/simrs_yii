<?php

use yii\db\Migration;

/**
 * Class m230615_034140_migrate_DSV56_konfigfarmasi_k
 */
class m230615_034140_migrate_DSV56_konfigfarmasi_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE konfigfarmasi_k ADD IF NOT EXISTS use_discount BOOLEAN DEFAULT FALSE;");
        $this->execute("ALTER TABLE konfigfarmasi_k ADD IF NOT EXISTS use_ppn BOOLEAN DEFAULT FALSE;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230615_034140_migrate_DSV56_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230615_034140_migrate_DSV56_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }
    */
}
