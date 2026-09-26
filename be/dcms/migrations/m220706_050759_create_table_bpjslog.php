<?php

use yii\db\Migration;

/**
 * Class m220706_050759_create_table_bpjslog
 */
class m220706_050759_create_table_bpjslog extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."logbpjs_r" (
              "logbpjs_id" serial8 NOT NULL PRIMARY KEY ,
              "url" text,
              "header" text,
              "request" text,
              "response" text,
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
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220706_050759_create_table_bpjslog cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220706_050759_create_table_bpjslog cannot be reverted.\n";

        return false;
    }
    */
}
