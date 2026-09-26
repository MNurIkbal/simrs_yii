<?php

use yii\db\Migration;

/**
 * Class m220616_021755_create_table_report
 */
class m220616_021755_create_table_report extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."report_t" (
                "id" serial8 NOT NULL,
                "docmapping_id" int4 NULL,
                "code" varchar(100) NOT NULL,
                "content" json NULL,
                "filepath" varchar(100) NULL,
                "config" json NULL,
                "is_file" bool NOT NULL,
                "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
                "created_by" int4 NOT NULL,
                "modified_count" int4 NULL,
                "last_modified_date" timestamp(6) NULL,
                "last_modified_by" int4 NULL,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6) NULL,
                "deleted_by" int4 NULL,
                CONSTRAINT "report_t_code_key" UNIQUE ("code"),
                CONSTRAINT "report_t_pkey" PRIMARY KEY ("id")
            )
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220616_021755_create_table_report cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220616_021755_create_table_report cannot be reverted.\n";

        return false;
    }
    */
}
