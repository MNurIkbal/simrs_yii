<?php

use yii\db\Migration;

/**
 * Class m210409_033435_migrate_20210409_laporanendoskopi_r
 */
class m210409_033435_migrate_20210409_laporanendoskopi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('CREATE TABLE "public"."laporanendoskopi_r" (
  "laporanedoskopi_id" serial8,
  "pendaftaran_id" int4,
  "pasienmasukpenunjang_id" int4,
  "simptoms" text COLLATE "pg_catalog"."default",
  "pre_diagnosis" text COLLATE "pg_catalog"."default",
  "pre_diagnosis_sekunder" json,
  "indications_examinations" text COLLATE "pg_catalog"."default",
  "instrument" text COLLATE "pg_catalog"."default",
  "pre_medications" text COLLATE "pg_catalog"."default",
  "procedure_performed" json,
  "findings" text COLLATE "pg_catalog"."default",
  "sampling" text COLLATE "pg_catalog"."default",
  "endoscopic_diagnosis" text COLLATE "pg_catalog"."default",
  "endoscopic_diagnosis_sekunder" json,
  "recommendations" text COLLATE "pg_catalog"."default",
  "additional_photo" json,
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
  CONSTRAINT "laporanedoskopi_r_pkey" PRIMARY KEY ("laporanedoskopi_id")
)
;');
    $this->execute('ALTER TABLE "public"."laporanendoskopi_r" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210409_033435_migrate_20210409_laporanendoskopi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210409_033435_migrate_20210409_laporanendoskopi_r cannot be reverted.\n";

        return false;
    }
    */
}
