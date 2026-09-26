<?php

use yii\db\Migration;

/**
 * Class m231218_065231_migrate_skema_intgrasiesiantri_table_mapping_poli_m
 */
class m231218_065231_migrate_skema_intgrasiesiantri_table_mapping_poli_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute('
		CREATE TABLE if not EXISTS "public"."mapping_poli_m" (
			  "mapping_poli_id" serial4 NOT NULL,
			  "kodepoli_map" varchar COLLATE "pg_catalog"."default",
			  "kodepoli_id" varchar COLLATE "pg_catalog"."default",
			  "nama" varchar COLLATE "pg_catalog"."default",
			  "ruangan_id" int4 NOT NULL,
			  "additional_data" text COLLATE "pg_catalog"."default",
			  "created_date" abstime,
			  "created_by" int4,
			  "modified_count" int4,
			  "last_modified_date" timestamp(6),
			  "last_modified_by" int4,
			  "is_active" bool,
			  "deleted_date" timestamp(6),
			  "deleted_by" int4,
			  "is_deleted" bool,
			  CONSTRAINT "mapping_poli_m_pk" PRIMARY KEY ("mapping_poli_id")
			);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231218_065231_migrate_skema_intgrasiesiantri_table_mapping_poli_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231218_065231_migrate_skema_intgrasiesiantri_table_mapping_poli_m cannot be reverted.\n";

        return false;
    }
    */
}
