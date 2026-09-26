<?php

use yii\db\Migration;

/**
 * Class m220624_095853_migrate_cssd_cssdsterilisasi_t
 */
class m220624_095853_migrate_cssd_cssdsterilisasi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdsterilisasi_t" (
          "cssdsterilisasi_id" serial8,
          "tgl_sterilisasi" timestamp(6),
          "no_sterilisasi" varchar(16) COLLATE "pg_catalog"."default",
          "status_cssd" int4,
          "tgl_sterilisasi_selesai" timestamp(6),
          "catatan" varchar(255) COLLATE "pg_catalog"."default",
          "peg_sterilisasi_id" int4,
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
          CONSTRAINT "cssdsterilisasi_t_pkey" PRIMARY KEY ("cssdsterilisasi_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."cssdsterilisasi_t" 
          OWNER TO "postgres";
          ');

        $this->execute('COMMENT ON COLUMN "public"."cssdsterilisasi_t"."status_cssd" IS \'lookup_type = status_cssd\';
        ');

        $this->execute('
            ALTER TABLE "public"."cssdsterilisasi_t" 
            ADD COLUMN IF NOT EXISTS "pegawai_mengetahui_id" varchar(255) COLLATE "pg_catalog"."default";
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_095853_migrate_cssd_cssdsterilisasi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_095853_migrate_cssd_cssdsterilisasi_t cannot be reverted.\n";

        return false;
    }
    */
}
