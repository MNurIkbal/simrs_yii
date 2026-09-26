<?php

use yii\db\Migration;

/**
 * Class m210226_064817_migrate_20210226_purchasereqbrg_t
 */
class m210226_064817_migrate_20210226_purchasereqbrg_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute('CREATE TABLE if not exists "public"."purchasereqbrg_t" (
  "purchasereqbrg_id" serial8,
  "no_pr" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_pr" date,
  "ruangan_id" int4,
  "pegawai_id" int4,
  "status" int2,
  "reference" text COLLATE "pg_catalog"."default",
  "is_prcyto" bool DEFAULT false,
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
  CONSTRAINT "purchasereqbrg_t_pkey" PRIMARY KEY ("purchasereqbrg_id")
)
;
');

    $this->execute('ALTER TABLE "public"."purchasereqbrg_t" OWNER TO "postgres";');

    $this->execute('COMMENT ON COLUMN "public"."purchasereqbrg_t"."status" IS \'lookup_type=\'\'status_purchaserequest\'\'\';');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_064817_migrate_20210226_purchasereqbrg_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_064817_migrate_20210226_purchasereqbrg_t cannot be reverted.\n";

        return false;
    }
    */
}
