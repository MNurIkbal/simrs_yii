<?php

use yii\db\Migration;

/**
 * Class m210419_071424_migrate_20210419_verifikasibedah_r
 */
class m210419_071424_migrate_20210419_verifikasibedah_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
  $this->execute('CREATE TABLE "public"."verifikasibedah_r" (
  "id" serial8,
  "pasienmasukpenunjang_id" int4,
  "operasi_nama" varchar(255) COLLATE "pg_catalog"."default",
  "posisi_operasi" varchar(255) COLLATE "pg_catalog"."default",
  "golonganoperasi_nama" varchar(255) COLLATE "pg_catalog"."default",
  "nama_pegawai" varchar(255) COLLATE "pg_catalog"."default",
  "pegawai_input" varchar(255) COLLATE "pg_catalog"."default",
  "dokter_id" int4,
  "perawat_id" int4,
  "tipepaket_id" int4,
  "daftartindakan_id" int4,
  "daftartindakan_nama" varchar(255) COLLATE "pg_catalog"."default",
  "is_cyto" bool,
  "is_penyulit" bool,
  "qty" int4 DEFAULT 0,
  "harga" float8 DEFAULT 0,
  "timoperasi_id" int4,
  "persentase" float8 DEFAULT 0,
  "posisi_tim" varchar(255) COLLATE "pg_catalog"."default",
  "useprice" bool,
  "kegiatanoperasi_nama" varchar(255) COLLATE "pg_catalog"."default",
  "kode_posisi" varchar(255) COLLATE "pg_catalog"."default",
  "persencyto_tindakan" float8 DEFAULT 0,
  "persen_penyulit" float8 DEFAULT 0,
  "harga_cyto" float8 DEFAULT 0,
  "harga_penyulit" float8 DEFAULT 0,
  "total_harga" float8 DEFAULT 0,
  "persentase_harga" float8 DEFAULT 0,
  "total_harga_real" float8 DEFAULT 0,
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
  CONSTRAINT "verifikasibedah_r_pkey" PRIMARY KEY ("id")
)
;');
  
  $this->execute('ALTER TABLE "public"."verifikasibedah_r" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210419_071424_migrate_20210419_verifikasibedah_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210419_071424_migrate_20210419_verifikasibedah_r cannot be reverted.\n";

        return false;
    }
    */
}
