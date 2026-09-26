<?php

use yii\db\Migration;

/**
 * Class m220624_100124_migrate_cssd_cssdsterilisasidet_t
 */
class m220624_100124_migrate_cssd_cssdsterilisasidet_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdsterilisasidet_t" (
          "cssdsterilisasidet_id" serial8,
          "cssdsterilisasi_id" int4,
          "cssd_id" int4,
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
          CONSTRAINT "cssdsterilisasidet_t_pkey" PRIMARY KEY ("cssdsterilisasidet_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."cssdsterilisasidet_t" 
          OWNER TO "postgres";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_100124_migrate_cssd_cssdsterilisasidet_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_100124_migrate_cssd_cssdsterilisasidet_t cannot be reverted.\n";

        return false;
    }
    */
}
