<?php

use yii\db\Migration;

/**
 * Class m240123_122527_migration_pcp_82_infopasienrs_v
 */
class m240123_122527_migration_pcp_82_infopasienrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopasienrs_v");
        $infopasienrs_v = file_get_contents(__DIR__ . '/definitions/infopasienrs_v.sql');
        $this->execute($infopasienrs_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240123_122527_migration_pcp_82_infopasienrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240123_122527_migration_pcp_82_infopasienrs_v cannot be reverted.\n";

        return false;
    }
    */
}
