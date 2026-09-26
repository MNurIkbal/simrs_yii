<?php

use yii\db\Migration;

/**
 * Class m220509_065803_migrate_historirencanaoperasi_r
 */
class m220509_065803_migrate_historirencanaoperasi_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('CREATE TABLE if not exists "public"."historirencanaoperasi_r" (
  "historirencanaoperasi_id" serial8,
  "rencanaoperasi_id" int8,
  "ruangan_id" int8,
  "kamarruangan_id" int8,
  "tgl_permintaan" timestamp(6),
  "tgl_perubahan" timestamp(6) DEFAULT (\'now\'::text)::date,
  "jam_rencana_mulai" time(6),
  "jam_rencana_selesai" time(6),
  "status_operasi" int4,
  "keterangan" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "historirencanaoperasi_r_pkey" PRIMARY KEY ("historirencanaoperasi_id")
)
;');
         $this->execute('ALTER TABLE "public"."historirencanaoperasi_r" 
  OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220509_065803_migrate_historirencanaoperasi_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220509_065803_migrate_historirencanaoperasi_r cannot be reverted.\n";

        return false;
    }
    */
}
