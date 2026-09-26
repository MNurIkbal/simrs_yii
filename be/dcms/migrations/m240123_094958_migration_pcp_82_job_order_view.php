<?php

use yii\db\Migration;

/**
 * Class m240123_094958_migration_pcp_82_job_order_view
 */
class m240123_094958_migration_pcp_82_job_order_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS notifikasijoborder_v");
        $notifikasijoborder_v = file_get_contents(__DIR__ . '/definitions/notifikasijoborder_v.sql');
        $this->execute($notifikasijoborder_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_094958_migration_pcp_82_job_order_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_094958_migration_pcp_82_job_order_view cannot be reverted.\n";

        return false;
    }
    */
}
