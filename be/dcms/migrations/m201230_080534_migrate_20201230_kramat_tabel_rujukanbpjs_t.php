<?php

use yii\db\Migration;

/**
 * Class m201230_080534_migrate_20201230_kramat_tabel_rujukanbpjs_t
 */
class m201230_080534_migrate_20201230_kramat_tabel_rujukanbpjs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('CREATE TABLE IF NOT EXISTS "public"."rujukanbpjs_t" (
  "rujukanbpjs_id" serial8,
  "pendaftaran_id" int4,
  "pasienadmisi_id" int4,
  "instalasi_id" int4,
  "diagnosa_id" int4,
  "perujuk_id" int4,
  "kelaspelayanan_id" int4,
  "tanggal_rujukan" timestamp(6),
  "no_rujukan" varchar(50) COLLATE "pg_catalog"."default",
  "rujukan" varchar(50) COLLATE "pg_catalog"."default",
  "spesialis" varchar(255) COLLATE "pg_catalog"."default",
  "catatan_rujukan" text COLLATE "pg_catalog"."default",
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
  "bpjs_id" int4,
  "jenis_pelayanan_bpjs" varchar(50) COLLATE "pg_catalog"."default",
  "dirujukke" varchar(50) COLLATE "pg_catalog"."default",
  "additional_request" text COLLATE "pg_catalog"."default",
  "poli_rujukan" varchar(50) COLLATE "pg_catalog"."default",
  "diagnosa_rujukan" varchar(50) COLLATE "pg_catalog"."default",
  "diagnosa_rujukan_nama" text COLLATE "pg_catalog"."default",
  "dirujukke_nama" varchar(255) COLLATE "pg_catalog"."default",
  CONSTRAINT "rujukanbpjs_t_pkey" PRIMARY KEY ("rujukanbpjs_id")
)
;');

$this->execute('ALTER TABLE "public"."rujukanbpjs_t" OWNER TO "postgres";');

$this->execute('COMMENT ON COLUMN "public"."rujukanbpjs_t"."instalasi_id" IS \'instalasi_m\';');

$this->execute('COMMENT ON COLUMN "public"."rujukanbpjs_t"."diagnosa_id" IS \'diagnosa_m\';');

$this->execute('COMMENT ON COLUMN "public"."rujukanbpjs_t"."perujuk_id" IS \'perujuk_m\';');

$this->execute('COMMENT ON COLUMN "public"."rujukanbpjs_t"."kelaspelayanan_id" IS \'kelaspelayanan_m\';');

$this->execute('COMMENT ON COLUMN "public"."rujukanbpjs_t"."rujukan" IS \'lookup_type=\'\'rujukan\'\'\';');

$this->execute('COMMENT ON COLUMN "public"."rujukanbpjs_t"."bpjs_id" IS \'bpjs_m\';');

$this->execute('COMMENT ON COLUMN "public"."rujukanbpjs_t"."jenis_pelayanan_bpjs" IS \'lookup_type=\'\'jenis_pelayanan_bpjs\'\'\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201230_080534_migrate_20201230_kramat_tabel_rujukanbpjs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201230_080534_migrate_20201230_kramat_tabel_rujukanbpjs_t cannot be reverted.\n";

        return false;
    }
    */
}
