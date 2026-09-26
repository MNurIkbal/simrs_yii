<?php

use yii\db\Migration;

/**
 * Class m230725_073126_rpp_394_infojadwaldokter_v
 */
class m230725_073126_rpp_394_infojadwaldokter_v extends Migration
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
        echo "m230725_073126_rpp_394_infojadwaldokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230725_073126_rpp_394_infojadwaldokter_v cannot be reverted.\n";

        return false;
    }
    */
}
