<?php

use yii\db\Migration;

/**
 * Class m200911_114821_migrate_20200911_anamnesadetail
 */
class m200911_114821_migrate_20200911_anamnesadetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."anamnesadetail_t" (
  "anamnesadetail_id" serial4,
  "anamnesa_id" int4 NOT NULL,
  "diagnosa_keperawatan" text COLLATE "pg_catalog"."default",
  "tujuan_terukur" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "pk_anamnesadetail_t" PRIMARY KEY ("anamnesadetail_id")
)
;');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200911_114821_migrate_20200911_anamnesadetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200911_114821_migrate_20200911_anamnesadetail cannot be reverted.\n";

        return false;
    }
    */
}
