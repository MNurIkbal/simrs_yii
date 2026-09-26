<?php

use yii\db\Migration;

/**
 * Class m210618_105037_oddo_updatebill_20210618
 */
class m210618_105037_oddo_updatebill_20210618 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN if not exists "edit_billing" bool;');

        $this->execute('CREATE TABLE if not exists "public"."logpelayanan_r" (
  "id" serial8,
  "is_tindakan" bool NOT NULL,
  "pelayanan_id" int4 NOT NULL,
  "hargasatuan_sebelum" float8 DEFAULT 0,
  "hargasatuan_sesudah" float8 DEFAULT 0,
  "hargacyto_sebelum" float8 DEFAULT 0,
  "hargacyto_sesudah" float8 DEFAULT 0,
  "hargapenyulit_sebelum" float8 DEFAULT 0,
  "hargapenyulit_sesudah" float8 DEFAULT 0,
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
  "verify_by" int4,
  CONSTRAINT "logpelayanan_r_pkey" PRIMARY KEY ("id")
)
;');
        $this->execute('ALTER TABLE "public"."logpelayanan_r" OWNER TO "postgres";');
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210618_105037_oddo_updatebill_20210618 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210618_105037_oddo_updatebill_20210618 cannot be reverted.\n";

        return false;
    }
    */
}
