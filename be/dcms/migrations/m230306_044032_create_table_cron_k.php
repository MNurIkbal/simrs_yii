<?php

use yii\db\Migration;

/**
 * Class m230306_044032_create_table_cron_k
 */
class m230306_044032_create_table_cron_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
        CREATE TABLE IF NOT EXISTS public.cron_k (
            cron_id serial4 NOT NULL,
            cron_nama varchar(255) NULL,
            cron_tgl_mulai timestamp(0) NULL,
            \"token\" varchar(255) NULL,
            url text NULL,
            cron_tgl_akhir timestamp(0) NULL,
            additional_data text NULL,
            created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            is_sync bool NOT NULL DEFAULT false,
            CONSTRAINT cron_k_pkey PRIMARY KEY (cron_id)
        );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230306_044032_create_table_cron_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230306_044032_create_table_cron_k cannot be reverted.\n";

        return false;
    }
    */
}
