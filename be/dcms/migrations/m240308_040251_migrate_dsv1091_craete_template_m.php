<?php

use yii\db\Migration;

/**
 * Class m240308_040251_migrate_dsv1091_craete_template_m
 */
class m240308_040251_migrate_dsv1091_craete_template_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."template_m" (
              "template_id" serial8 NOT NULL PRIMARY KEY ,
              "jenis" varchar(50) NULL,
              "type" varchar(50) NULL,
              "temp_nama" varchar(100) NULL,
              "pendaftaran_id" int4,
              "additional_data" text,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "deleted_date" timestamp(6),
              "deleted_by" int4,
              "data_anatomi" text
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240308_040251_migrate_dsv1091_craete_template_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240308_040251_migrate_dsv1091_craete_template_m cannot be reverted.\n";

        return false;
    }
    */
}
