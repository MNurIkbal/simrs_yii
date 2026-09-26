<?php

use yii\db\Migration;

/**
 * Class m210221_084955_migrate_20210221_jpk_m
 */
class m210221_084955_migrate_20210221_jpk_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."jpk_m" (
  "jpk_id" serial8,
  "daftartindakan_id" int4,
  "is_rujuk" bool DEFAULT false,
  "is_harian" bool DEFAULT false,
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
  CONSTRAINT "lookupjpk_m_pkey" PRIMARY KEY ("jpk_id")
)
;');
        $this->execute('ALTER TABLE "public"."jpk_m" OWNER TO "postgres";');
   

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210221_084955_migrate_20210221_jpk_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210221_084955_migrate_20210221_jpk_m cannot be reverted.\n";

        return false;
    }
    */
}
