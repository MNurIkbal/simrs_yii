<?php

use yii\db\Migration;

/**
 * Class m220130_075206_migrate_BTS85_set_tgl_sensus
 */
class m220130_075206_migrate_BTS85_set_tgl_sensus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
            ADD COLUMN "set_tgl_sensusi" date DEFAULT \'2020-07-07\'::date;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220130_075206_migrate_BTS85_set_tgl_sensus cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220130_075206_migrate_BTS85_set_tgl_sensus cannot be reverted.\n";

        return false;
    }
    */
}
