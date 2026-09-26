<?php

use yii\db\Migration;

/**
 * Class m210325_040312_migrate_20210325_zataktif_m
 */
class m210325_040312_migrate_20210325_zataktif_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE "public"."zataktif_m" (
  "zataktif_id" serial8,
  "zataktif_kode" varchar(100) COLLATE "pg_catalog"."default",
  "zataktif_nama" varchar(250) COLLATE "pg_catalog"."default",
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "zataktif_m_pkey" PRIMARY KEY ("zataktif_id")
)
;');
        $this->execute('ALTER TABLE "public"."zataktif_m" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210325_040312_migrate_20210325_zataktif_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210325_040312_migrate_20210325_zataktif_m cannot be reverted.\n";

        return false;
    }
    */
}
