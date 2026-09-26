<?php

use yii\db\Migration;

/**
 * Class m210403_082323_migrate_20210403_tarifbedah_m
 */
class m210403_082323_migrate_20210403_tarifbedah_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE "public"."tarifbedah_m" (
  "tarifbedah_id" serial8,
  "kegiatanoperasi_id" int4,
  "kelaspelayanan_id" int4,
  "perdatarif_id" int4,
  "persen_cyto" float4,
  "tarif" float8,
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
  CONSTRAINT "tarifbedah_m_pkey" PRIMARY KEY ("tarifbedah_id")
)
;');
        
        $this->execute('ALTER TABLE "public"."tarifbedah_m" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210403_082323_migrate_20210403_tarifbedah_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210403_082323_migrate_20210403_tarifbedah_m cannot be reverted.\n";

        return false;
    }
    */
}
