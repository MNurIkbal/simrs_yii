<?php

use yii\db\Migration;

/**
 * Class m210427_022048_improvment_add_table_jenisdiagnosakep_m
 */
class m210427_022048_improvment_add_table_jenisdiagnosakep_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE diagnosakep_m ADD IF NOT EXISTS jenisdiagnosakep_id int4;
        ');

        $this->execute('
            ALTER TABLE diagnosakep_m ADD IF NOT EXISTS keterangan TEXT;
        ');

        $this->execute('
            DROP TABLE IF EXISTS jenisdiagnosakep_m;
        ');

        $this->execute('
            CREATE TABLE "public"."jenisdiagnosakep_m" (
                "jenisdiagnosakep_id" serial8 NOT NULL PRIMARY KEY,
                "jenisdiagnosakep_kode" varchar(20) COLLATE "pg_catalog"."default",
                "jenisdiagnosakep_nama" varchar(100) COLLATE "pg_catalog"."default",
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
        echo "m210427_022048_improvment_add_table_jenisdiagnosakep_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210427_022048_improvment_add_table_jenisdiagnosakep_m cannot be reverted.\n";

        return false;
    }
    */
}
