<?php

use yii\db\Migration;

/**
 * Class m210127_094924_migrate_20200127_int_freezebill_r
 */
class m210127_094924_migrate_20200127_int_freezebill_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."int_freezebill_r" (
  "id" serial8,
  "invoice_id" varchar(100) COLLATE "pg_catalog"."default",
  "invoice_date" date,
  "invoice_due_date" date,
  "invoice_number" varchar(255) COLLATE "pg_catalog"."default",
  "invoice_total" float8,
  "payer_id" int4,
  "payer_code" varchar(255) COLLATE "pg_catalog"."default",
  "payer_name" varchar(255) COLLATE "pg_catalog"."default",
  "payer_sync_id_api" varchar(100) COLLATE "pg_catalog"."default",
  "create_uid" int4,
  "create_by" varchar(255) COLLATE "pg_catalog"."default",
  "create_date" date,
  "write_uid" int4,
  "write_by" varchar(255) COLLATE "pg_catalog"."default",
  "write_date" date,
  "status" int2 DEFAULT 1,
  "additional_detail" json,
  "pendaftaran_id" varchar(255) COLLATE "pg_catalog"."default",
  CONSTRAINT "int_freezebill_r_pkey" PRIMARY KEY ("id")
)
;');
        $this->execute('COMMENT ON COLUMN "public"."int_freezebill_r"."status" IS \'0=unfreeze, 1=freeze\';');

        $this->execute('ALTER TABLE "public"."int_freezebill_r" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210127_094924_migrate_20200127_int_freezebill_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210127_094924_migrate_20200127_int_freezebill_r cannot be reverted.\n";

        return false;
    }
    */
}
