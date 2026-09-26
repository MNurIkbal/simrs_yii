<?php

use yii\db\Migration;

/**
 * Class m231127_154032_migrate_sy_infopasienbpjsdiagnosa_v
 */
class m231127_154032_migrate_sy_infopasienbpjsdiagnosa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS sy_infopasienbpjsdiagnosa_v");
        $sy_infopasienbpjsdiagnosa_v = file_get_contents(__DIR__ . '/definitions/sy_infopasienbpjsdiagnosa_v.sql');
        $this->execute($sy_infopasienbpjsdiagnosa_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231127_154032_migrate_sy_infopasienbpjsdiagnosa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231127_154032_migrate_sy_infopasienbpjsdiagnosa_v cannot be reverted.\n";

        return false;
    }
    */
}
