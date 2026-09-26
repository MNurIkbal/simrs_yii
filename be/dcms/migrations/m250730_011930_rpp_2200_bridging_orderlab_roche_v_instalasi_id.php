<?php

use yii\db\Migration;

/**
 * Class m250730_011930_rpp_2200_bridging_orderlab_roche_v_instalasi_id
 */
class m250730_011930_rpp_2200_bridging_orderlab_roche_v_instalasi_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS bridging_orderlab_roche_v");
        $bridging_orderlab_roche_v = file_get_contents(__DIR__ . '/definitions/bridging_orderlab_roche_v.sql');
        $this->execute($bridging_orderlab_roche_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250730_011930_rpp_2200_bridging_orderlab_roche_v_instalasi_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250730_011930_rpp_2200_bridging_orderlab_roche_v_instalasi_id cannot be reverted.\n";

        return false;
    }
    */
}
