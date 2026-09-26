<?php

use yii\db\Migration;

/**
 * Class m230515_073455_migrate_ga_284_create_table_pasien_satusehat_m
 */
class m230515_073455_migrate_ga_284_create_table_pasien_satusehat_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS public.pasien_satusehat_m (
                id serial8 NOT NULL,
                pasien_id int8 NOT NULL,
                satusehat_pasien_id text NULL,
                satusehat_integration_id int8 NULL,
                created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
                created_by int4 NULL,
                last_modified_date timestamp(6) NULL,
                last_modified_by int4 NULL,
                is_deleted bool NOT NULL DEFAULT false,
                is_active bool NOT NULL DEFAULT true,
                deleted_date timestamp(6) NULL,
                deleted_by int4 NULL,
                CONSTRAINT pasien_satusehat_m_pkey PRIMARY KEY (id)
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230515_073455_migrate_ga_284_create_table_pasien_satusehat_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230515_073455_migrate_ga_284_create_table_pasien_satusehat_m cannot be reverted.\n";

        return false;
    }
    */
}
