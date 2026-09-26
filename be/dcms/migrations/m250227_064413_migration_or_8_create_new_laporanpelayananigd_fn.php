<?php

use yii\db\Migration;

/**
 * Class m250227_064413_migration_or_8_create_new_laporanpelayananigd_fn
 */
class m250227_064413_migration_or_8_create_new_laporanpelayananigd_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.new_laporanpelayananigd_fn(date, date)");
        $query = file_get_contents(__DIR__ . '/definitions/new_laporanpelayananigd_fn.sql');
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250227_064413_migration_or_8_create_new_laporanpelayananigd_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250227_064413_migration_or_8_create_new_laporanpelayananigd_fn cannot be reverted.\n";

        return false;
    }
    */
}
