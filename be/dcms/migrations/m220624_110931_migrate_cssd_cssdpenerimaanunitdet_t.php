<?php

use yii\db\Migration;

/**
 * Class m220624_110931_migrate_cssd_cssdpenerimaanunitdet_t
 */
class m220624_110931_migrate_cssd_cssdpenerimaanunitdet_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdpenerimaanunitdet_t" (
          "cssdpenerimaanunitdet_id" serial8,
          "cssdpenerimaanunit_id" int4 NOT NULL,
          "cssddet_id" int4 NOT NULL,
          "ruangan_id" int4,
          "barangalkes_id" int4,
          "jmlkirim" int4,
          "jmlterima" int4,
          "satuankecil_id" int4,
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          "is_alkes" bool DEFAULT true,
          CONSTRAINT "cssdpenerimaanunitdet_t_pkey" PRIMARY KEY ("cssdpenerimaanunitdet_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."cssdpenerimaanunitdet_t" 
          OWNER TO "postgres";
          ');

        $this->execute('COMMENT ON COLUMN "public"."cssdpenerimaanunitdet_t"."is_alkes" IS \'flaging pembeda barang atau alkes\';
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_110931_migrate_cssd_cssdpenerimaanunitdet_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_110931_migrate_cssd_cssdpenerimaanunitdet_t cannot be reverted.\n";

        return false;
    }
    */
}
