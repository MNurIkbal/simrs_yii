<?php

use yii\db\Migration;

/**
 * Class m240123_124951_migration_pcp_82_logactivitypenatajasa_v
 */
class m240123_124951_migration_pcp_82_logactivitypenatajasa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS logactivitypenatajasa_v");
        $logactivitypenatajasa_v = file_get_contents(__DIR__ . '/definitions/logactivitypenatajasa_v.sql');
        $this->execute($logactivitypenatajasa_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_124951_migration_pcp_82_logactivitypenatajasa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_124951_migration_pcp_82_logactivitypenatajasa_v cannot be reverted.\n";

        return false;
    }
    */
}
