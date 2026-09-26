<?php

use yii\db\Migration;

/**
 * Class m220318_073558_migrate_ODH396_table_invoicegabungdetail_t
 */
class m220318_073558_migrate_ODH396_table_invoicegabungdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."invoicegabungdetail_t" (
              "invoicegabungdetail_id" serial8 NOT NULL PRIMARY KEY,
              "invoicegabung_id" int4 NOT NULL,
              "pendaftaran_id" int4,
              "pembayaran_id" int4,
              "no_pembayaran" varchar COLLATE "pg_catalog"."default",
              "tgl_invoice" timestamp(6) DEFAULT (\'now\'::text)::date,
              "total_invoice" float8 DEFAULT 0,
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4
            )
            ;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220318_073558_migrate_ODH396_table_invoicegabungdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_073558_migrate_ODH396_table_invoicegabungdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
