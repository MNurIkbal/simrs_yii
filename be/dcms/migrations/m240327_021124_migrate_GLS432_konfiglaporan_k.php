<?php

use yii\db\Migration;

/**
 * Class m240327_021124_migrate_GLS432_konfiglaporan_k
 */
class m240327_021124_migrate_GLS432_konfiglaporan_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM konfiglaporan_k WHERE source_view = 'laporanpenyetoranrawatjalan_v' AND jenis_laporan = 'Laporan Penyetoran Pasien Rawat Jalan';");
        $this->execute("INSERT INTO public.konfiglaporan_k
        (key_laporan, jenis_laporan, source_view, \"filter\")
        VALUES('laporan_keuangan', 'Laporan Penyetoran Pasien Rawat Jalan', 'laporanpenyetoranrawatjalan_v', '\"Tanggal Kunjungan\"');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240327_021124_migrate_GLS432_konfiglaporan_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240327_021124_migrate_GLS432_konfiglaporan_k cannot be reverted.\n";

        return false;
    }
    */
}
