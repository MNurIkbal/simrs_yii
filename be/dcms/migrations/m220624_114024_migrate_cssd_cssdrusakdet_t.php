<?php

use yii\db\Migration;

/**
 * Class m220624_114024_migrate_cssd_cssdrusakdet_t
 */
class m220624_114024_migrate_cssd_cssdrusakdet_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE "public"."cssdrusakdet_t" (
          "cssdrusakdet_id" serial8,
          "cssdrusak_id" int4,
          "barangalkes_id" int4,
          "satuanunit_id" int4,
          "qty" int4,
          "catatan" text COLLATE "pg_catalog"."default",
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
          "barangalkes_kode" varchar(50) COLLATE "pg_catalog"."default",
          "is_alkes" bool DEFAULT true,
          "total" int4,
          "qty_rusak" int4,
          CONSTRAINT "cssdrusakdet_t_pkey" PRIMARY KEY ("cssdrusakdet_id")
      )
      ;
      ');

        $this->execute('ALTER TABLE "public"."cssdrusakdet_t" 
          OWNER TO "postgres";
          ');

        $this->execute('COMMENT ON COLUMN "public"."cssdrusakdet_t"."is_alkes" IS \'flaging pembeda barang atau alkes\';
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_114024_migrate_cssd_cssdrusakdet_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_114024_migrate_cssd_cssdrusakdet_t cannot be reverted.\n";

        return false;
    }
    */
}
