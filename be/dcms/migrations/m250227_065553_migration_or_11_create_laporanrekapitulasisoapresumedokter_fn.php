<?php

use yii\db\Migration;

/**
 * Class m250227_065553_migration_or_11_create_laporanrekapitulasisoapresumedokter_fn
 */
class m250227_065553_migration_or_11_create_laporanrekapitulasisoapresumedokter_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.laporanrekapitulasisoapresumedokter_fn(date, date, int4)");
        $query = file_get_contents(__DIR__ . '/definitions/laporanrekapitulasisoapresumedokter_fn.sql');
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250227_065553_migration_or_11_create_laporanrekapitulasisoapresumedokter_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250227_065553_migration_or_11_create_laporanrekapitulasisoapresumedokter_fn cannot be reverted.\n";

        return false;
    }
    */
}
