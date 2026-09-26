<?php

use yii\db\Migration;

/**
 * Class m201019_042443_migrate_20201019_pemeriksaanspesialismcu
 */
class m201019_042443_migrate_20201019_pemeriksaanspesialismcu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."pemeriksaanspesialismcu_t" (
  "pemeriksaanspesialismcu_id" serial8,
  "pendaftaran_id" int4 NOT NULL,
  "ruangan_id" int4 NOT NULL,
  "berat_badan" float4,
  "tinggi_badan" float4,
  "td_sistolik" int2,
  "td_diastolik" int2,
  "pernafasan" varchar(50) COLLATE "pg_catalog"."default",
  "detak_nadi" int2,
  "suhu" varchar(50) COLLATE "pg_catalog"."default",
  "additional_pemeriksaan" json,
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
  CONSTRAINT "pemeriksaanspesialismcu_t_pkey" PRIMARY KEY ("pemeriksaanspesialismcu_id")
)
;');
        $this->execute('ALTER TABLE "public"."pemeriksaanspesialismcu_t" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201019_042443_migrate_20201019_pemeriksaanspesialismcu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201019_042443_migrate_20201019_pemeriksaanspesialismcu cannot be reverted.\n";

        return false;
    }
    */
}
