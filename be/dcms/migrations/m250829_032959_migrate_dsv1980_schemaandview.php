<?php

use yii\db\Migration;

/**
 * Class m250829_032959_migrate_dsv1980_schemaandview
 */
class m250829_032959_migrate_dsv1980_schemaandview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP TABLE IF EXISTS rehabilitasiterapi_t");
        $this->execute("CREATE TABLE public.rehabilitasiterapi_t (
            rehabilitasi_id bigserial NOT NULL,
            pendaftaran_id int4 NULL,
            ftp_url varchar NULL,
            params text NULL,
            created_date timestamp(6) DEFAULT 'now'::text::date NOT NULL,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool DEFAULT false NOT NULL,
            is_active bool DEFAULT true NOT NULL,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            CONSTRAINT rehabilitasiterapi_t_pk PRIMARY KEY (rehabilitasi_id)
        );");

        $this->execute("DROP VIEW IF EXISTS inforiwayatpasien_v");
        $inforiwayatpasien_v = file_get_contents(__DIR__ . '/definitions/inforiwayatpasien_v_15082025.sql');
        $this->execute($inforiwayatpasien_v);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250829_032959_migrate_dsv1980_schemaandview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250829_032959_migrate_dsv1980_schemaandview cannot be reverted.\n";

        return false;
    }
    */
}
