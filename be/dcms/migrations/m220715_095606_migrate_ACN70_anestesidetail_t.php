<?php

use yii\db\Migration;

/**
 * Class m220715_095606_migrate_ACN70_anestesidetail_t
 */
class m220715_095606_migrate_ACN70_anestesidetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.anestesidetail_t (
            anestesidetail_id serial8 NOT NULL,
            anestesi_id int4 NULL,
            obatalkes_id int4 NULL,
            dose varchar(100) NULL,
            time_delivery time NULL,
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
            CONSTRAINT anestesidetail_t_pkey PRIMARY KEY (anestesidetail_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_095606_migrate_ACN70_anestesidetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_095606_migrate_ACN70_anestesidetail_t cannot be reverted.\n";

        return false;
    }
    */
}
