<?php

use yii\db\Migration;

/**
 * Class m220423_215239_migrate_MHG764_table_mims_master
 */
class m220423_215239_migrate_MHG764_table_mims_master extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
		CREATE TABLE IF NOT EXISTS "public"."obatalkesmims_m" (
		  "obatalkesmims_id" serial NOT NULL ,
		  "parent_id" int4 NOT NULL,
		  "obatalkesmims_nama" varchar(100) COLLATE "pg_catalog"."default",
		  "catatan" varchar(100) COLLATE "pg_catalog"."default",
		  "is_deleted" bool NOT NULL DEFAULT false,
		  "is_active" bool NOT NULL DEFAULT true,
		  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
		  "created_by" int4,
		  "modified_count" int4,
		  "last_modified_date" timestamp(6),
		  "last_modified_by" int4,
		  "deleted_date" timestamp(6),
		  "deleted_by" int4,
		  CONSTRAINT "obatalkesmims_m_pkey" PRIMARY KEY ("obatalkesmims_id")
		)
		;
			');
		
		$this->execute('ALTER TABLE "public"."obatalkesmims_m" OWNER TO "postgres";');
		
		
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220423_215239_migrate_MHG764_table_mims_master cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220423_215239_migrate_MHG764_table_mims_master cannot be reverted.\n";

        return false;
    }
    */
}
