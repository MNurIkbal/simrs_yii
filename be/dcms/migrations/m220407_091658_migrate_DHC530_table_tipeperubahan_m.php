<?php

use yii\db\Migration;

/**
 * Class m220407_091658_migrate_DHC530_table_tipeperubahan_m
 */
class m220407_091658_migrate_DHC530_table_tipeperubahan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."tipeperubahan_m" (
              "tipeperubahan_id" serial8 NOT NULL PRIMARY KEY ,
              "tipeperubahan_nama" varchar(100) COLLATE "pg_catalog"."default",
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

        $this->execute('
            TRUNCATE TABLE tipeperubahan_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "tipeperubahan_m"("tipeperubahan_nama") VALUES 
            (\'Tambah Tindakan\'),
            (\'Hapus Tindakan\'),
            (\'Tambah Penjamin\'),
            (\'Hapus Penjamin\'),
            (\'Tambah Akomodasi\'),
            (\'Edit Akomodasi\'),
            (\'Hapus Akomodasi\'),
            (\'Edit Tindakan\'),
            (\'Edit Harga Tindakan\'),
            (\'Edit Kelas Pelayanan\'),
            (\'Tambah BMHP\'),
            (\'Hapus BMHP\'),
            (\'Tambah Paket\'),
            (\'Hapus Paket\'),
            (\'Edit Paket\');

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220407_091658_migrate_DHC530_table_tipeperubahan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_091658_migrate_DHC530_table_tipeperubahan_m cannot be reverted.\n";

        return false;
    }
    */
}
