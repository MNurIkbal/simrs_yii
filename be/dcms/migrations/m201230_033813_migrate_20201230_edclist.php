<?php

use yii\db\Migration;

/**
 * Class m201230_033813_migrate_20201230_edclist
 */
class m201230_033813_migrate_20201230_edclist extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->execute('CREATE TABLE if not exists "public"."edclist_m" (
  "edclist_id" serial8,
  "edclist_kode" varchar(50) COLLATE "pg_catalog"."default",
  "edclist_namamesin" varchar(100) COLLATE "pg_catalog"."default",
  "edclist_bank" int4,
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
  CONSTRAINT "edclist_m_pkey" PRIMARY KEY ("edclist_id")
)
;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201230_033813_migrate_20201230_edclist cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201230_033813_migrate_20201230_edclist cannot be reverted.\n";

        return false;
    }
    */
}
