<?php

use yii\db\Migration;

/**
 * Class m250805_090847_rpp_2109_infojadwaldokter_v_is_executive
 */
class m250805_090847_rpp_2109_infojadwaldokter_v_is_executive extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infojadwaldokter_v");
        $infojadwaldokter_v = file_get_contents(__DIR__ . '/definitions/infojadwaldokter_v.sql');
        $this->execute($infojadwaldokter_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250805_090847_rpp_2109_infojadwaldokter_v_is_executive cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250805_090847_rpp_2109_infojadwaldokter_v_is_executive cannot be reverted.\n";

        return false;
    }
    */
}
