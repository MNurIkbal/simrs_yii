<?php

use yii\db\Migration;

/**
 * Class m240123_123930_migration_pcp_82_infokunjunganrd_v
 */
class m240123_123930_migration_pcp_82_infokunjunganrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infokunjunganrd_v");
        $infokunjunganrd_v = file_get_contents(__DIR__ . '/definitions/infokunjunganrd_v.sql');
        $this->execute($infokunjunganrd_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_123930_migration_pcp_82_infokunjunganrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_123930_migration_pcp_82_infokunjunganrd_v cannot be reverted.\n";

        return false;
    }
    */
}
