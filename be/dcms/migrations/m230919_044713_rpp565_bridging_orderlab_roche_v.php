<?php

use yii\db\Migration;

/**
 * Class m230919_044713_rpp565_bridging_orderlab_roche_v
 */
class m230919_044713_rpp565_bridging_orderlab_roche_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS bridging_orderlab_roche_v");
        $bridging_orderlab_roche_v = file_get_contents(__DIR__ . '/definitions/bridging_orderlab_roche_v.view.sql');
        $this->execute($bridging_orderlab_roche_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230919_044713_rpp565_bridging_orderlab_roche_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230919_044713_rpp565_bridging_orderlab_roche_v cannot be reverted.\n";

        return false;
    }
    */
}
