<?php

use yii\db\Migration;

/**
 * Class m220718_101633_migrate_BIN_anestesiintraoprvisign_t
 */
class m220718_101633_migrate_BIN_anestesiintraoprvisign_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.anestesiintraoprvisign_t (
            anestesiintraoprvisign_id serial8 NOT NULL,
            anestesiintraopr_id int4 NOT NULL,
            \"time\" time NOT NULL,
            rr int4 NULL,
            hr int4 NULL,
            systolic int4 NULL,
            diastolic int4 NULL,
            additional_data text NULL,
            created_date timestamp(6) NULL DEFAULT 'now'::text::date,
            created_by int4 NULL,
            modified_count int4 NULL,
            last_modified_date timestamp(6) NULL,
            last_modified_by int4 NULL,
            is_deleted bool NOT NULL DEFAULT false,
            is_active bool NOT NULL DEFAULT true,
            deleted_date timestamp(6) NULL,
            deleted_by int4 NULL,
            CONSTRAINT anestesiintraoprvisign_t_pkey PRIMARY KEY (anestesiintraoprvisign_id)
        );");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220718_101633_migrate_BIN_anestesiintraoprvisign_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220718_101633_migrate_BIN_anestesiintraoprvisign_t cannot be reverted.\n";

        return false;
    }
    */
}
