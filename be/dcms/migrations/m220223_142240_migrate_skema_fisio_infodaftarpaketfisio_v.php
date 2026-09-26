<?php

use yii\db\Migration;

/**
 * Class m220223_142240_migrate_skema_fisio_infodaftarpaketfisio_v
 */
class m220223_142240_migrate_skema_fisio_infodaftarpaketfisio_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."daftarpaketfisio_m" (
          "daftarpaketfisio_id" serial4,
          "parent_id" int4,
          "daftarpaketfisio_nama" varchar(100) COLLATE "pg_catalog"."default",
          "frekuensi" float4,
          "catatan" text COLLATE "pg_catalog"."default",
          "jumlah" float4,
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
          CONSTRAINT "daftarpaketfisio_m_pkey" PRIMARY KEY ("daftarpaketfisio_id")
          )
          ;
        ');

        $this->execute('
          ALTER TABLE "public"."daftarpaketfisio_m" 
          OWNER TO "postgres";
        ');
        
        $this->execute('DROP VIEW if exists public.infodaftarpaketfisio_v;');
        $this->execute("
          CREATE VIEW \"public\".\"infodaftarpaketfisio_v\" AS
          SELECT daftarpaketfisio_m.daftarpaketfisio_id,
          daftarpaketfisio_m.parent_id,
          daftarpaketfisio_m.daftarpaketfisio_nama,
          daftarpaketfisio_m.frekuensi,
          daftarpaketfisio_m.catatan,
          daftarpaketfisio_m.jumlah,
          daftarpaketfisio_m.is_active,
          daftarpaketfisio_m.is_deleted,
          daftartindakan_m.daftartindakan_kode,
          daftartindakan_m.daftartindakan_nama,
          daftartindakan_m.daftartindakan_namalainnya
          FROM (daftarpaketfisio_m
          JOIN ( SELECT a.daftartindakan_id,
          a.daftartindakan_kode,
          a.daftartindakan_nama,
          a.daftartindakan_namalainnya
          FROM daftartindakan_m a) daftartindakan_m ON ((daftarpaketfisio_m.parent_id = daftartindakan_m.daftartindakan_id)))
          WHERE (daftarpaketfisio_m.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.infodaftarpaketfisio_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_142240_migrate_skema_fisio_infodaftarpaketfisio_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_142240_migrate_skema_fisio_infodaftarpaketfisio_v cannot be reverted.\n";

        return false;
    }
    */
}
