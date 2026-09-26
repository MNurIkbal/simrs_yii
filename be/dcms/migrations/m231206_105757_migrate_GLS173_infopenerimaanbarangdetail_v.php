<?php

use yii\db\Migration;

/**
 * Class m231206_105757_migrate_GLS173_infopenerimaanbarangdetail_v
 */
class m231206_105757_migrate_GLS173_infopenerimaanbarangdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopenerimaanbarangdetail_v");
        $infopenerimaanbarangdetail_v = file_get_contents(__DIR__ . '/definitions/infopenerimaanbarangdetail_v.sql');
        $this->execute($infopenerimaanbarangdetail_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231206_105757_migrate_GLS173_infopenerimaanbarangdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231206_105757_migrate_GLS173_infopenerimaanbarangdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
