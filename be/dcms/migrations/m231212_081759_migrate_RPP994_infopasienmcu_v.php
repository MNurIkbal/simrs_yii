<?php

use yii\db\Migration;

/**
 * Class m231212_081759_migrate_RPP994_infopasienmcu_v
 */
class m231212_081759_migrate_RPP994_infopasienmcu_v extends Migration
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
        echo "m231212_081759_migrate_RPP994_infopasienmcu_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231212_081759_migrate_RPP994_infopasienmcu_v cannot be reverted.\n";

        return false;
    }
    */
}
