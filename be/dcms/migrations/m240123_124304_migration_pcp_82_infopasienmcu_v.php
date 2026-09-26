<?php

use yii\db\Migration;

/**
 * Class m240123_124304_migration_pcp_82_infopasienmcu_v
 */
class m240123_124304_migration_pcp_82_infopasienmcu_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienmcu_v");
        $infopasienmcu_v = file_get_contents(__DIR__ . '/definitions/infopasienmcu_v.sql');
        $this->execute($infopasienmcu_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_124304_migration_pcp_82_infopasienmcu_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_124304_migration_pcp_82_infopasienmcu_v cannot be reverted.\n";

        return false;
    }
    */
}
