<?php

use yii\db\Migration;

/**
 * Class m240123_115123_migration_pcp_82_infotagihanpasienpulang_v
 */
class m240123_115123_migration_pcp_82_infotagihanpasienpulang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infotagihanpasienpulang_v");
        $infotagihanpasienpulang_v = file_get_contents(__DIR__ . '/definitions/infotagihanpasienpulang_v.sql');
        $this->execute($infotagihanpasienpulang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_115123_migration_pcp_82_infotagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_115123_migration_pcp_82_infotagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }
    */
}
