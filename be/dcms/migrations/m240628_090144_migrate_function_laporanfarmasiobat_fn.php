<?php

use yii\db\Migration;

/**
 * Class m240628_090144_migrate_function_laporanfarmasiobat_fn
 */
class m240628_090144_migrate_function_laporanfarmasiobat_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.laporanfarmasiobat_fn();');

		$laporanfarmasiobat_fn = file_get_contents(__DIR__ . '/definitions/laporanfarmasiobat_fn.sql');
		$this->execute($laporanfarmasiobat_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240628_090144_migrate_function_laporanfarmasiobat_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240628_090144_migrate_function_laporanfarmasiobat_fn cannot be reverted.\n";

        return false;
    }
    */
}
