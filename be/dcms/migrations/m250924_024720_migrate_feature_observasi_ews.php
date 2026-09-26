<?php

use yii\db\Migration;

/**
 * Class m250924_024720_migrate_feature_observasi_ews
 */
class m250924_024720_migrate_feature_observasi_ews extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'jenis_ews'");

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(2259, 'jenis_ews', 'Ibu Hamil', 'Ibu Hamil', NULL, NULL, NULL, '2025-09-08 00:00:00.000', 1, NULL, NULL, NULL, false, true, NULL, NULL);");

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(2257, 'jenis_ews', 'Dewasa', 'Dewasa', NULL, NULL, NULL, '2025-09-08 00:00:00.000', 1, NULL, NULL, NULL, false, true, NULL, NULL);");

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(2258, 'jenis_ews', 'Anak', 'Anak', NULL, NULL, NULL, '2025-09-08 00:00:00.000', 1, NULL, NULL, NULL, false, true, NULL, NULL);");

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(2256, 'jenis_ews', 'Kebidanan', 'Kebidanan', NULL, NULL, NULL, '2025-09-08 00:00:00.000', 1, NULL, NULL, NULL, false, true, NULL, NULL);");

        $this->execute("INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES(2264, 'sumber_ttv', 'TTV EWS', 'TTV EWS', NULL, NULL, NULL, '2025-09-12 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);");

        $this->execute("DROP TABLE IF EXISTS ews_t");
        $this->execute("CREATE TABLE public.ews_t (
            ews_id serial4 NOT NULL,
            tanggal_ews timestamp(6) NOT NULL,
            jenis_ews varchar(255) NOT NULL,
            pendaftaran_id int4 NULL,
            pasienadmisi_id int4 NULL,
            pegawai_id int4 NULL,
            skor_total varchar(255) NULL,
            additional_data text NULL,
            created_date timestamp(6) DEFAULT 'now'::text::date NULL,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool DEFAULT false NOT NULL,
            is_active bool DEFAULT true NOT NULL,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            CONSTRAINT ews_t_pkey PRIMARY KEY (ews_id)
        );");

        $this->execute("ALTER TABLE public.konfigsystem_k ADD COLUMN IF NOT EXISTS konfig_observasi_ews text NULL;");
        $this->execute("UPDATE public.konfigsystem_k SET konfig_observasi_ews='{\"RJ\":true,\"RI\":true,\"RD\":true}'
            WHERE konfigsystem_id = 1;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250924_024720_migrate_feature_observasi_ews cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250924_024720_migrate_feature_observasi_ews cannot be reverted.\n";

        return false;
    }
    */
}
