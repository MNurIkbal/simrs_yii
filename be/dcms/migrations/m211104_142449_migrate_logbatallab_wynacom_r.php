<?php

use yii\db\Migration;

/**
 * Class m211104_142449_migrate_logbatallab_wynacom_r
 */
class m211104_142449_migrate_logbatallab_wynacom_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('CREATE TABLE if not exists "public"."logbatallab_wynacom_r" (
  "logbatallab_wynacom_id" serial8,
  "status" text COLLATE "pg_catalog"."default",
  "ordernumber" varchar(32) COLLATE "pg_catalog"."default",
  "testid" varchar(32) COLLATE "pg_catalog"."default",
  "testname" varchar(64) COLLATE "pg_catalog"."default",
  "userid" varchar(32) COLLATE "pg_catalog"."default",
  "username" varchar(32) COLLATE "pg_catalog"."default",
  "reason" text COLLATE "pg_catalog"."default",
  "status_response" bool,
  "messages" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "logbatallab_wynacom_r_pkey" PRIMARY KEY ("logbatallab_wynacom_id")
)
;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211104_142449_migrate_logbatallab_wynacom_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211104_142449_migrate_logbatallab_wynacom_r cannot be reverted.\n";

        return false;
    }
    */
}
