<?php

use yii\db\Migration;

/**
 * Class m220223_142428_migrate_skema_fisio_infodaftarpaketfisiodet_v
 */
class m220223_142428_migrate_skema_fisio_infodaftarpaketfisiodet_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."daftarpaketfisiodet_m" (
          "daftarpaketfisiodet_id" serial4,
          "daftarpaketfisio_id" int4,
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
          CONSTRAINT "daftarpaketfisiodet_m_pkey" PRIMARY KEY ("daftarpaketfisiodet_id")
          )
          ;
        ');

        $this->execute('
          ALTER TABLE "public"."daftarpaketfisiodet_m" 
          OWNER TO "postgres";
        ');
        
        $this->execute('DROP VIEW if exists public.infodaftarpaketfisiodet_v;');
        $this->execute("
          CREATE VIEW \"public\".\"infodaftarpaketfisiodet_v\" AS
          SELECT daftarpaketfisiodet_m.daftarpaketfisiodet_id,
          daftarpaketfisiodet_m.daftarpaketfisio_id,
          daftarpaketfisiodet_m.daftartindakan_id,
          daftarpaketfisiodet_m.is_active,
          daftartindakan_m.daftartindakan_kode,
          daftartindakan_m.daftartindakan_nama,
          daftartindakan_m.daftartindakan_namalainnya,
          daftartindakan_m.kelompoktindakan_nama
          FROM (daftarpaketfisiodet_m
          JOIN ( SELECT a.daftartindakan_id,
          a.daftartindakan_kode,
          a.daftartindakan_nama,
          a.daftartindakan_namalainnya,
          kelompoktindakan_m.kelompoktindakan_id,
          kelompoktindakan_m.kelompoktindakan_nama
          FROM (daftartindakan_m a
          JOIN kelompoktindakan_m ON ((a.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))) daftartindakan_m ON ((daftarpaketfisiodet_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
          WHERE (daftarpaketfisiodet_m.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.infodaftarpaketfisiodet_v OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220223_142428_migrate_skema_fisio_infodaftarpaketfisiodet_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220223_142428_migrate_skema_fisio_infodaftarpaketfisiodet_v cannot be reverted.\n";

        return false;
    }
    */
}
