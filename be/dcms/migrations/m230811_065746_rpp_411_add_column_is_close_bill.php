<?php

use yii\db\Migration;

/**
 * Class m230906_065746_rpp_411_add_column_is_close_bill
 */
class m230811_065746_rpp_411_add_column_is_close_bill extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.pendaftaran_t ADD IF NOT EXISTS is_close_bill bool NULL DEFAULT false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230906_065746_rpp_411_add_column_is_close_bill cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230906_065746_rpp_411_add_column_is_close_bill cannot be reverted.\n";

        return false;
    }
    */
}
