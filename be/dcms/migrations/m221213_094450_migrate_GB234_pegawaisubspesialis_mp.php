<?php

use yii\db\Migration;

/**
 * Class m221213_094450_migrate_GB234_pegawaisubspesialis_mp
 */
class m221213_094450_migrate_GB234_pegawaisubspesialis_mp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.pegawaisubspesialis_mp (
            pegawaisubspesialis_id serial8 NOT NULL,
            pegawai_id int4 NOT NULL,
            subspesialis_id int4 NULL,
            additional_data text NULL,
            created_date timestamp(6) NOT NULL DEFAULT 'now'::text::timestamp,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            CONSTRAINT pegawaisubspesialis_mp_pkey PRIMARY KEY (pegawaisubspesialis_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221213_094450_migrate_GB234_pegawaisubspesialis_mp cannot be reverted.\n";
        return false;
    }
}
