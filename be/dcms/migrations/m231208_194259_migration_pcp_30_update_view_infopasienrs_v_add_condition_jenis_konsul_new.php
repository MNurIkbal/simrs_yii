<?php

use yii\db\Migration;

/**
 * Class m231208_194259_migration_pcp_30_update_view_infopasienrs_v_add_condition_jenis_konsul_new
 */
class m231208_194259_migration_pcp_30_update_view_infopasienrs_v_add_condition_jenis_konsul_new extends Migration
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
        echo "m231208_194259_migration_pcp_30_update_view_infopasienrs_v_add_condition_jenis_konsul_new cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231208_194259_migration_pcp_30_update_view_infopasienrs_v_add_condition_jenis_konsul_new cannot be reverted.\n";

        return false;
    }
    */
}
