<?php

use yii\db\Migration;

/**
 * Class m220922_035309_migrate_BTS502_satusehat_integrasi_t
 */
class m220922_035309_migrate_BTS502_satusehat_integrasi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.satusehat_integrasi_t (
            id serial8 NOT NULL,
            pendaftaran_id int8 NOT NULL,
            satusehat_id text NULL,
            \"type\" text NOT NULL,
            state varchar(255) NULL,
            is_sent bool NULL DEFAULT false,
            payload text NULL,
            id_sync_sercon text NULL,
            sync_response text NULL,
            created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            additional_id int8 NULL,
            CONSTRAINT satusehat_integrasi_t_pkey PRIMARY KEY (id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220922_035309_migrate_BTS502_satusehat_integrasi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220922_035309_migrate_BTS502_satusehat_integrasi_t cannot be reverted.\n";

        return false;
    }
    */
}
