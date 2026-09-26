<?php

use yii\db\Migration;

/**
 * Class m210121_082908_migrate_20200121_manufaktur_m
 */
class m210121_082908_migrate_20200121_manufaktur_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."manufaktur_m" (
  "manufaktur_id" serial8,
  "kode" varchar(100) COLLATE "pg_catalog"."default",
  "nama" varchar(255) COLLATE "pg_catalog"."default",
  "alamat" text COLLATE "pg_catalog"."default",
  "kontak" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "manufaktur_m_pkey" PRIMARY KEY ("manufaktur_id")
)
;');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210121_082908_migrate_20200121_manufaktur_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210121_082908_migrate_20200121_manufaktur_m cannot be reverted.\n";

        return false;
    }
    */
}
