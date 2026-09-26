<?php

use yii\db\Migration;

/**
 * Class m240405_085915_migrate_GLS517_konfiglaporan_k
 */
class m240405_085915_migrate_GLS517_konfiglaporan_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM konfiglaporan_k WHERE jenis_laporan = 'Laporan Penyetoran Pasien Rawat Darurat' AND source_view = 'laporanpenyetoranrawatdarurat_v';");
        $this->execute("INSERT INTO public.konfiglaporan_k
        (key_laporan, jenis_laporan, source_view, \"filter\")
        VALUES('laporan_keuangan', 'Laporan Penyetoran Pasien Rawat Darurat', 'laporanpenyetoranrawatdarurat_v', '\"Tanggal Kunjungan\"');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240405_085915_migrate_GLS517_konfiglaporan_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240405_085915_migrate_GLS517_konfiglaporan_k cannot be reverted.\n";

        return false;
    }
    */
}
