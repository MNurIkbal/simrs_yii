<?php

use yii\db\Migration;

/**
 * Class m250107_090007_migrate_dsv_1617_dashboardintegrasi_v
 */
class m250107_090007_migrate_dsv_1617_dashboardintegrasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS dashboardintegrasiasuransi_v");
        $dashboardintegrasiasuransi_v = file_get_contents(__DIR__ . '/definitions/dashboardintegrasiasuransi_v.sql');
        $this->execute($dashboardintegrasiasuransi_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250107_090007_migrate_dsv_1617_dashboardintegrasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250107_090007_migrate_dsv_1617_dashboardintegrasi_v cannot be reverted.\n";

        return false;
    }
    */
}
