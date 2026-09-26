<?php

use yii\db\Migration;

/**
 * Class m210329_093557_migrate_20210329_laporanoperasi_r
 */
class m210329_093557_migrate_20210329_laporanoperasi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE "public"."laporanoperasi_r" (
  "laporanoperasi_id" serial8,
  "mulai_operasi" timestamp(6),
  "selesai_operasi" timestamp(6),
  "lama_pembedahan" varchar(255) COLLATE "pg_catalog"."default",
  "dokter_bedah" varchar(255) COLLATE "pg_catalog"."default",
  "asisten" varchar(255) COLLATE "pg_catalog"."default",
  "asisten_instrumen" varchar(255) COLLATE "pg_catalog"."default",
  "kategori_operasi" varchar(255) COLLATE "pg_catalog"."default",
  "diagnosis_prabedah" text COLLATE "pg_catalog"."default",
  "nama_prosedur" text COLLATE "pg_catalog"."default",
  "diagnosis_paskabedah" text COLLATE "pg_catalog"."default",
  "dokte_anastesi" varchar(255) COLLATE "pg_catalog"."default",
  "cara_pembiusan" text COLLATE "pg_catalog"."default",
  "posisi_pasien" text COLLATE "pg_catalog"."default",
  "mulai_pembiusan" timestamp(6),
  "selesai_pembiusan" timestamp(6),
  "uraian" text COLLATE "pg_catalog"."default",
  "komplikasi" text COLLATE "pg_catalog"."default",
  "perdarahan" text COLLATE "pg_catalog"."default",
  "is_kirimkepatologi" bool,
  "asal_jaringan" text COLLATE "pg_catalog"."default",
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
  "pasienmasukpenunjang_id" int4,
  CONSTRAINT "laporanoperasi_r_pkey" PRIMARY KEY ("laporanoperasi_id")
)
;');
        $this->execute('ALTER TABLE "public"."laporanoperasi_r" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210329_093557_migrate_20210329_laporanoperasi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210329_093557_migrate_20210329_laporanoperasi_r cannot be reverted.\n";

        return false;
    }
    */
}
