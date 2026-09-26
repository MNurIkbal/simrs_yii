<?php

use yii\db\Migration;

/**
 * Class m210427_020735_migrate_20210427_generatedpo_t
 */
class m210427_020735_migrate_20210427_generatedpo_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE "public"."generatedpo_t" (
  "generatedpo_id" serial8,
  "tipe" varchar(100) COLLATE "pg_catalog"."default",
  "prdetail_id" int4,
  "podetail_id" int4,
  "status" int4,
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
  CONSTRAINT "generatedpo_t_pkey" PRIMARY KEY ("generatedpo_id")
)
;
');
        $this->execute('COMMENT ON COLUMN "public"."generatedpo_t"."tipe" IS \'obat/barang\';');
        
        $this->execute('ALTER TABLE "public"."generatedpo_t" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210427_020735_migrate_20210427_generatedpo_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210427_020735_migrate_20210427_generatedpo_t cannot be reverted.\n";

        return false;
    }
    */
}
