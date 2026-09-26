<?php

use yii\db\Migration;

/**
 * Class m230628_025755_migrate_DSV259_konfiglaporan_k_data
 */
class m230628_025755_migrate_DSV259_konfiglaporan_k_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("INSERT INTO public.konfiglaporan_k
        (key_laporan, jenis_laporan, source_view, \"filter\", additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer)
        VALUES('laporan_kasir', 'Laporan Pembelian', 'laporanpenerimaanobatdetail_v', '\"tanggal_penerimaan\"', NULL, '2023-06-19 16:46:02.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL, NULL);");

        $this->execute("INSERT INTO public.konfiglaporan_k
        (key_laporan, jenis_laporan, source_view, \"filter\", additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer)
        VALUES('laporan_kasir', 'Laporan pengeluaran kasir', 'laporanpembayarantransaksi_v', '\"tanggal\"', NULL, '2023-06-20 09:45:14.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL, '{\"I\":\"SUM\"}');");

        $this->execute("INSERT INTO public.konfiglaporan_k
        (key_laporan, jenis_laporan, source_view, \"filter\", additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer)
        VALUES('laporan_kasir', 'Laporan kas Harian', 'laporankasharian_v', '\"tgl_daftar\"', NULL, '2023-06-20 09:45:41.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL, '{\"H\":\"SUM\"}');");

        $this->execute("INSERT INTO public.konfiglaporan_k
        (key_laporan, jenis_laporan, source_view, \"filter\", additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer)
        VALUES('laporan_kasir', 'Laporan Penerimaan Harian', 'laporanpenerimaandatakasir_v', '\"tanggal\"', NULL, '2023-06-20 09:46:14.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL, '{\"H\":\"SUM\"}');");

        $this->execute("INSERT INTO public.konfiglaporan_k
        (key_laporan, jenis_laporan, source_view, \"filter\", additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer)
        VALUES('laporan_kasir', 'Rekap Laporan Kasir', 'laporanrekapkasir_v', '\"tanggal\"', NULL, '2023-06-20 09:46:33.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL, '{\"G\":\"SUM\",\"H\":\"SUM\",\"I\":\"SUM\",\"J\":\"SUM\",\"L\":\"SUM\",\"M\":\"SUM\",\"N\":\"SUM\"}');");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230628_025755_migrate_DSV259_konfiglaporan_k_data cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230628_025755_migrate_DSV259_konfiglaporan_k_data cannot be reverted.\n";

        return false;
    }
    */
}
