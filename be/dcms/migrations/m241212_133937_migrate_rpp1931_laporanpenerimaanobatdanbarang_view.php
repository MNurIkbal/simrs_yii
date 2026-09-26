<?php

use yii\db\Migration;

/**
 * Class m241212_133937_migrate_rpp1931_laporanpenerimaanobatdanbarang_view
 */
class m241212_133937_migrate_rpp1931_laporanpenerimaanobatdanbarang_view extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS infopenerimaanobatdetail_v");
        $infopenerimaanobatdetail_v = file_get_contents(__DIR__ . '/definitions/infopenerimaanobatdetail_v.sql');
        $this->execute($infopenerimaanobatdetail_v);
		
        $this->execute("DROP VIEW IF EXISTS infopenerimaanbarangdetail_v");
        $infopenerimaanbarangdetail_v = file_get_contents(__DIR__ . '/definitions/infopenerimaanbarangdetail_v.sql');
        $this->execute($infopenerimaanbarangdetail_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_133937_migrate_rpp1931_laporanpenerimaanobatdanbarang_view cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_133937_migrate_rpp1931_laporanpenerimaanobatdanbarang_view cannot be reverted.\n";

        return false;
    }
    */
}
