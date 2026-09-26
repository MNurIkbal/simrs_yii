<?php

use yii\db\Migration;

/**
 * Class m210226_073117_migrate_20210226_kontraksupplierbrg_m
 */
class m210226_073117_migrate_20210226_kontraksupplierbrg_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE if not exists "public"."kontraksupplierbrg_m" (
  "kontraksupplierbrg_id" serial8,
  "supplier_id" int4,
  "payterm_id" int4,
  "jumlah_hari" int4,
  "pajak_id" int4,
  "persen_ppn" numeric(15,2) DEFAULT 0,
  "kontraksupplier_no" varchar(100) COLLATE "pg_catalog"."default",
  "tgl_berlaku" date,
  "metode_bayar" varchar(255) COLLATE "pg_catalog"."default",
  "catatan" varchar(255) COLLATE "pg_catalog"."default",
  "dikirim_ke" varchar(64) COLLATE "pg_catalog"."default",
  "contact_person" varchar(64) COLLATE "pg_catalog"."default",
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
  CONSTRAINT "kontraksupplierbrg_m_pkey" PRIMARY KEY ("kontraksupplierbrg_id")
)
;');

        $this->execute('ALTER TABLE "public"."kontraksupplierbrg_m" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_073117_migrate_20210226_kontraksupplierbrg_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_073117_migrate_20210226_kontraksupplierbrg_m cannot be reverted.\n";

        return false;
    }
    */
}
