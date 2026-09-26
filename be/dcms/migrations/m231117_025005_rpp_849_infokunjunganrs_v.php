<?php

use yii\db\Migration;

/**
 * Class m231117_025005_rpp_849_infokunjunganrs_v
 */
class m231117_025005_rpp_849_infokunjunganrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infokunjunganrs_v");
        $infokunjunganrs_v = file_get_contents(__DIR__ . '/definitions/infokunjunganrs_v.view.sql');
        $this->execute($infokunjunganrs_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231117_025005_rpp_849_infokunjunganrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231117_025005_rpp_849_infokunjunganrs_v cannot be reverted.\n";

        return false;
    }
    */
}
