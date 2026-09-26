<?php

use yii\db\Migration;

/**
 * Class m220427_022858_migrate_BTS625_jadwalcuti_m
 */
class m220427_022858_migrate_BTS625_jadwalcuti_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."jadwalcuti_m" (
          "jadwalcuti_id" serial8,
          "pegawai_id" int4,
          "spesialis_id" int4,
          "ruangan_id" int4,
          "tgl_cuti_awal" timestamp(6),
          "tgl_cuti_akhir" timestamp(6),
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          CONSTRAINT "jadwalcuti_m_pkey" PRIMARY KEY ("jadwalcuti_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."jadwalcuti_m" 
          OWNER TO "postgres";
        ');


        $this->execute('ALTER TABLE "public"."jadwalcuti_m" 
          ADD COLUMN IF NOT EXISTS "alasan_cuti" text COLLATE "pg_catalog"."default";
          ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220427_022858_migrate_BTS625_jadwalcuti_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220427_022858_migrate_BTS625_jadwalcuti_m cannot be reverted.\n";

        return false;
    }
    */
}
