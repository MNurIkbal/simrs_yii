<?php

use yii\db\Migration;

/**
 * Class m220624_092255_migrate_cssd_cssd_t
 */
class m220624_092255_migrate_cssd_cssd_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssd_t" (
          "cssd_id" serial8,
          "ruanganasal_id" int4 NOT NULL,
          "ruangantujuan_id" int4 NOT NULL,
          "tgl_pengajuan_sterilisasi" timestamp(6) NOT NULL,
          "no_pengajuan_sterilisasi" varchar(16) COLLATE "pg_catalog"."default",
          "status_cssd" int4,
          "no_proses_sterilisasi" varchar(16) COLLATE "pg_catalog"."default",
          "tgl_terima" timestamp(6),
          "peg_mengetahui_id" int4,
          "peg_menyetujui_id" int4,
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
          "tgl_terima_unit" timestamp(6),
          "peg_menerima_id" int4,
          CONSTRAINT "cssd_t _pkey" PRIMARY KEY ("cssd_id")
          )
          ;
        ');

        $this->execute('ALTER TABLE "public"."cssd_t" 
          OWNER TO "postgres";
          ');

        $this->execute('
            ALTER TABLE "public"."cssd_t" 
            ADD COLUMN IF NOT EXISTS "cssdsterilisasi_id" int4,
            ADD COLUMN IF NOT EXISTS "alasan_batal" varchar(255) COLLATE "pg_catalog"."default",
            ADD COLUMN IF NOT EXISTS "peg_batal_id" int4,
            ADD COLUMN IF NOT EXISTS "tgl_batal" timestamp(6),
            ADD COLUMN IF NOT EXISTS "tgl_pengiriman" timestamp(6);
        ');

        $this->execute('COMMENT ON COLUMN "public"."cssd_t"."status_cssd" IS \'lookup_type = status_cssd\';
        ');

        $this->execute('
          CREATE TRIGGER "no_pengajuan_sterilisasi" BEFORE INSERT ON "public"."cssd_t"
          FOR EACH ROW
          EXECUTE PROCEDURE "public"."no_pengajuan_sterilisasi"();
          ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_092255_migrate_cssd_cssd_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_092255_migrate_cssd_cssd_t cannot be reverted.\n";

        return false;
    }
    */
}
