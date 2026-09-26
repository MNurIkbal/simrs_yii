<?php

use yii\db\Migration;

/**
 * Class m230818_092252_migrate_RPP519_table_mappingreportperanpengguna_k
 */
class m230818_092252_migrate_RPP519_table_mappingreportperanpengguna_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."mappingreportperanpengguna_k" (
  "mappingreportperanpengguna_id" serial4 NOT NULL,
  "peranpengguna_id" int4,
  "module_id" int4,
  "reports_id" json,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6),
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_active" bool,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  "is_deleted" bool DEFAULT false,
  "modul_id" int4,
  CONSTRAINT "mappingreportperanpengguna_k_pkey" PRIMARY KEY ("mappingreportperanpengguna_id")
)
;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230818_092252_migrate_RPP519_table_mappingreportperanpengguna_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230818_092252_migrate_RPP519_table_mappingreportperanpengguna_k cannot be reverted.\n";

        return false;
    }
    */
}
