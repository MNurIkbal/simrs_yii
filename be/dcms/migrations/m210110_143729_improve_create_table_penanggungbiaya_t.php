<?php

use yii\db\Migration;

/**
 * Class m210110_143729_improve_create_table_penanggungbiaya_t
 */
class m210110_143729_improve_create_table_penanggungbiaya_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS penanggungbiaya_v;');

        $this->execute('DROP TABLE IF EXISTS penanggungbiaya_t;');

        $this->execute('CREATE TABLE "public"."penanggungbiaya_t" (
              "penanggungbiaya_id" serial8,
              "pasien_id" int4,
              "carabayar_id" int4,
              "penanggungbiaya_nama" varchar(100) COLLATE "pg_catalog"."default",
              "namabagian" varchar(50) COLLATE "pg_catalog"."default",
              "noindukkaryawan" varchar(50) COLLATE "pg_catalog"."default",
              "jpkm" varchar(50) COLLATE "pg_catalog"."default",
              "instansi" varchar(50) COLLATE "pg_catalog"."default",
              "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
              "created_by" int4,
              "modified_count" int4,
              "last_modified_date" timestamp(6),
              "last_modified_by" int4,
              "is_deleted" bool NOT NULL DEFAULT false,
              "is_active" bool NOT NULL DEFAULT true,
              "deleted_date" timestamp(6),
              "deleted_by" int4,
              CONSTRAINT "penanggungbiaya_t_pkey" PRIMARY KEY ("penanggungbiaya_id")
            );
        ');

        $this->execute('
            CREATE VIEW "public"."penanggungbiaya_v" AS  SELECT penanggungbiaya_t.penanggungbiaya_id,
                penanggungbiaya_t.pasien_id, 
                penanggungbiaya_t.carabayar_id,
                penanggungbiaya_t.penanggungbiaya_nama,
                penanggungbiaya_t.namabagian,
                penanggungbiaya_t.noindukkaryawan,
                penanggungbiaya_t.jpkm,
                penanggungbiaya_t.instansi
           FROM (penanggungbiaya_t
             LEFT JOIN pendaftaran_t ON ((penanggungbiaya_t.pasien_id = pendaftaran_t.pasien_id)));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210110_143729_improve_create_table_penanggungbiaya_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210110_143729_improve_create_table_penanggungbiaya_t cannot be reverted.\n";

        return false;
    }
    */
}
