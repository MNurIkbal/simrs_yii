<?php

use yii\db\Migration;

/**
 * Class m220624_110439_migrate_cssd_cssdpenerimaanunit_t
 */
class m220624_110439_migrate_cssd_cssdpenerimaanunit_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdpenerimaanunit_t" (
          "cssdpenerimaanunit_id" serial8,
          "cssd_id" int4,
          "nocssdpenerimaan" varchar(255) COLLATE "pg_catalog"."default",
          "tglterima" timestamp(6),
          "ruanganpenerima_id" int4,
          "ruanganasal_id" int4,
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          "peg_mengetahui_id" int4,
          "peg_menyetujui_id" int4,
          CONSTRAINT "cssdpenerimaanunit_t_pkey" PRIMARY KEY ("cssdpenerimaanunit_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."cssdpenerimaanunit_t" 
          OWNER TO "postgres";
          ');

        $this->execute('CREATE TRIGGER "no_cssdpenerimaan_t" BEFORE INSERT ON "public"."cssdpenerimaanunit_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."no_cssdpenerimaan_t"();
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_110439_migrate_cssd_cssdpenerimaanunit_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_110439_migrate_cssd_cssdpenerimaanunit_t cannot be reverted.\n";

        return false;
    }
    */
}
