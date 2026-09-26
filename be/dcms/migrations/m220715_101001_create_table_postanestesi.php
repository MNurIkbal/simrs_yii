<?php

use yii\db\Migration;

/**
 * Class m220715_101001_create_table_postanestesi
 */
class m220715_101001_create_table_postanestesi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
        CREATE TABLE IF NOT EXISTS "public"."anestesipostopr_t" (
            "anestesipostopr_id" bigserial NOT NULL,
            "pasienmasukpenunjang_id" int4 NOT NULL,
            "arrival_time" time(4) NULL,
            "consciousness" varchar NULL,
            "respiration" varchar NULL,
            "tv" varchar NULL,
            "bp" varchar NULL,
            "hr" varchar NULL,
            "spontaneous" varchar NULL,
            "fio" varchar NULL,
            "spo2" varchar NULL,
            "skin_color" varchar NULL,
            "skin_temperature" varchar NULL,
            "arousal_time" time NULL,
            "doctor_ins" text NULL,
            "additional_data" text NULL,
            "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
            "created_by" int4 NULL,
            "modified_count" int4 NULL,
            "last_modified_date" timestamp(6) NULL,
            "last_modified_by" int4 NULL,
            "is_deleted" bool NOT NULL DEFAULT false,
            "is_active" bool NOT NULL DEFAULT true,
            "deleted_date" timestamp(6) NULL,
            "deleted_by" int4 NULL,
            CONSTRAINT "anestesipostopr_t_pkey" PRIMARY KEY ("anestesipostopr_id")
        );
        ');

        $this->execute('
        CREATE TABLE IF NOT EXISTS "public"."anestesipostopraldscore_t" (
            "anestesipostopraldscore_id" bigserial NOT NULL,
            "anestesipostopr_id" int4 NOT NULL,
            "arrived_id" int4 NOT NULL,
            "score_id" int4 NOT NULL,
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
            CONSTRAINT "anestesipostopraldscore_t_pkey" PRIMARY KEY ("anestesipostopraldscore_id")
        );
        ');

        $this->execute('
        CREATE TABLE IF NOT EXISTS "public"."anestesipostoprdrugsupport_t" (
            "anestesipostoprdrugsupport_id" bigserial NOT NULL,
            "anestesipostopr_id" int4 NOT NULL,
            "obatalkes_id" int4 NOT NULL,
            "dose" varchar NULL,
            "time_delivery" time NULL,
            "additional_data" text NULL,
            "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
            "created_by" int4 NULL,
            "modified_count" int4 NULL,
            "last_modified_date" timestamp(6) NULL,
            "last_modified_by" int4 NULL,
            "is_deleted" bool NOT NULL DEFAULT false,
            "is_active" bool NOT NULL DEFAULT true,
            "deleted_date" timestamp(6) NULL,
            "deleted_by" int4 NULL,
            CONSTRAINT "anestesipostoprdrugsupport_t_pkey" PRIMARY KEY ("anestesipostoprdrugsupport_id")
        );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_101001_create_table_postanestesi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_101001_create_table_postanestesi cannot be reverted.\n";

        return false;
    }
    */
}