<?php

use yii\db\Migration;

/**
 * Class m210226_065805_migrate_20210226_purchasereqbrgdetail_t
 */
class m210226_065805_migrate_20210226_purchasereqbrgdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
   
    $this->execute('CREATE TABLE if not exists "public"."purchasereqbrgdetail_t" (
  "purchasereqbrgdetail_id" serial8,
  "purchasereqbrg_id" int4,
  "barang_id" int4,
  "qty_input" numeric(15,2),
  "qty_konversi" numeric(15,2),
  "satuan_id" int4,
  "satuankonversi_id" int4,
  "catatan" text COLLATE "pg_catalog"."default",
  "status" int2 DEFAULT 712,
  "alasan" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "purchasereqbrgdetail_t_pkey" PRIMARY KEY ("purchasereqbrgdetail_id")
)
;');

    $this->execute('ALTER TABLE "public"."purchasereqbrgdetail_t" OWNER TO "postgres";');

    $this->execute('COMMENT ON COLUMN "public"."purchasereqbrgdetail_t"."status" IS \'lookup_type=status_purchaserequest\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_065805_migrate_20210226_purchasereqbrgdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_065805_migrate_20210226_purchasereqbrgdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
