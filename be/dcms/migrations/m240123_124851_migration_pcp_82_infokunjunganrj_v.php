<?php

use yii\db\Migration;

/**
 * Class m240123_124851_migration_pcp_82_infokunjunganrj_v
 */
class m240123_124851_migration_pcp_82_infokunjunganrj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infokunjunganrj_v");
        $infokunjunganrj_v = file_get_contents(__DIR__ . '/definitions/infokunjunganrj_v.sql');
        $this->execute($infokunjunganrj_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_124851_migration_pcp_82_infokunjunganrj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_124851_migration_pcp_82_infokunjunganrj_v cannot be reverted.\n";

        return false;
    }
    */
}
