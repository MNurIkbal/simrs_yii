<?php

use yii\db\Migration;

/**
 * Class m231214_162143_rpp_899_infostokobatalkes_fnr_new
 */
class m231214_162143_rpp_899_infostokobatalkes_fnr_new extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $infostokobatalkes_fnr_new = file_get_contents(__DIR__ . '/definitions/infostokobatalkes_fnr_new.fn.sql');
        $this->execute($infostokobatalkes_fnr_new);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231214_162143_rpp_899_infostokobatalkes_fnr_new cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231214_162143_rpp_899_infostokobatalkes_fnr_new cannot be reverted.\n";

        return false;
    }
    */
}
