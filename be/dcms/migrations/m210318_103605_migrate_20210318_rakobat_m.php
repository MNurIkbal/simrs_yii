<?php

use yii\db\Migration;

/**
 * Class m210318_103605_migrate_20210318_rakobat_m
 */
class m210318_103605_migrate_20210318_rakobat_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE "public"."rakobat_m" (
  "rakobat_id" serial8,
  "rakobat_nama" varchar(255) COLLATE "pg_catalog"."default",
  "ruangan_id" int4,
  "parentrakobat_id" int4,
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
  CONSTRAINT "rakobat_m_pkey" PRIMARY KEY ("rakobat_id")
)
;');
        $this->execute('ALTER TABLE "public"."rakobat_m" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210318_103605_migrate_20210318_rakobat_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210318_103605_migrate_20210318_rakobat_m cannot be reverted.\n";

        return false;
    }
    */
}
