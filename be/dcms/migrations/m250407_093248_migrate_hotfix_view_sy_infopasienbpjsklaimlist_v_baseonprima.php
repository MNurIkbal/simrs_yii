<?php

use yii\db\Migration;

/**
 * Class m250407_093248_migrate_hotfix_view_sy_infopasienbpjsklaimlist_v_baseonprima
 */
class m250407_093248_migrate_hotfix_view_sy_infopasienbpjsklaimlist_v_baseonprima extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_infopasienbpjsklaimlist_v");
        $sy_infopasienbpjsklaimlist_v= file_get_contents(__DIR__ . '/definitions/sy_infopasienbpjsklaimlist_v.sql');
        $this->execute($sy_infopasienbpjsklaimlist_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250407_093248_migrate_hotfix_view_sy_infopasienbpjsklaimlist_v_baseonprima cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250407_093248_migrate_hotfix_view_sy_infopasienbpjsklaimlist_v_baseonprima cannot be reverted.\n";

        return false;
    }
    */
}
