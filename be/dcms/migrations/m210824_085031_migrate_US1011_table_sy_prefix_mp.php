<?php

use yii\db\Migration;

/**
 * Class m210824_085031_migrate_US1011_table_sy_prefix_mp
 */
class m210824_085031_migrate_US1011_table_sy_prefix_mp extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."sy_prefix_mp" (
          "id" serial8,
          "instalasi_id" int4,
          "penomoran_id" int4,
          "nama_prefix" varchar(50) COLLATE "pg_catalog"."default",
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          CONSTRAINT "sy_prefix_mp_pkey" PRIMARY KEY ("id")
          );
        ');

        $this->execute('ALTER TABLE "public"."sy_prefix_mp" 
          OWNER TO "postgres";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210824_085031_migrate_US1011_table_sy_prefix_mp cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210824_085031_migrate_US1011_table_sy_prefix_mp cannot be reverted.\n";

        return false;
    }
    */
}
