<?php

use yii\db\Migration;

/**
 * Class m240619_063522_migrate_app_diskon_table_approvaldiskon_t
 */
class m240619_063522_migrate_app_diskon_table_approvaldiskon_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           CREATE TABLE IF NOT EXISTS approvaldiskon_t (
                approvaldiskon_id serial8 NOT NULL PRIMARY KEY,
                pendaftaran_id int4 NULL,
                pembayaran_id int4 NULL,
                limit_diskon float4 NULL,
                jumlah_limit_diskon float8 NULL,
                diskon float4 NULL,
                jumlah_diskon float8 NULL,
                status_approve int2 DEFAULT 1323 NULL,
                pegawai_approve_id int2 NULL,
                tgl_approve timestamp(0) NULL,
                additional_data text NULL,
                created_date timestamp(6) DEFAULT \'now\'::text::date NOT NULL,
                created_by int4 NULL,
                modified_count int4 NULL,
                last_modified_date timestamp(6) NULL,
                last_modified_by int4 NULL,
                is_deleted bool DEFAULT false NOT NULL,
                is_active bool DEFAULT true NOT NULL,
                deleted_date timestamp(6) NULL,
                deleted_by int4 NULL
            );
       ');

        $this->execute('
           CREATE INDEX IF NOT EXISTS approvaldiskon_pendaftaran_id_idx ON approvaldiskon_t USING btree (pendaftaran_id);
       ');

        $this->execute('
           CREATE INDEX IF NOT EXISTS approvaldiskon_pembayaran_id_idx ON approvaldiskon_t USING btree (pembayaran_id);
       ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240619_063522_migrate_app_diskon_table_approvaldiskon_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240619_063522_migrate_app_diskon_table_approvaldiskon_t cannot be reverted.\n";

        return false;
    }
    */
}
