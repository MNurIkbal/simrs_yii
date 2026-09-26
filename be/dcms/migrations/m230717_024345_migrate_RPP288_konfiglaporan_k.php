<?php

use yii\db\Migration;

/**
 * Class m230717_024345_migrate_RPP288_konfiglaporan_k
 */
class m230717_024345_migrate_RPP288_konfiglaporan_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM konfiglaporan_k WHERE jenis_laporan = 'Laporan Jasa Medis';");
        $this->execute("DELETE FROM konfiglaporan_k WHERE jenis_laporan = 'Laporan Data Jurnal';");
        $this->execute("INSERT INTO public.konfiglaporan_k
        (key_laporan, jenis_laporan, source_view, \"filter\")
        VALUES('laporan_kasir', 'Laporan Jasa Medis', 'laporanjasamedis_v', '\"Tanggal Billing\"');");
        $this->execute("INSERT INTO public.konfiglaporan_k
        (key_laporan, jenis_laporan, source_view, \"filter\")
        VALUES('laporan_kasir', 'Laporan Data Jurnal', 'laporandatajurnal_v', '\"Tanggal Billing\"');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230717_024345_migrate_RPP288_konfiglaporan_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230717_024345_migrate_RPP288_konfiglaporan_k cannot be reverted.\n";

        return false;
    }
    */
}
