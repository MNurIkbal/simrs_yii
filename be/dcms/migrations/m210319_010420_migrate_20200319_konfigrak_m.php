<?php

use yii\db\Migration;

/**
 * Class m210319_010420_migrate_20200319_konfigrak_m
 */
class m210319_010420_migrate_20200319_konfigrak_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE "public"."konfigrak_m" (
  "konfigrak_id" serial8,
  "ruangan_id" int4,
  "rakobat_id" int4,
  "min_stok" float8,
  "max_stok" float8,
  "obatalkes_id" int4 NOT NULL,
  "stokobatr_id" int4,
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
  CONSTRAINT "konfigrak_m_pkey" PRIMARY KEY ("konfigrak_id")
)
;');
        $this->execute('ALTER TABLE "public"."konfigrak_m" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210319_010420_migrate_20200319_konfigrak_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210319_010420_migrate_20200319_konfigrak_m cannot be reverted.\n";

        return false;
    }
    */
}
