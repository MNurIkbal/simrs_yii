<?php

use yii\db\Migration;

/**
 * Class m200730_035036_migrate_mhkn_20200730
 */
class m200730_035036_migrate_mhkn_20200730 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pindahkamar_t" ADD COLUMN "is_pasientitipan" bool DEFAULT false;');
        $this->execute('ALTER TABLE "public"."pindahkamar_t" ADD COLUMN "kelas_ditagihkan_id" int4;');
        $this->execute('ALTER TABLE "public"."pindahkamar_t" ADD COLUMN "kamar_titipan_id" int4;');
        $this->execute('ALTER TABLE "public"."pindahkamar_t" ADD COLUMN "tempattidur_titipan_id" int4;');
        $this->execute('ALTER TABLE "public"."pindahkamar_t" ADD COLUMN "ruangan_titipan_id" int4;');

        $this->execute('CREATE TABLE "public"."dokterexternal_m" (
  "dokterexternal_id" serial8,
  "nama" varchar(255) COLLATE "pg_catalog"."default",
  "asal_rs" varchar(255) COLLATE "pg_catalog"."default",
  "alamat_rs" text COLLATE "pg_catalog"."default",
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
  CONSTRAINT "dokterexternal_m_pkey" PRIMARY KEY ("dokterexternal_id")
)
;');

        $this->execute('CREATE TABLE "public"."kontraksupplier_m" (
  "kontraksupplier_id" serial8,
  "supplier_id" int4,
  "payterm_id" int4,
  "jumlah_hari" int4,
  "pajak_id" int4,
  "persen_ppn" numeric(15,2) DEFAULT 0,
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
  CONSTRAINT "kontraksupplier_m_pkey" PRIMARY KEY ("kontraksupplier_id")
)
;');

        $this->execute('CREATE TABLE "public"."kontraksupplierdetail_m" (
  "kontraksupplierdetail_id" serial8,
  "kontraksupplier_id" int4,
  "obatalkes_id" int4,
  "kode_obat" varchar(255) COLLATE "pg_catalog"."default",
  "nama_obat" varchar(255) COLLATE "pg_catalog"."default",
  "satuankecil_id" int4,
  "satuankonv1_id" int4,
  "satuankonv2_id" int4,
  "harga" numeric(15,2) DEFAULT 0,
  "diskon" numeric(15,2) DEFAULT 0,
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
  CONSTRAINT "kontraksupplierdetail_m_pkey" PRIMARY KEY ("kontraksupplierdetail_id")
)
;');

        $this->execute("
            CREATE VIEW \"public\".\"kontraksupplier_v\" AS  SELECT kontraksupplier_m.kontraksupplier_id,
    kontraksupplier_m.supplier_id,
    supplier_m.supplier_nama,
    kontraksupplier_m.payterm_id,
    kontraksupplier_m.jumlah_hari,
    kontraksupplier_m.pajak_id,
    kontraksupplier_m.persen_ppn,
    kontraksupplierdetail_m.obatalkes_id,
    kontraksupplierdetail_m.kode_obat,
    kontraksupplierdetail_m.nama_obat,
    kontraksupplierdetail_m.harga,
    kontraksupplierdetail_m.diskon,
    sat_kecil.satuanunit_nama AS satuan_kecil,
    sat_konv1.satuanunit_nama AS satuan_konversi1,
    sat_konv2.satuanunit_nama AS satuan_konversi2
   FROM (((((kontraksupplier_m
     JOIN supplier_m ON ((kontraksupplier_m.supplier_id = supplier_m.supplier_id)))
     JOIN kontraksupplierdetail_m ON ((kontraksupplier_m.kontraksupplier_id = kontraksupplierdetail_m.kontraksupplier_id)))
     LEFT JOIN satuanunit_m sat_kecil ON ((kontraksupplierdetail_m.satuankecil_id = sat_kecil.satuanunit_id)))
     LEFT JOIN satuanunit_m sat_konv1 ON ((kontraksupplierdetail_m.satuankecil_id = sat_konv1.satuanunit_id)))
     LEFT JOIN satuanunit_m sat_konv2 ON ((kontraksupplierdetail_m.satuankecil_id = sat_konv2.satuanunit_id)));");
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200730_035036_migrate_mhkn_20200730 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200730_035036_migrate_mhkn_20200730 cannot be reverted.\n";

        return false;
    }
    */
}
