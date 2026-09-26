<?php

use yii\db\Migration;

/**
 * Class m220707_040532_migrate_migrate_mhg1742_create_table_atccode
 */
class m220707_040532_migrate_migrate_mhg1742_create_table_atccode extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
			
			CREATE TABLE IF NOT EXISTS obatalkes_atccode (
			  atccode_id serial8 NOT NULL,
			  level_atccode varchar(50) COLLATE pg_catalog.default,
			  classification_atccode text COLLATE pg_catalog.default NOT NULL,
			  atccode varchar(25) COLLATE pg_catalog.default NOT NULL,
			  created_date timestamp(6) NOT NULL DEFAULT ('now'::text)::date,
			  created_by int4,
			  modified_count int4,
			  last_modified_date timestamp(6),
			  last_modified_by int4,
			  is_deleted bool NOT NULL DEFAULT false,
			  is_active bool NOT NULL DEFAULT true,
			  deleted_date timestamp(6),
			  deleted_by int4,
			  CONSTRAINT obatalkes_atccode_pkey PRIMARY KEY (atccode_id))"
			);
			
	        $this->execute('
			ALTER TABLE public.obatalkes_atccode
			  OWNER TO postgres;
	        ');
			
	        $this->execute('
				ALTER TABLE "public"."obatalkes_m" ADD COLUMN IF NOT EXISTS  "atccode_id" int4;
	        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220707_040532_migrate_migrate_mhg1742_create_table_atccode cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220707_040532_migrate_migrate_mhg1742_create_table_atccode cannot be reverted.\n";

        return false;
    }
    */
}
