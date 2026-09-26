<?php

use yii\db\Migration;

/**
 * Class m220120_011841_migrate_bts14_pendaftaranrencanakontrol
 */
class m220120_011841_migrate_bts14_pendaftaranrencanakontrol extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."rencanakontrol_t" (
          "rencanakontrol_id" serial4,
          "pendaftaran_id" int4,
          "bpjs_id" int4,
          "no_spri" varchar(32) COLLATE "pg_catalog"."default",
          "jenis_rencana" varchar(5) COLLATE "pg_catalog"."default",
          "no_sep" varchar(32) COLLATE "pg_catalog"."default",
          "no_kartu" varchar(32) COLLATE "pg_catalog"."default",
          "nama" varchar(64) COLLATE "pg_catalog"."default",
          "nosuratkontrol" varchar(64) COLLATE "pg_catalog"."default",
          "nama_spesialis" varchar(64) COLLATE "pg_catalog"."default",
          "dokterdpjp_kode" varchar(16) COLLATE "pg_catalog"."default",
          "dokterdpjp_nama" varchar(64) COLLATE "pg_catalog"."default",
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          "tgl_rencanakontrol" timestamp(6),
          "jenis_pelayanan" varchar(32) COLLATE "pg_catalog"."default",
          CONSTRAINT "rencanakontrol_t_pkey" PRIMARY KEY ("rencanakontrol_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."rencanakontrol_t" 
          OWNER TO "postgres";
          ');

        $this->execute('ALTER TABLE "public"."rencanakontrol_t" 
            ADD COLUMN IF NOT EXISTS "kode_poli" varchar(32) COLLATE "pg_catalog"."default";
          ');

        $this->execute("
            DELETE from lookup_m where lookup_id IN (1126,1125);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1126, 'jenis_rencana', 'Rencana Inap', 'RI', NULL, NULL, NULL, '2022-01-18 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1125, 'jenis_rencana', 'Rencana Kontrol', 'RJ', NULL, NULL, NULL, '2022-01-18 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");

        $this->execute('DROP VIEW if exists public.rencanakontrol_v;');
        $this->execute("
            CREATE VIEW \"public\".\"rencanakontrol_v\" AS
            SELECT rencanakontrol_t.rencanakontrol_id,
            rencanakontrol_t.pendaftaran_id,
            rencanakontrol_t.bpjs_id,
            rencanakontrol_t.no_spri,
            rencanakontrol_t.tgl_rencanakontrol,
            rencanakontrol_t.jenis_rencana,
            lookup_m.lookup_name AS jenis_rencana_nama,
            rencanakontrol_t.jenis_pelayanan,
            rencanakontrol_t.no_sep,
            rencanakontrol_t.no_kartu,
            rencanakontrol_t.nama,
            rencanakontrol_t.nosuratkontrol,
            rencanakontrol_t.nama_spesialis,
            rencanakontrol_t.dokterdpjp_kode,
            rencanakontrol_t.dokterdpjp_nama,
            rencanakontrol_t.additional_data
            FROM (rencanakontrol_t
            LEFT JOIN lookup_m ON (((rencanakontrol_t.jenis_rencana)::integer = lookup_m.lookup_id)))
            WHERE ((rencanakontrol_t.is_deleted = false) AND (rencanakontrol_t.is_active = true))
            ;");
        $this->execute('
            ALTER TABLE public.rencanakontrol_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220120_011841_migrate_bts14_pendaftaranrencanakontrol cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220120_011841_migrate_bts14_pendaftaranrencanakontrol cannot be reverted.\n";

        return false;
    }
    */
}
