<?php

use yii\db\Migration;

/**
 * Class m231117_103002_alter_konfiguduhdokumen_k
 */
class m231117_103002_alter_konfiguduhdokumen_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            ALTER TABLE konfigunduhdokumen_k 
            ADD IF NOT EXISTS base_url varchar NULL;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231117_103002_alter_konfiguduhdokumen_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231117_103002_alter_konfiguduhdokumen_k cannot be reverted.\n";

        return false;
    }
    */
}
