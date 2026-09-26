<?php

use yii\db\Migration;

/**
 * Class m220624_113649_migrate_cssd_cssdbatal_r
 */
class m220624_113649_migrate_cssd_cssdbatal_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdbatal_r" (
          "cssdbatal_id" serial8,
          "cssd_id" int4,
          "tgl_cssdbatal" timestamp(6) NOT NULL,
          "status_cssdbatal" int4,
          "peg_cssdbatal_id" int4,
          "alasan_cssdbatal" text COLLATE "pg_catalog"."default",
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
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."cssdbatal_r" 
          OWNER TO "postgres";
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_113649_migrate_cssd_cssdbatal_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_113649_migrate_cssd_cssdbatal_r cannot be reverted.\n";

        return false;
    }
    */
}
