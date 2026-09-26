<?php

use yii\db\Migration;

/**
 * Class m200303_070712_tabel_diskon
 */
class m200303_070712_tabel_diskon extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP SEQUENCE IF exists public.pembayarandiskon_t_pembayarandiskon_id_seq;');
        $this->execute('
               CREATE SEQUENCE public.pembayarandiskon_t_pembayarandiskon_id_seq
              INCREMENT 1
              MINVALUE 1
              MAXVALUE 9223372036854775807
              START 1
              CACHE 1;
        ');
        $this->execute('
        CREATE TABLE "public"."pembayarandiskon_t" (
          "pembayarandiskon_id" int8 NOT NULL DEFAULT nextval(\'pembayarandiskon_t_pembayarandiskon_id_seq\'::regclass),
          "pembayaran_id" int4 NOT NULL,
          "pegawai_id" int4 NOT NULL,
          "komponentarif_id" int4,
          "total_komponentarif" float8,
          "total_diskon" float8,
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
    );');
         $this->execute('ALTER TABLE IF exists public.pembayarandiskon_t OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200303_070712_tabel_diskon cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200303_070712_tabel_diskon cannot be reverted.\n";

        return false;
    }
    */
}
