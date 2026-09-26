<?php

use yii\db\Migration;

/**
 * Class m220413_043630_migrate_multypayer_trigger_pembatalan
 */
class m220413_043630_migrate_multypayer_trigger_pembatalan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220413_043630_migrate_multypayer_trigger_pembatalan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220413_043630_migrate_multypayer_trigger_pembatalan cannot be reverted.\n";

        return false;
    }
    */
}
