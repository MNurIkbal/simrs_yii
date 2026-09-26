<?php

use yii\db\Migration;

/**
 * Class m230515_082518_migrate_DSV46_lookup_m
 */
class m230515_082518_migrate_DSV46_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'rujukanbpjs';");
        $this->execute(" 
            INSERT INTO lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1907, 'rujukanbpjs', 'Lahir di RS', 'born', NULL, 'born', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1901, 'rujukanbpjs', 'Rujukan FKTP', 'gp', NULL, 'gp', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1902, 'rujukanbpjs', 'Rujukan FKRTL', 'hosp-trans', NULL, 'hosp-trans', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1903, 'rujukanbpjs', 'Rujukan Spesialis', 'mp', NULL, 'mp', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1904, 'rujukanbpjs', 'Dari Rawat Jalan', 'outp', NULL, 'outp', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1905, 'rujukanbpjs', 'Dari Rawat Inap', 'inp', NULL, 'inp', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1906, 'rujukanbpjs', 'Dari Rawat Darurat', 'emd', NULL, 'emd', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1908, 'rujukanbpjs', 'Rujukan Panti Jompo', 'nursing', NULL, 'nursing', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1909, 'rujukanbpjs', 'Rujukan dari RS Jiwa', 'psych', NULL, 'psych', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1910, 'rujukanbpjs', 'Rujukan Fasilitas Rehab', 'rehab', NULL, 'rehab', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1911, 'rujukanbpjs', 'Lain-lain', 'other', NULL, 'other', NULL, '2023-04-04 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);  
        ");

        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'inacbg_penjamin';");
        $this->execute("
            INSERT INTO lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(1913, 'inacbg_penjamin', 'JAMINAN COVID-19', '71', 2, NULL, NULL, '2023-04-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1918, 'inacbg_penjamin', 'JAMPERSAL', '76', 7, NULL, NULL, '2023-04-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1917, 'inacbg_penjamin', 'JAMINAN CO-INSIDENSE', '75', 6, NULL, NULL, '2023-04-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1916, 'inacbg_penjamin', 'JAMINAN PERPANJANGAN MASA RAWAT', '74', 5, NULL, NULL, '2023-04-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1915, 'inacbg_penjamin', 'JAMINAN BAYI BARU LAHIR', '73', 4, NULL, NULL, '2023-04-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1914, 'inacbg_penjamin', 'JAMINAN KIPI', '72', 3, NULL, NULL, '2023-04-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1912, 'inacbg_penjamin', 'JKN', '3', 1, NULL, NULL, '2023-04-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);
        ");

        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'jenis_identitas_bpjs';");
        $this->execute("
            INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(1922, 'jenis_identitas_bpjs', 'NIK', 'nik', 1, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1927, 'jenis_identitas_bpjs', 'Surat UNCHR', 'unhcr', 6, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1923, 'jenis_identitas_bpjs', 'KITAS', 'kitas', 2, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1924, 'jenis_identitas_bpjs', 'PASPOR', 'paspor', 3, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1925, 'jenis_identitas_bpjs', 'Kartu Peserta JKN', 'kartu_jkn', 4, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1926, 'jenis_identitas_bpjs', 'Kertu Keluarga', 'kk', 5, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1931, 'jenis_identitas_bpjs', 'Surat Jaminan Perawatan', 'sjp', 10, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1932, 'jenis_identitas_bpjs', 'Klaim Ibu', 'klaim_ibu', 12, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, true, true, NULL, NULL),
            (1933, 'jenis_identitas_bpjs', 'Lainnya', 'lainnya', 11, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1928, 'jenis_identitas_bpjs', 'Surat Kelurahan', 'kelurahan', 7, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1929, 'jenis_identitas_bpjs', 'Surat Dinas Sosial', 'dinsos', 8, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (1930, 'jenis_identitas_bpjs', 'Surat Dinas Kesehatan', 'dinkes', 9, NULL, NULL, '2023-05-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);
        ");
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230515_082518_migrate_DSV46_lookup_m cannot be reverted.\n";
        
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230515_082518_migrate_DSV46_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
