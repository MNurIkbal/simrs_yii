<?php

use yii\db\Migration;

/**
 * Class m190423_075203_seed_kertas_k
 */
class m190423_075203_seed_kertas_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE kertas_k RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO public.kertas_k(kertas_id, kertas_kode, kertas_nama, panjang, lebar, batas_kiri, batas_kanan, batas_atas, batas_bawah, ppi, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1, \'323\', \'sdsd1\', 24, 22, 5, 3, 2, 4, 0, NULL, \'2018-03-14 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-03-19 04:54:12\', 1),
            (2, \'324\', \'sdsd\', 25, 22, 5, 3, 2, 4, 0, NULL, \'2018-03-15 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-03-15 10:17:19\', 1),
            (3, \'90\', \'Kop Surat Struk Pembayaran\', 25, 23, 3, 3, 3, 3, 0, NULL, \'2018-03-19 00:00:00\', NULL, 2, \'2019-01-10 14:31:05\', 77, \'f\', \'f\', NULL, NULL),
            (4, \'7979\', \'Kop Surat Pendaftaran\', 15, 18, 3, 3, 3, 3, 0, NULL, \'2018-03-19 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'f\', NULL, NULL),
            (5, \'55\', \'Kop Surat Undangan\', 34, 25, 3, 3, 3, 3, 0, NULL, \'2018-03-19 00:00:00\', NULL, 3, \'2018-09-21 17:32:38\', 1, \'f\', \'f\', NULL, NULL),
            (6, \'7878\', \'Kop Surat Pendaftaran\', 15, 18, 2, 2, 2, 2, 0, NULL, \'2018-03-20 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-03-27 03:42:52\', 1),
            (7, \'7171\', \'Kop Surat Pendaftaran\', 15, 18, 2, 2, 2, 2, 0, NULL, \'2018-03-20 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-03-27 10:37:08\', 1),
            (8, \'PR\', \'Print Resep\', 27, 30, 1, 1, 6, 1, 0, NULL, \'2018-03-27 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-03-27 11:28:18\', 1),
            (9, \'21\', \'test\', 13, 10, 2, 3, 3, 3, 0, NULL, \'2018-03-27 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-03-27 11:13:06\', 1),
            (10, \'test2\', \'test cetak\', 7, 13, 12, 12, 12, 12, 0, NULL, \'2018-03-27 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-03-27 11:28:03\', 1),
            (11, \'tes\', \'tes\', 12, 12, 12, 12, 12, 12, 0, NULL, \'2018-03-27 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-03-27 11:29:08\', 1),
            (12, \'tes\', \'tes\', 29, 21, 0, 1, 1, 1, 0, NULL, \'2018-03-27 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-03-27 07:53:43\', 1),
            (13, \'A001\', \'Struk Antrian\', 29, 21, 5, 5, 5, 5, 0, NULL, \'2018-04-20 00:00:00\', NULL, NULL, NULL, NULL, \'t\', \'f\', \'2018-04-23 14:27:41\', 1),
            (14, \'Potrait\', \'Potrait\', 21, 29, 1, 1, 1, 1, 0, NULL, \'2018-05-31 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'f\', NULL, NULL),
            (15, \'Kartu\', \'Kartu\', 5.4, 8.5, 0.5, 0.5, 0.1, 0.1, 0, NULL, \'2018-06-12 00:00:00\', NULL, 1, \'2019-03-11 16:53:50\', 1, \'f\', \'f\', NULL, NULL),
            (16, \'Gelang\', \'Gelang\', 4, 20, 0.1, 0.1, 0.1, 0.1, 0, NULL, \'2018-06-12 00:00:00\', NULL, 3, \'2019-03-18 16:53:14\', 1, \'f\', \'f\', NULL, NULL),
            (17, \'Label\', \'Label\', 20, 14, 0, 0, 0, 0, 0, NULL, \'2018-06-25 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'f\', NULL, NULL),
            (18, \'01bpjs\', \'BPJS-SEP\', 15, 25, 3, 0, 1, 0, 0, NULL, \'2018-08-29 11:01:30\', 1, 2, \'2018-08-29 11:15:41\', 1, \'f\', \'f\', NULL, NULL),
            (19, \'A4\', \'A4\', 29, 21, 1, 1, 3, 1, 0, NULL, \'2018-09-13 14:11:37\', 1, 3, \'2019-03-11 17:14:06\', 1, \'f\', \'f\', NULL, NULL),
            (20, \'JK1\', \'Jenis Coba\', 34, 25, 3, 3, 1, 1, 0, NULL, \'2018-09-21 17:13:00\', 1, 4, \'2018-09-21 17:35:06\', 1, \'f\', \'f\', NULL, NULL),
            (21, \'KOP 01\', \'Kop Surat\', 29, 21, 1, 1, 1, 1, 0, NULL, \'2018-10-09 11:18:54\', 1, 0, NULL, NULL, \'f\', \'f\', NULL, NULL);
        ');
        $this->execute('
            SELECT setval(\'public.kertas_k_kertas_id_seq\', (SELECT COALESCE(MAX(kertas_id) ,1)+1 FROM kertas_k), false);

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            TRUNCATE TABLE kertas_k RESTART IDENTITY;

        ');

        $this->execute('
            SELECT setval(\'public.kertas_k_kertas_id_seq\', (SELECT COALESCE(MAX(kertas_id) ,1)+1 FROM kertas_k), false);

        ');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190423_075203_seed_kertas_k cannot be reverted.\n";

        return false;
    }
    */
}
