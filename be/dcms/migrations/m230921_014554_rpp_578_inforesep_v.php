<?php

use yii\db\Migration;

/**
 * Class m230921_014554_rpp_578_inforesep_v
 */
class m230921_014554_rpp_578_inforesep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS inforesep_v");
        $inforesep_v = file_get_contents(__DIR__ . '/definitions/inforesep_v.sql');
        $this->execute($inforesep_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230921_014554_rpp_578_inforesep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230921_014554_rpp_578_inforesep_v cannot be reverted.\n";

        return false;
    }
    */
}
