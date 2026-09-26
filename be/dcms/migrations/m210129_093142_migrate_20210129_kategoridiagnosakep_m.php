<?php

use yii\db\Migration;

/**
 * Class m210129_093142_migrate_20210129_kategoridiagnosakep_m
 */
class m210129_093142_migrate_20210129_kategoridiagnosakep_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."kategoridiagnosakep_m" (
              "kategoridiagnosakep_id" serial8 NOT NULL PRIMARY KEY,
              "kategoridiagnosakep_nama" varchar(100) COLLATE "pg_catalog"."default",
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4
            );
        ');

         $this->execute('ALTER TABLE "public"."kategoridiagnosakep_m" ADD COLUMN IF NOT EXISTS "kategoridiagnosakep_kode" varchar(20) COLLATE "pg_catalog"."default";
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210129_093142_migrate_20210129_kategoridiagnosakep_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210129_093142_migrate_20210129_kategoridiagnosakep_m cannot be reverted.\n";

        return false;
    }
    */
}
