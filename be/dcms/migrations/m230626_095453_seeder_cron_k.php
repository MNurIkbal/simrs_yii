<?php

use yii\db\Migration;

/**
 * Class m230626_095453_seeder_cron_k
 */
class m230626_095453_seeder_cron_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("truncate table cron_k restart identity;");
        $this->execute("
            INSERT INTO public.cron_k
            (cron_id, cron_nama, cron_tgl_mulai, token, url, cron_tgl_akhir, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by, is_sync) VALUES
            (1, 'kunjungan', '2023-06-23 14:03:47.000', NULL, NULL, '2023-06-23 14:03:47.000', NULL, '2022-10-01 00:00:00.000', NULL, 1026, '2023-06-23 14:03:47.000', 1, false, true, NULL, NULL, false),
            (2, 'diagnosa', '2023-06-26 15:47:31.000', NULL, NULL, '2023-06-26 15:47:31.000', NULL, '2022-10-01 00:00:00.000', NULL, 686, '2023-06-26 15:47:31.000', 1, false, true, NULL, NULL, false),
            (3, 'tagihan', '2023-06-26 15:47:31.000', NULL, NULL, '2023-06-26 15:47:31.000', NULL, '2022-10-01 00:00:00.000', NULL, 762, '2023-06-26 15:47:31.000', 1, false, true, NULL, NULL, false),
            (4, 'master layanan', '2022-11-24 07:00:00.000', NULL, NULL, '2022-11-24 07:00:00.000', NULL, '2022-11-24 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL, false),
            (5, 'pegawai', '2019-12-10 00:00:00.000', NULL, NULL, '2020-01-01 14:30:14.000', NULL, '2019-11-21 00:00:00.000', NULL, 13, '2020-03-31 12:01:14.000', 1, false, true, NULL, NULL, false),
            (6, 'bagian', '2019-12-10 00:00:00.000', NULL, NULL, '2020-02-17 14:11:50.000', NULL, '2019-12-18 00:00:00.000', NULL, 42, '2020-02-17 14:11:50.000', 1, false, true, NULL, NULL, false),
            (7, 'nota', '2019-12-10 00:00:00.000', NULL, NULL, '2020-02-10 16:16:33.000', NULL, '2020-01-09 00:00:00.000', NULL, 9, '2020-02-10 16:16:33.000', 1, false, true, NULL, NULL, false),
            (8, 'potongan', '2022-11-23 20:15:34.000', NULL, NULL, '2022-11-23 20:15:34.000', NULL, '2020-01-15 00:00:00.000', NULL, 101, '2022-11-23 20:15:34.000', 1, false, true, NULL, NULL, false),
            (9, 'adjusment_header', '2023-05-02 11:28:39.000', NULL, NULL, '2023-05-02 11:28:39.000', NULL, '2020-02-13 00:00:00.000', NULL, 254, '2023-05-02 11:28:39.000', 1, false, true, NULL, NULL, false),
            (10, 'adjusment_detail', '2023-05-02 11:28:39.000', NULL, NULL, '2023-05-02 11:28:39.000', NULL, '2020-02-13 00:00:00.000', NULL, 226, '2023-05-02 11:28:39.000', 1, false, true, NULL, NULL, false),
            (11, 'kunjungan_rj', '2022-11-30 00:00:00.000', NULL, NULL, '2022-11-29 00:02:12.000', NULL, '2020-04-21 00:00:00.000', NULL, 949, '2022-11-29 00:02:12.000', 1, false, true, NULL, NULL, false),
            (12, 'diagnosa_rj', '2022-10-01 00:00:00.000', NULL, NULL, '2022-10-01 00:00:00.000', NULL, '2020-04-21 00:00:00.000', NULL, 949, '2022-11-29 00:02:12.000', 1, false, true, NULL, NULL, false),
            (13, 'tagihan_rj', '2020-04-23 00:01:58.000', NULL, NULL, '2020-04-23 00:01:58.000', NULL, '2020-04-21 00:00:00.000', NULL, 6, '2020-04-23 00:01:58.000', 1, false, true, NULL, NULL, false);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230626_095453_seeder_cron_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230626_095453_seeder_cron_k cannot be reverted.\n";

        return false;
    }
    */
}
