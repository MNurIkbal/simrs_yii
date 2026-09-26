<?php

use yii\db\Migration;

/**
 * Class m220223_135654_migrate_skema_fisio_scheduleprogramterapidet_v
 */
class m220223_135654_migrate_skema_fisio_scheduleprogramterapidet_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."programterapidetailpaket_t" (
          "programterapidetailpaket_id" serial8,
          "programterapidetail_id" int4,
          "daftartindakan_id" int4,
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
          "catatan" text COLLATE "pg_catalog"."default",
          CONSTRAINT "programterapidetailpaket_t_pkey" PRIMARY KEY ("programterapidetailpaket_id")
          )
          ;
        ');

        $this->execute('
          ALTER TABLE "public"."programterapidetailpaket_t" 
          OWNER TO "postgres";
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."programterapirajal_r" (
          "programterapirajal_id" serial8,
          "programterapi_id" int4,
          "pendaftaran_id" int4,
          "pasienmasukpenunjang_id" int4,
          "pasien_id" int4,
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
          )
          ;
          ');

        $this->execute('
          ALTER TABLE "public"."programterapirajal_r" 
          OWNER TO "postgres";
        ');
        
        $this->execute('DROP VIEW if exists public.scheduleprogramterapidet_v;');
        $this->execute("
            CREATE VIEW \"public\".\"scheduleprogramterapidet_v\" AS
            SELECT programterapi_t.pendaftaran_id,
            programterapidetail_t.programterapi_id,
            programterapidetail_t.programterapidetail_id,
            programterapidetail_t.frekuensi,
            CASE
            WHEN (programterapidetail_t.is_paketfisio = true) THEN programterapidetailpaket_t.catatan
            ELSE programterapidetail_t.catatan
            END AS catatan,
            programterapidetail_t.is_paketfisio,
            pegawai_m.nama_pegawai AS dokter_perujuk,
            daftartindakan_m.daftartindakan_id AS terapi_id,
            kategoritindakan_m.kategoritindakan_nama AS kategori,
            daftartindakan_paket.daftartindakan_nama AS daftartindakan_parent,
            daftartindakan_m.daftartindakan_nama AS terapi
            FROM ((((((programterapidetail_t
            LEFT JOIN ( SELECT a.daftartindakan_id,
            a.programterapidetail_id,
            a.catatan
            FROM programterapidetailpaket_t a) programterapidetailpaket_t ON ((programterapidetail_t.programterapidetail_id = programterapidetailpaket_t.programterapidetail_id)))
            JOIN ( SELECT a.daftartindakan_id,
            a.kategoritindakan_id,
            a.daftartindakan_nama
            FROM daftartindakan_m a) daftartindakan_m ON ((((programterapidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id) AND (programterapidetail_t.is_paketfisio IS NOT TRUE)) OR ((programterapidetailpaket_t.daftartindakan_id = daftartindakan_m.daftartindakan_id) AND (programterapidetail_t.is_paketfisio = true)))))
            JOIN ( SELECT a.kategoritindakan_id,
            a.kategoritindakan_nama
            FROM kategoritindakan_m a) kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) pegawai_m ON ((programterapidetail_t.dokterperujuk_id = pegawai_m.pegawai_id)))
            JOIN ( SELECT a.programterapi_id,
            a.pendaftaran_id
            FROM programterapi_t a) programterapi_t ON ((programterapidetail_t.programterapi_id = programterapi_t.programterapi_id)))
            JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
            FROM daftartindakan_m a) daftartindakan_paket ON ((programterapidetail_t.daftartindakan_id = daftartindakan_paket.daftartindakan_id)))
            WHERE (programterapidetail_t.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.scheduleprogramterapidet_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_135654_migrate_skema_fisio_scheduleprogramterapidet_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_135654_migrate_skema_fisio_scheduleprogramterapidet_v cannot be reverted.\n";

        return false;
    }
    */
}
