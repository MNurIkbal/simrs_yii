<?php

use yii\db\Migration;

/**
 * Class m230822_030635_RPP_497_detailreferral_v
 */
class m230822_030635_RPP_497_detailreferral_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS detailreferral_v");
        $detailreferral_v = file_get_contents(__DIR__ . '/definitions/detailreferral_v.sql');
        $this->execute($detailreferral_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230822_030635_RPP_497_detailreferral_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_030635_RPP_497_detailreferral_v cannot be reverted.\n";

        return false;
    }
    */
}
