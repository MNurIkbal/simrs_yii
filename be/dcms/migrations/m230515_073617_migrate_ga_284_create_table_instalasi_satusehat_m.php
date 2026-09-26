<?php

use yii\db\Migration;

/**
 * Class m230515_073617_migrate_ga_284_create_table_instalasi_satusehat_m
 */
class m230515_073617_migrate_ga_284_create_table_instalasi_satusehat_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE IF NOT EXISTS public.instalasi_satusehat_m (
                id serial8 NOT NULL,
                instalasi_id int8 NOT NULL,
                satusehat_instalasi_id text NULL,
                satusehat_integration_id int8 NULL,
                created_date timestamp(6) NOT NULL DEFAULT 'now'::text::date,
                created_by int4 NULL,
                updated_date timestamp(6) NULL,
                updated_by int4 NULL,
                is_deleted bool NOT NULL DEFAULT false,
                is_active bool NOT NULL DEFAULT true,
                deleted_date timestamp(6) NULL,
                deleted_by int4 NULL,
                CONSTRAINT instalasi_satusehat_m_pkey PRIMARY KEY (id)
            );
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230515_073617_migrate_ga_284_create_table_instalasi_satusehat_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230515_073617_migrate_ga_284_create_table_instalasi_satusehat_m cannot be reverted.\n";

        return false;
    }
    */
}
