<?php

use yii\db\Migration;

/**
 * Class m210802_041043_improve_rekomendasiorder
 */
class m210802_041043_improve_rekomendasiorder extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "doi" int4;');
    $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "ssmin" numeric;');
    $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "qty_sugesstion" numeric;');

    $this->execute('CREATE TABLE IF NOT exists "public"."basecalro_r" (
  "basecalro_id" serial8,
  "tanggal" date,
  "count" float4 DEFAULT 0,
  "move_category" varchar(100) COLLATE "pg_catalog"."default",
  "min" float4 DEFAULT 0,
  "max" float4 DEFAULT 0,
  "avg" float4 DEFAULT 0,
  "min_resep" float4 DEFAULT 0,
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
  "obatalkes_id" int4 NOT NULL,
  "jenisobatalkes_id" int4,
  CONSTRAINT "basecalro_k_pkey" PRIMARY KEY ("basecalro_id")
)
;');

    $this->execute('ALTER TABLE "public"."basecalro_r" OWNER TO "postgres";');

    $this->execute('CREATE TABLE if not exists "public"."movingcriteria_m" (
  "movingcriteria_id" serial8,
  "min" int4,
  "max" int4,
  "criteria" varchar(255) COLLATE "pg_catalog"."default",
  "factor" varchar(255) COLLATE "pg_catalog"."default",
  "ss_min" varchar(255) COLLATE "pg_catalog"."default",
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
  CONSTRAINT "movingcriteria_m_pkey" PRIMARY KEY ("movingcriteria_id")
)
;');
    $this->execute('ALTER TABLE "public"."movingcriteria_m" OWNER TO "postgres";');

     $this->execute('DROP VIEW if exists "public"."sumstokout_v";');
    
    $this->execute("
        CREATE VIEW \"public\".\"sumstokout_v\" AS  SELECT count_stok.obatalkes_id,
    count(count_stok.tanggal) AS count,
    max(count_stok.stok_out) AS max,
    min(count_stok.stok_out) AS min,
    sum(count_stok.stok_out) / count(count_stok.tanggal)::double precision AS avg,
    min(count_stok.min_resep) AS min_resep,
    obatalkes_m.jenisobatalkes_id
   FROM ( SELECT detail.obatalkes_id,
            detail.tanggal,
            sum(detail.qtystok_out) AS stok_out,
            min(detail.qty_resep) AS min_resep
           FROM ( SELECT stokobatalkes_t.obatalkes_id,
                    to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date AS tanggal,
                    stokobatalkes_t.qtystok_out,
                        CASE
                            WHEN stokobatalkes_t.obatalkespasien_id IS NOT NULL AND stokobatalkes_t.tglstok_out IS NOT NULL THEN stokobatalkes_t.qtystok_out
                            ELSE NULL::double precision
                        END AS qty_resep,
                    stokobatalkes_t.mutasiobatdetail_id,
                    mutasiobatdetail.ruanganasal_id,
                    mutasiobatdetail.ruangantujuan_id
                   FROM stokobatalkes_t
                     LEFT JOIN ( SELECT mutasiobatdetail_t.mutasiobatdetail_id,
                            mutasiobatruangan_t.ruanganasal_id,
                            mutasiobatruangan_t.ruangantujuan_id
                           FROM mutasiobatdetail_t
                             JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id) mutasiobatdetail ON stokobatalkes_t.mutasiobatdetail_id = mutasiobatdetail.mutasiobatdetail_id
                  WHERE (stokobatalkes_t.mutasiobatdetail_id IS NOT NULL AND NOT (mutasiobatdetail.ruangantujuan_id IN ( SELECT ruangan_m.ruangan_id
                           FROM ruangan_m
                          WHERE ruangan_m.instalasi_id = 6)) AND mutasiobatdetail.ruanganasal_id = 25 OR stokobatalkes_t.obatalkespasien_id IS NOT NULL AND stokobatalkes_t.tglstok_out IS NOT NULL) AND (stokobatalkes_t.tglstok_out >= CURRENT_DATE::timestamp without time zone AND stokobatalkes_t.tglstok_out <= (CURRENT_DATE - '30 days'::interval) OR stokobatalkes_t.tglstok_out >= (CURRENT_DATE - '30 days'::interval) AND stokobatalkes_t.tglstok_out <= CURRENT_DATE::timestamp without time zone)
                  ORDER BY stokobatalkes_t.obatalkes_id, (to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date)) detail
          GROUP BY detail.obatalkes_id, detail.tanggal
          ORDER BY detail.obatalkes_id, detail.tanggal) count_stok
     LEFT JOIN obatalkes_m ON count_stok.obatalkes_id = obatalkes_m.obatalkes_id
  GROUP BY count_stok.obatalkes_id, obatalkes_m.jenisobatalkes_id
  ORDER BY count_stok.obatalkes_id;");

    $this->execute('ALTER TABLE "public"."sumstokout_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210802_041043_improve_rekomendasiorder cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210802_041043_improve_rekomendasiorder cannot be reverted.\n";

        return false;
    }
    */
}
