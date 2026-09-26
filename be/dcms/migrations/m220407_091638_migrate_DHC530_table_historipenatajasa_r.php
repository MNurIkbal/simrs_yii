<?php

use yii\db\Migration;

/**
 * Class m220407_091638_migrate_DHC530_table_historipenatajasa_r
 */
class m220407_091638_migrate_DHC530_table_historipenatajasa_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."historipenatajasa_r" (
              "historipenatajasa_id" serial8 NOT NULL PRIMARY KEY ,
              "tindakanpelayanan_id" int8,
              "obatalkespasien_id" int8,
              "tgl_perubahan" timestamp(6) DEFAULT (\'now\'::text)::date,
              "tipeperubahan_id" int4,
              "alasan" text COLLATE "pg_catalog"."default",
              "keterangan" text COLLATE "pg_catalog"."default",
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
        echo "m220407_091638_migrate_DHC530_table_historipenatajasa_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_091638_migrate_DHC530_table_historipenatajasa_r cannot be reverted.\n";

        return false;
    }
    */
}
