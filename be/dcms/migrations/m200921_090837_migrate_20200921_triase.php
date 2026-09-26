<?php

use yii\db\Migration;

/**
 * Class m200921_090837_migrate_20200921_triase
 */
class m200921_090837_migrate_20200921_triase extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."triase_t" (
  "triase_id" serial4,
  "pendaftaran_id" int4,
  "dokter_id" int4,
  "perawat_id" int4,
  "tgl_triase" timestamp(6),
  "keluhan_utama" text COLLATE "pg_catalog"."default",
  "tekanan_darah" varchar(30) COLLATE "pg_catalog"."default",
  "nadi" varchar(30) COLLATE "pg_catalog"."default",
  "nafas" varchar(30) COLLATE "pg_catalog"."default",
  "suhu" varchar(30) COLLATE "pg_catalog"."default",
  "is_alergi" bool,
  "alergi_obat" text COLLATE "pg_catalog"."default",
  "alergi_lainnya" text COLLATE "pg_catalog"."default",
  "is_trauma" bool,
  "jalan_nafas" text COLLATE "pg_catalog"."default",
  "pernafasan" text COLLATE "pg_catalog"."default",
  "sirkulasi" text COLLATE "pg_catalog"."default",
  "gcseye_id" int4,
  "gcsverbal_id" int4,
  "gcsmotorik_id" int4,
  "is_kapitis" bool,
  "hasil_gcs" text COLLATE "pg_catalog"."default",
  "waktu_respon" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "pk_triase_t" PRIMARY KEY ("triase_id")
)
;');

        $this->execute('ALTER TABLE "public"."triase_t" OWNER TO "postgres";');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200921_090837_migrate_20200921_triase cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200921_090837_migrate_20200921_triase cannot be reverted.\n";

        return false;
    }
    */
}
