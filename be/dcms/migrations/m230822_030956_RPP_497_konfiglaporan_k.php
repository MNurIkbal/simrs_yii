<?php

use yii\db\Migration;

/**
 * Class m230822_030956_RPP_497_konfiglaporan_k
 */
class m230822_030956_RPP_497_konfiglaporan_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.konfiglaporan_k WHERE jenis_laporan = 'Laporan Referral';");
        $this->execute("DELETE FROM public.konfiglaporan_k WHERE jenis_laporan = 'Laporan Summary Referral';");

        $this->execute("INSERT INTO public.konfiglaporan_k
            (key_laporan, jenis_laporan, source_view, \"filter\", additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer)
            VALUES('laporan_kasir', 'Laporan Referral', 'detailreferral_v', '\"Tanggal\"', NULL, '2023-08-09 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL, NULL);
        ");
        $this->execute("INSERT INTO public.konfiglaporan_k
            (key_laporan, jenis_laporan, source_view, \"filter\", additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, footer)
            VALUES('laporan_kasir', 'Laporan Summary Referral', 'summaryreferral_v', '\"Tanggal Transaksi\"', NULL, '2023-08-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230822_030956_RPP_497_konfiglaporan_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230822_030956_RPP_497_konfiglaporan_k cannot be reverted.\n";

        return false;
    }
    */
}
