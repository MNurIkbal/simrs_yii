<?php

use yii\db\Migration;

/**
 * Class m220624_112011_migrate_cssd_cssdpengirimandet_t
 */
class m220624_112011_migrate_cssd_cssdpengirimandet_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdpengirimandet_t" (
          "cssdpengirimandet_id" serial8,
          "cssdpengiriman_id" int4,
          "qty" int4,
          "catatan" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          "barangalkes_id" int4,
          "cssddet_id" int8,
          CONSTRAINT "cssdpengirimandet_t_pkey" PRIMARY KEY ("cssdpengirimandet_id")
          )
          ;
          ');

       $this->execute('ALTER TABLE "public"."cssdpengirimandet_t" 
          OWNER TO "postgres";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_112011_migrate_cssd_cssdpengirimandet_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_112011_migrate_cssd_cssdpengirimandet_t cannot be reverted.\n";

        return false;
    }
    */
}
