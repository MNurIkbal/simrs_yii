<?php

use yii\db\Migration;

/**
 * Class m230815_125751_migrate_DSV318_lookup_m
 */
class m230815_125751_migrate_DSV318_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2100;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2020;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2018;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2019;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2015;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2016;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2017;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2012;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2013;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2014;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2009;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2010;");
        $this->execute("DELETE FROM public.lookup_m WHERE lookup_id = 2011;");

        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2100, 'surat_keterangan', 'Hasil Pemeriksaan Golongan Darah', 'Hasil Pemeriksaan Golongan Darah', NULL, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2020, 'keputusan', 'IGD', 'IGD', 3, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2018, 'keputusan', 'SESUAI ANTRIAN', 'SESUAI ANTRIAN', 1, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2019, 'keputusan', 'POLIKLINIK DI SEGERAKAN', 'POLIKLINIK DI SEGERAKAN', 2, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2015, 'batuk', 'TIDAK ADA', 'TIDAK ADA', 1, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2016, 'batuk', 'BATUK > 2 MINGGU', 'BATUK > 2 MINGGU', 2, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2017, 'batuk', 'HEMOPTOE', 'HEMOPTOE', 3, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2012, 'nyeri_dada', 'TIDAK ADA', 'TIDAK ADA', 1, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2013, 'nyeri_dada', 'ADA (TINGKAT SEDANG)', 'ADA (TINGKAT SEDANG)', 2, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2014, 'nyeri_dada', 'NYERI DADA KIRITEMBUS BELAKANG', 'NYERI DADA KIRITEMBUS BELAKANG', 3, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2009, 'resiko_jatuh', 'RESIKO RENDAH', 'RESIKO RENDAH', 1, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2010, 'resiko_jatuh', 'RESIKO SEDANG', 'RESIKO SEDANG', 2, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
        $this->execute("INSERT INTO public.lookup_m
        (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
        VALUES(2011, 'resiko_jatuh', 'RESIKO TINGGI', 'RESIKO TINGGI', 3, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230815_125751_migrate_DSV318_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230815_125751_migrate_DSV318_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
