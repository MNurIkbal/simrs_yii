<?php

use yii\db\Migration;

/**
 * Class m250703_100835_migration_rpp_2176_create_laporankunjunganri_fn
 */
class m250703_100835_migration_rpp_2176_create_laporankunjunganri_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS laporankunjunganri_fn(timestamp, timestamp)");
        $laporankunjunganri_fn = file_get_contents(__DIR__ . '/definitions/laporankunjunganri_fn.sql');
        $this->execute($laporankunjunganri_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250703_100835_migration_rpp_2176_create_laporankunjunganri_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250703_100835_migration_rpp_2176_create_laporankunjunganri_fn cannot be reverted.\n";

        return false;
    }
    */
}
