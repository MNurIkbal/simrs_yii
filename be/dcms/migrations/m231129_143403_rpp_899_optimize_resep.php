<?php

use yii\db\Migration;

/**
 * Class m231129_143403_rpp_899_optimize_resep
 */
class m231129_143403_rpp_899_optimize_resep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS prescribe_v");
        $prescribe_v = file_get_contents(__DIR__ . '/definitions/prescribe_v.view.sql');
        $this->execute($prescribe_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231129_143403_rpp_899_optimize_resep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231129_143403_rpp_899_optimize_resep cannot be reverted.\n";

        return false;
    }
    */
}
