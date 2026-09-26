<?php

use yii\db\Migration;

/**
 * Class m231028_100953_update_generate_kodebooking
 */
class m231028_100953_update_generate_kodebooking extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $generate_kodebooking_antrianjkn = file_get_contents(__DIR__ . '/definitions/generate_kodebooking_antrianjkn.fn.sql');
        $this->execute($generate_kodebooking_antrianjkn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231028_100953_update_generate_kodebooking cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231028_100953_update_generate_kodebooking cannot be reverted.\n";

        return false;
    }
    */
}
