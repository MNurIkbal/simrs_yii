<?php

use yii\db\Migration;

/**
 * Class m250724_091506_GLBJ778_konfiglaporank_laporanpendapatandokter_seeder
 */
class m250724_091506_GLBJ778_konfiglaporank_laporanpendapatandokter_seeder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM konfiglaporan_k
            WHERE key_laporan = \'laporan_kasir\' AND jenis_laporan = \'Laporan Pendapatan Dokter\';
        ');

        $this->execute('
            INSERT INTO konfiglaporan_k (key_laporan,jenis_laporan,source_view,"filter",additional_data,created_date,created_by,modified_count,last_modified_date,last_modified_by,is_deleted,is_active,deleted_date,deleted_by,footer) VALUES
	        (\'laporan_kasir\',\'Laporan Pendapatan Dokter\',\'laporan_pendapatan_dokter\',NULL,\'{"is_function": true}\',\'2025-07-24 00:00:00.000\',NULL,NULL,NULL,NULL,false,true,NULL,NULL,NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250724_091506_GLBJ778_konfiglaporank_laporanpendapatandokter_seeder cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250724_091506_GLBJ778_konfiglaporank_laporanpendapatandokter_seeder cannot be reverted.\n";

        return false;
    }
    */
}
