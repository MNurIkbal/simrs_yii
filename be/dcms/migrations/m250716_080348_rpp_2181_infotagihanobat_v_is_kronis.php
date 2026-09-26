<?php

use yii\db\Migration;

/**
 * Class m250716_080348_rpp_2181_infotagihanobat_v_is_kronis
 */
class m250716_080348_rpp_2181_infotagihanobat_v_is_kronis extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infotagihanobat_v");
        $infotagihanobat_v = file_get_contents(__DIR__ . '/definitions/infotagihanobat_v.sql');
        $this->execute($infotagihanobat_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250716_080348_rpp_2181_infotagihanobat_v_is_kronis cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250716_080348_rpp_2181_infotagihanobat_v_is_kronis cannot be reverted.\n";

        return false;
    }
    */
}
