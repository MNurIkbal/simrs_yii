<?php

use yii\db\Migration;

/**
 * Class m220624_112419_migrate_cssd_cssdsterilisasi_r
 */
class m220624_112419_migrate_cssd_cssdsterilisasi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdsterilisasi_r" (
          "id" serial8,
          "cssdsterilisasi_id" int4,
          "peg_sterilisasi_id" int4,
          "status_cssd" int4,
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
          CONSTRAINT "cssdsterilisasi_r_pkey" PRIMARY KEY ("id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."cssdsterilisasi_r" 
            OWNER TO "postgres";
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_112419_migrate_cssd_cssdsterilisasi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_112419_migrate_cssd_cssdsterilisasi_r cannot be reverted.\n";

        return false;
    }
    */
}
