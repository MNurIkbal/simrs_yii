<?php

use yii\db\Migration;

/**
 * Class m220624_095349_migrate_cssd_cssddet_t
 */
class m220624_095349_migrate_cssd_cssddet_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssddet_t" (
            "cssddet_id" serial8,
            "cssd_id" int4,
            "barangalkes_id" int4,
            "satuanunit_id" int4,
            "stok" int4,
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
            CONSTRAINT "cssddet_t_pkey" PRIMARY KEY ("cssddet_id")
            )
            ;
            ');

        $this->execute('
            ALTER TABLE "public"."cssddet_t" 
            ADD COLUMN IF NOT EXISTS "is_alkes" bool DEFAULT true;
            ');

        $this->execute('ALTER TABLE "public"."cssddet_t" 
          OWNER TO "postgres";
          ');

        $this->execute('COMMENT ON COLUMN "public"."cssddet_t"."is_alkes" IS \'flaging pembeda barang atau alkes\';
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_095349_migrate_cssd_cssddet_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_095349_migrate_cssd_cssddet_t cannot be reverted.\n";

        return false;
    }
    */
}
