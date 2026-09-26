<?php

use yii\db\Migration;

/**
 * Class m230822_030810_RPP_497_summaryreferral_v
 */
class m230822_030810_RPP_497_summaryreferral_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS summaryreferral_v");
        $summaryreferral_v = file_get_contents(__DIR__ . '/definitions/summaryreferral_v.sql');
        $this->execute($summaryreferral_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230822_030810_RPP_497_summaryreferral_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_030810_RPP_497_summaryreferral_v cannot be reverted.\n";

        return false;
    }
    */
}
