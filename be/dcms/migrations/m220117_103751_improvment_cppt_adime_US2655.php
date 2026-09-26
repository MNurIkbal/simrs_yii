<?php

use yii\db\Migration;

/**
 * Class m220117_103751_improvment_cppt_adime_US2655
 */
class m220117_103751_improvment_cppt_adime_US2655 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."cpptadime_t" (
              "cpptadime_id" serial8 NOT NULL ,
              "pendaftaran_id" int4,
              "pasienadmisi_id" int4,
              "pagt_id" int4,
              "asesmen_gizi" text COLLATE "pg_catalog"."default",
              "diagnosa_gizi" text COLLATE "pg_catalog"."default",
              "intervensi_gizi" text COLLATE "pg_catalog"."default",
              "monitoring" text COLLATE "pg_catalog"."default",
              "evaluasi" text COLLATE "pg_catalog"."default",
              "suggestion" bool,
              "additional_data" text COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4,
              CONSTRAINT "cpptadime_t_pkey" PRIMARY KEY ("cpptadime_id")
            )
            ;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220117_103751_improvment_cppt_adime_US2655 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220117_103751_improvment_cppt_adime_US2655 cannot be reverted.\n";

        return false;
    }
    */
}
