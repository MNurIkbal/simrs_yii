<?php

use yii\db\Migration;

/**
 * Class m221031_115303_migrate_acn_190_skema_gt_akunting_t
 */
class m221031_115303_migrate_acn_190_skema_gt_akunting_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	        $this->execute('
	            CREATE TABLE IF NOT EXISTS "public"."gt_akuntansi_t" (
  "id" serial8 NOT NULL ,
  "no_referensi" varchar(30) COLLATE "pg_catalog"."default",
  "is_send" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
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
  "sync_respon" text COLLATE "pg_catalog"."default",
  "type_account" text COLLATE "pg_catalog"."default",
  CONSTRAINT "gt_akuntansi_t_pkey" PRIMARY KEY ("id")
);       ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221031_115303_migrate_acn_190_skema_gt_akunting_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221031_115303_migrate_acn_190_skema_gt_akunting_t cannot be reverted.\n";

        return false;
    }
    */
}
