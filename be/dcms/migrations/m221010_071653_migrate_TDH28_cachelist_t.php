<?php

use yii\db\Migration;

/**
 * Class m221010_071653_migrate_TDH28_cachelist_t
 */
class m221010_071653_migrate_TDH28_cachelist_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.cachelist_t (
            cache_id serial4 NOT NULL,
            cache_name varchar NOT NULL,
            cache_info text NULL,
            cache_count int4 NOT NULL DEFAULT 0,
            additional_data text NULL,
            created_date timestamp NOT NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp NULL,
            deleted_by int4 NULL,
            CONSTRAINT cachelist_t_pk PRIMARY KEY (cache_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221010_071653_migrate_TDH28_cachelist_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221010_071653_migrate_TDH28_cachelist_t cannot be reverted.\n";

        return false;
    }
    */
}
