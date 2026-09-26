<?php

use yii\db\Migration;

/**
 * Class m220620_112950_migrate_MHG2156_table_bodymassindexanak_m
 */
class m220620_112950_migrate_MHG2156_table_bodymassindexanak_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."bodymassindexanak_m" (
              "bodymassindexanak_id" serial8 NOT NULL PRIMARY KEY,
              "bmi_range" varchar(50) COLLATE "pg_catalog"."default",
              "jenis_kelamin" int4,
              "umur_awal" float4,
              "umur_akhir" float4,
              "bmi_minimum" float8 NOT NULL,
              "bmi_maksimum" float8 NOT NULL,
              "bmi_sign" varchar(2) COLLATE "pg_catalog"."default",
              "bmi_defenisi" text COLLATE "pg_catalog"."default" NOT NULL,
              "bmi_pesan" varchar(100) COLLATE "pg_catalog"."default",
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
        echo "m220620_112950_migrate_MHG2156_table_bodymassindexanak_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220620_112950_migrate_MHG2156_table_bodymassindexanak_m cannot be reverted.\n";

        return false;
    }
    */
}
