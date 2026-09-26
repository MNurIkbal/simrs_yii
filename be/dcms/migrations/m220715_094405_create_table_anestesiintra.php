<?php

use yii\db\Migration;

/**
 * Class m220715_094405_create_table_anestesiintra
 */
class m220715_094405_create_table_anestesiintra extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."anestesiintraopr_t" (
            "anestesiintraopr_id" bigserial NOT NULL,
            "pasienmasukpenunjang_id" int4 NOT NULL,
            "start_induction" time NOT NULL,
            "end_induction" time NOT NULL,
            "start_surgery" time NULL,
            "end_surgery" time NULL,
            "patient_exit" time NULL,
            "additional_data" text NULL,
            "created_date" timestamp(6) NULL DEFAULT (\'now\'::text)::date,
            "created_by" int4 NULL,
            "modified_count" int4 NULL,
            "last_modified_date" timestamp(6) NULL,
            "last_modified_by" int4 NULL,
            "is_deleted" bool NOT NULL DEFAULT false,
            "is_active" bool NOT NULL DEFAULT true,
            "deleted_date" timestamp(6) NULL,
            "deleted_by" int4 NULL,
            "length_anesthesia" int4 NULL,
            "length_surgery" int4 NULL,
            CONSTRAINT "anestesiintraopr_t_pkey" PRIMARY KEY ("anestesiintraopr_id")
        );
        ');

        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."anestesiintraoprmonitor_t" (
            "anestesiintraoprmonitor_id" bigserial NOT NULL,
            "anestesiintraopr_id" int4 NOT NULL,
            "monitoring_id" int4 NOT NULL,
            "time" time NOT NULL,
            "vinput" int4 NULL,
            "additional_data" text NULL,
            "created_date" timestamp(6) NULL DEFAULT (\'now\'::text)::date,
            "created_by" int4 NULL,
            "modified_count" int4 NULL,
            "last_modified_date" timestamp(6) NULL,
            "last_modified_by" int4 NULL,
            "is_deleted" bool NOT NULL DEFAULT false,
            "is_active" bool NOT NULL DEFAULT true,
            "deleted_date" timestamp(6) NULL,
            "deleted_by" int4 NULL,
            CONSTRAINT "anestesiintraoprmonitor_t_pkey" PRIMARY KEY ("anestesiintraoprmonitor_id")
        );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_094405_create_table_preanestesi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_094405_create_table_preanestesi cannot be reverted.\n";

        return false;
    }
    */
}
