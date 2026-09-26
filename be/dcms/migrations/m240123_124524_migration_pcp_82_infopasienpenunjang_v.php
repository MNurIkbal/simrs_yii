<?php

use yii\db\Migration;

/**
 * Class m240123_124524_migration_pcp_82_infopasienpenunjang_v
 */
class m240123_124524_migration_pcp_82_infopasienpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienpenunjang_v");
        $infopasienpenunjang_v = file_get_contents(__DIR__ . '/definitions/infopasienpenunjang_v.sql');
        $this->execute($infopasienpenunjang_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_124524_migration_pcp_82_infopasienpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_124524_migration_pcp_82_infopasienpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
