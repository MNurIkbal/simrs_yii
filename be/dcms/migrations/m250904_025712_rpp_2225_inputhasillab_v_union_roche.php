<?php

use yii\db\Migration;

/**
 * Class m250904_025712_rpp_2225_inputhasillab_v_union_roche
 */
class m250904_025712_rpp_2225_inputhasillab_v_union_roche extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS inputhasillab_v");
        $inputhasillab_v = file_get_contents(__DIR__ . '/definitions/inputhasillab_v.sql');
        $this->execute($inputhasillab_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250904_025712_rpp_2225_inputhasillab_v_union_roche cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250904_025712_rpp_2225_inputhasillab_v_union_roche cannot be reverted.\n";

        return false;
    }
    */
}
