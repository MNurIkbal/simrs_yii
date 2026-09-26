<?php

use yii\db\Migration;

/**
 * Class m241104_115001_migrate_PCP94_programterapiapprove_t
 */
class m241104_115001_migrate_PCP94_programterapiapprove_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("CREATE TABLE IF NOT EXISTS public.programterapiapprove_t (
	programterapiapprove_id serial4 NOT NULL,
	programterapi_id int4 NULL,
	is_approve bool NULL,
	created_date timestamp(6) DEFAULT 'now'::text::date NOT NULL,
	created_by int4 NULL,
	modified_count int4 NULL,
	last_modified_date timestamp(6) NULL,
	last_modified_by int4 NULL,
	is_deleted bool DEFAULT false NOT NULL,
	is_active bool DEFAULT true NOT NULL,
	deleted_date timestamp(6) NULL,
	deleted_by int4 NULL,
	CONSTRAINT programterapiapprove_t_pkey PRIMARY KEY (programterapiapprove_id)
);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241104_115001_migrate_PCP94_programterapiapprove_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241104_115001_migrate_PCP94_programterapiapprove_t cannot be reverted.\n";

        return false;
    }
    */
}
