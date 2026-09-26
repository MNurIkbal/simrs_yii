<?php

use yii\db\Migration;

/**
 * Class m210804_032332_improve_kontrakpenjamin
 */
class m210804_032332_improve_kontrakpenjamin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE if not exists "public"."tipediskon_m" (
  "tipediskon_id" serial8,
  "tipediskon_nama" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
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
  CONSTRAINT "tipediskon_m_pkey" PRIMARY KEY ("tipediskon_id")
);');

        $this->execute('ALTER TABLE "public"."tipediskon_m" OWNER TO "postgres";');

        $this->execute('CREATE TABLE if not exists "public"."tipediskondetail_m" (
  "tipediskondetail_id" serial8,
  "tipediskon_id" int4 NOT NULL,
  "jenislayanan_id" int4,
  "layanan_id" int4,
  "disc_persen" float4 DEFAULT 0,
  "max_dijamin" float8 DEFAULT 0,
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
  CONSTRAINT "tipediskondetail_m_pkey" PRIMARY KEY ("tipediskondetail_id")
);');

        $this->execute('ALTER TABLE "public"."tipediskondetail_m" OWNER TO "postgres";');
        
        $this->execute('COMMENT ON COLUMN "public"."tipediskondetail_m"."jenislayanan_id" IS \'lookup_type =  jenis_layanan\';');

        $this->execute('DROP VIEW if exists "public"."tipediskon_v";');

        $this->execute("
            CREATE VIEW \"public\".\"tipediskon_v\" AS  SELECT tipediskon_m.tipediskon_id,
    tipediskon_m.tipediskon_nama,
    tipediskon_m.is_active
   FROM tipediskon_m
  WHERE tipediskon_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."tipediskon_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."tipediskondetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"tipediskondetail_v\" AS  SELECT tipediskondetail_m.tipediskondetail_id,
    tipediskon_m.tipediskon_id,
    tipediskondetail_m.jenislayanan_id,
    fgetnamalookup(tipediskondetail_m.jenislayanan_id) AS jenis_layanan,
    tipediskondetail_m.layanan_id,
        CASE
            WHEN tipediskondetail_m.jenislayanan_id = 1033 THEN kelaspelayanan_m.kelaspelayanan_nama
            WHEN tipediskondetail_m.jenislayanan_id = 1025 THEN kelompoktindakan_m.kelompoktindakan_nama
            ELSE daftartindakan_m.daftartindakan_nama
        END AS layanan,
    tipediskondetail_m.disc_persen,
    tipediskondetail_m.max_dijamin
   FROM tipediskondetail_m
     JOIN ( SELECT a.tipediskon_id,
            a.tipediskon_nama
           FROM tipediskon_m a
          WHERE a.is_deleted = false) tipediskon_m ON tipediskondetail_m.tipediskon_id = tipediskon_m.tipediskon_id
     LEFT JOIN ( SELECT e.kelaspelayanan_id,
            e.kelaspelayanan_nama
           FROM kelaspelayanan_m e) kelaspelayanan_m ON tipediskondetail_m.jenislayanan_id = 1033 AND tipediskondetail_m.layanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT b.kelompoktindakan_id,
            b.kelompoktindakan_nama
           FROM kelompoktindakan_m b) kelompoktindakan_m ON tipediskondetail_m.jenislayanan_id = 1025 AND tipediskondetail_m.layanan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN ( SELECT c.daftartindakan_id,
            c.daftartindakan_nama
           FROM daftartindakan_m c) daftartindakan_m ON tipediskondetail_m.jenislayanan_id = 1026 AND tipediskondetail_m.layanan_id = daftartindakan_m.daftartindakan_id
  WHERE tipediskondetail_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."tipediskondetail_v" OWNER TO "postgres";');

        $this->execute('CREATE TABLE if not exists "public"."kontrakpenjamin_m" (
  "kontrakpenjamin_id" serial8,
  "penjamin_id" int4 NOT NULL,
  "no_kontrak" varchar(100) COLLATE "pg_catalog"."default",
  "nama_kontrak" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_mulai" date,
  "tgl_selesai" date,
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
  CONSTRAINT "kontrakpenjamin_m_pkey" PRIMARY KEY ("kontrakpenjamin_id")
)
;');
        $this->execute('ALTER TABLE "public"."kontrakpenjamin_m" OWNER TO "postgres";');

        $this->execute('CREATE TABLE if not exists "public"."kontrakpenjamindetail_m" (
  "kontrakpenjamindetail_id" serial8,
  "kontrakpenjamin_id" int4 NOT NULL,
  "grade" varchar(255) COLLATE "pg_catalog"."default",
  "lob_id" int4,
  "tipediskon_id" int4,
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
  CONSTRAINT "kontrakpenjamindetail_m_pkey" PRIMARY KEY ("kontrakpenjamindetail_id")
)
;');

        $this->execute('COMMENT ON COLUMN "public"."kontrakpenjamindetail_m"."lob_id" IS \'lookup_type=LOB\';');

        $this->execute('ALTER TABLE "public"."kontrakpenjamindetail_m" OWNER TO "postgres";');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210804_032332_improve_kontrakpenjamin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210804_032332_improve_kontrakpenjamin cannot be reverted.\n";

        return false;
    }
    */
}
