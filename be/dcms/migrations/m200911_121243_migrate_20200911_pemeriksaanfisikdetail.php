<?php

use yii\db\Migration;

/**
 * Class m200911_121243_migrate_20200911_pemeriksaanfisikdetail
 */
class m200911_121243_migrate_20200911_pemeriksaanfisikdetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('CREATE TABLE IF NOT EXISTS "public"."pemeriksaanfisikdetail_t" (
  "pemeriksaanfisikdetail_id" serial4,
  "pemeriksaanfisik_id" int4 NOT NULL,
  "masalah_diagnosa_medis" text COLLATE "pg_catalog"."default",
  "rencana_laksana_medis" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "pk_pemeriksaanfisikdetail_t" PRIMARY KEY ("pemeriksaanfisikdetail_id")
)
;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200911_121243_migrate_20200911_pemeriksaanfisikdetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200911_121243_migrate_20200911_pemeriksaanfisikdetail cannot be reverted.\n";

        return false;
    }
    */
}
