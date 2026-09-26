<?php

use yii\db\Migration;

/**
 * Class m251206_101500_migration_add_pasien_lama_column_to_rujukanbantaran_t
 */
class m251206_101500_migration_add_pasien_lama_column_to_rujukanbantaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(
            "ALTER TABLE public.rujukanbantaran_t
             ADD COLUMN IF NOT EXISTS pasien_lama bool DEFAULT FALSE;"
        );

        $this->execute(
            "UPDATE public.rujukanbantaran_t
             SET pasien_lama = COALESCE(pasien_lama, FALSE)
             WHERE pasien_lama IS NULL;"
        );

        $this->execute(
            "INSERT INTO public.lookup_m
            (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by)
            VALUES(4040, 'status_pelayanan_bantaran', 'Menunggu Didaftarkan', 'Belum Didaftarkan', NULL, NULL, NULL, '2025-12-03 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);"
        );

        $this->execute("DROP VIEW IF EXISTS rujukanbantaran_v;");
        $rujukanbantaran_v = file_get_contents(__DIR__ . '/definitions/rujukanbantaran_v.sql');
        $this->execute($rujukanbantaran_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251206_101500_migration_add_pasien_lama_column_to_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }
}
