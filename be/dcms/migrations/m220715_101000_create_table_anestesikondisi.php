<?php

use yii\db\Migration;

/**
 * Class m220715_101000_create_table_anestesikondisi
 */
class m220715_101000_create_table_anestesikondisi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."anestesikondisipasien_t" (
                "anestesikondisipasien_id" serial4 NOT NULL,
                "pasienmasukpenunjang_id" int4 NULL,
                "pendaftaran_id" int4 NULL,
                "observation_taken" time NULL,
                "consciousness" int4 NULL,
                "respiration" int4 NULL,
                "tv" int4 NULL,
                "hemodinamic_bp" float8 NULL,
                "hemodinamic_hr" float8 NULL,
                "spontaneous" float8 NULL,
                "fio" float8 NULL,
                "spo2" float8 NULL,
                "skin_color" int4 NULL,
                "skin_temperature" int4 NULL,
                "additional_data" text NULL,
                "created_date" timestamp(6) NULL DEFAULT (\'now\'::text)::date,
                "created_by" int4 NOT NULL,
                "modified_count" int4 NULL,
                "last_modified_date" timestamp(6) NULL,
                "last_modified_by" int4 NULL,
                "is_deleted" bool NOT NULL DEFAULT false,
                "is_active" bool NOT NULL DEFAULT true,
                "deleted_date" timestamp(6) NULL,
                "deleted_by" int4 NULL,
                CONSTRAINT "anestesikondisipasien_t_pkey" PRIMARY KEY ("anestesikondisipasien_id")
            );
        ');

        $this->execute('
        CREATE TABLE IF NOT EXISTS "public"."anestesikondisipasiendrugsupport_t" (
            "anestesikondisipasiendrugsupport_id" serial4 NOT NULL,
            "anestesikondisipasien_id" int4 NOT NULL,
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
            CONSTRAINT "anestesikondisipasiendrugsupport_t_pkey" PRIMARY KEY ("anestesikondisipasiendrugsupport_id")
        );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220715_101000_create_table_anestesikondisi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220715_101000_create_table_anestesikondisi cannot be reverted.\n";

        return false;
    }
    */
}