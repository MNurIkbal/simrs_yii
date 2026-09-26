<?php

use yii\db\Migration;

/**
 * Class m210105_094554_migrate_sy_20210105_tabel_penanggungbiaya_t
 */
class m210105_094554_migrate_sy_20210105_tabel_penanggungbiaya_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."penanggungbiaya_t" (
  "penanggungbiaya_id" serial8,
  "pasien_id" int4,
  "carabayar_id" int4,
  "penanggungbiaya_nama" varchar(100) COLLATE "pg_catalog"."default",
  "namabagian" varchar(50) COLLATE "pg_catalog"."default",
  "noindukkaryawan" varchar(50) COLLATE "pg_catalog"."default",
  "jpkm" varchar(50) COLLATE "pg_catalog"."default",
  "instansi" varchar(50) COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "penanggungbiaya_t_pkey" PRIMARY KEY ("penanggungbiaya_id")
)
;');
    $this->execute('ALTER TABLE "public"."penanggungbiaya_t" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210105_094554_migrate_sy_20210105_tabel_penanggungbiaya_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210105_094554_migrate_sy_20210105_tabel_penanggungbiaya_t cannot be reverted.\n";

        return false;
    }
    */
}
