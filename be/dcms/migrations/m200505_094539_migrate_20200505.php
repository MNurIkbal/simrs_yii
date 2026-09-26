<?php

use yii\db\Migration;

/**
 * Class m200505_094539_migrate_20200505
 */
class m200505_094539_migrate_20200505 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE "public"."riwayat_m" (
  "riwayat_id" serial8,
  "riwayat_nama" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "riwayat_m_pkey" PRIMARY KEY ("riwayat_id")
)
;');

        $this->execute('CREATE TABLE "public"."riwayatpenyakit_t" (
  "riwayatpenyakit_id" serial8,
  "pendaftaran_id" int4 NOT NULL,
  "riwayat" text COLLATE "pg_catalog"."default",
  "riwayat_lainnya" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "riwayatpenyakit_t_pkey" PRIMARY KEY ("riwayatpenyakit_id")
)
;
');
        $this->execute('COMMENT ON COLUMN "public"."riwayatpenyakit_t"."riwayat" IS \'[{"nama_riwayat":"abc","flag":"TRUE"},{"nama_riwayat":"def","flag":"FALSE"}]\';');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200505_094539_migrate_20200505 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200505_094539_migrate_20200505 cannot be reverted.\n";

        return false;
    }
    */
}
