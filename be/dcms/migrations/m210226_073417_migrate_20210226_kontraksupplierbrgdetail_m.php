<?php

use yii\db\Migration;

/**
 * Class m210226_073417_migrate_20210226_kontraksupplierbrgdetail_m
 */
class m210226_073417_migrate_20210226_kontraksupplierbrgdetail_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE if not exists "public"."kontraksupplierbrgdetail_m" (
  "kontraksupplierbrgdetail_id" serial8,
  "kontraksupplierbrg_id" int4,
  "barang_id" int4,
  "kode_barang" varchar(255) COLLATE "pg_catalog"."default",
  "nama_barang" varchar(255) COLLATE "pg_catalog"."default",
  "satuankecil_id" int4,
  "satuankonv1_id" int4,
  "satuankonv2_id" int4,
  "harga" numeric(15,2) DEFAULT 0,
  "pengurang" numeric(15,2) DEFAULT 0,
  "qty_min" numeric(15,2),
  "penambah" numeric(15,2),
  "total_harga" numeric(15,2),
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
  CONSTRAINT "kontraksupplierbrgdetail_m_pkey" PRIMARY KEY ("kontraksupplierbrgdetail_id")
)
;');
        $this->execute('ALTER TABLE "public"."kontraksupplierbrgdetail_m" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_073417_migrate_20210226_kontraksupplierbrgdetail_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_073417_migrate_20210226_kontraksupplierbrgdetail_m cannot be reverted.\n";

        return false;
    }
    */
}
