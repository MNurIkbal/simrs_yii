<?php

use yii\db\Migration;

/**
 * Class m230925_103304_migrate_DSV320_seeder_lookup_m
 */
class m230925_103304_migrate_DSV320_seeder_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'kesadaran';");
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'bahasa_skrining';");
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'pernapasan';");
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'resiko_jatuh';");
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'nyeri_dada';");
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'batuk';");
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'keputusan';");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2000, 'kesadaran', 'KESADARAN PENUH', 'KESADARAN PENUH', 1, NULL, NULL, '2023-07-07 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2003, 'bahasa_skrining', 'Indonesia', 'Indonesia', NULL, NULL, NULL, '2023-07-07 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2004, 'bahasa_skrining', 'Daerah', 'Daerah', NULL, NULL, NULL, '2023-07-07 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2005, 'bahasa_skrining', 'Asing', 'Asing', NULL, NULL, NULL, '2023-07-07 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2006, 'pernapasan', 'NAPAS NORMAL', 'NAPAS NORMAL', 1, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2007, 'pernapasan', 'TAMPAK SESAK NAPAS', 'TAMPAK SESAK NAPAS', 2, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2008, 'pernapasan', 'HENTI NAPAS', 'HENTI NAPAS', 3, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2001, 'kesadaran', 'TAMPAK MENGANTUK/GELISAH BICARA TIDAK JELAS', 'TAMPAK MENGANTUK/GELISAH BICARA TIDAK JELAS', 2, NULL, NULL, '2023-07-07 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2002, 'kesadaran', 'TIDAK SADAR/KEJANG', 'TIDAK SADAR/KEJANG', 3, NULL, NULL, '2023-07-07 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2020, 'keputusan', 'IGD', 'IGD', 3, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2018, 'keputusan', 'SESUAI ANTRIAN', 'SESUAI ANTRIAN', 1, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2019, 'keputusan', 'POLIKLINIK DI SEGERAKAN', 'POLIKLINIK DI SEGERAKAN', 2, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2015, 'batuk', 'TIDAK ADA', 'TIDAK ADA', 1, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2016, 'batuk', 'BATUK > 2 MINGGU', 'BATUK > 2 MINGGU', 2, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2017, 'batuk', 'HEMOPTOE', 'HEMOPTOE', 3, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2012, 'nyeri_dada', 'TIDAK ADA', 'TIDAK ADA', 1, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2013, 'nyeri_dada', 'ADA (TINGKAT SEDANG)', 'ADA (TINGKAT SEDANG)', 2, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2014, 'nyeri_dada', 'NYERI DADA KIRITEMBUS BELAKANG', 'NYERI DADA KIRITEMBUS BELAKANG', 3, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2009, 'resiko_jatuh', 'RESIKO RENDAH', 'RESIKO RENDAH', 1, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2010, 'resiko_jatuh', 'RESIKO SEDANG', 'RESIKO SEDANG', 2, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");

        $this->execute("INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(2011, 'resiko_jatuh', 'RESIKO TINGGI', 'RESIKO TINGGI', 3, NULL, NULL, '2023-07-10 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230925_103304_migrate_DSV320_seeder_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230925_103304_migrate_DSV320_seeder_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
