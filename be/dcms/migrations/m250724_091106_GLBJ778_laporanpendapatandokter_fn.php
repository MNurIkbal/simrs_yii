<?php

use yii\db\Migration;

/**
 * Class m250724_091106_GLBJ778_laporanpendapatandokter_fn
 */
class m250724_091106_GLBJ778_laporanpendapatandokter_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS laporan_pendapatan_dokter");
        $laporan_pendapatan_dokter = file_get_contents(__DIR__ . '/definitions/laporan_pendapatan_dokter.fn.sql');
        $this->execute($laporan_pendapatan_dokter);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250724_091106_GLBJ778_laporanpendapatandokter_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250724_091106_GLBJ778_laporanpendapatandokter_fn cannot be reverted.\n";

        return false;
    }
    */
}
