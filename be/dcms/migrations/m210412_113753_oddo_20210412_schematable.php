<?php

use yii\db\Migration;

/**
 * Class m210412_113753_oddo_20210412_schematable
 */
class m210412_113753_oddo_20210412_schematable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
   
    $this->execute('CREATE TABLE "public"."adjusmenbarangkeluar_r" (
          "id" serial8,
          "adjusmenbarangkeluar_id" int4,
          "adjusmenbarang_id" int4,
          "barang_id" int4,
          "qty" int4 DEFAULT 0,
          "satuankecil_id" int4,
          "alasan" text COLLATE "pg_catalog"."default",
          "satuanbesar_id" int4,
          "qty_konversi" int4 DEFAULT 0,
          "satuankonversibrg_id" int4,
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
          "no_batch" varchar(100) COLLATE "pg_catalog"."default",
          "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
          "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
          "is_sent" bool DEFAULT false,
          "is_sending" bool DEFAULT false,
          "id_sync_sercon" text COLLATE "pg_catalog"."default",
          "sync_respon" text COLLATE "pg_catalog"."default",
          CONSTRAINT "adjusmenbarangkeluar_r_pkey" PRIMARY KEY ("id")
        )
        ;');

    $this->execute('ALTER TABLE "public"."adjusmenbarangkeluar_r" OWNER TO "postgres";');

    $this->execute('CREATE TABLE "public"."adjusmenbarangmasuk_r" (
  "id" serial8,
  "adjusmenbarangmasuk_id" int4,
  "adjusmenbarang_id" int4,
  "barang_id" int4,
  "tgl_kadaluarsa" timestamp(0),
  "qty" int4,
  "satuankecil_id" int4,
  "harga_netto" float8,
  "satuanbesar_id" int4,
  "qty_konversi" int4,
  "satuankonversibrg_id" int4,
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
  "no_batch" varchar(100) COLLATE "pg_catalog"."default",
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "adjusmenbarangmasuk_r_pkey" PRIMARY KEY ("id")
)
;');

    $this->execute('ALTER TABLE "public"."adjusmenbarangmasuk_r" OWNER TO "postgres";');

    $this->execute('CREATE TABLE "public"."pemakaianbarangdetail_r" (
  "id" serial8,
  "pemakaianbarangdetail_id" int4,
  "pemakaianbarang_id" int4,
  "barang_id" int4,
  "jumlah_pakai" int4,
  "harga_netto" float8,
  "ppn" float8,
  "disc" float8,
  "hpp" float8,
  "harga_jual" float8,
  "catatan_barang" varchar(200) COLLATE "pg_catalog"."default",
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
  "satuanbesar_id" int4,
  "jumlah_input" float8,
  "satuankecil_id" int4,
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "pemakaianbarangdetail_r_pkey" PRIMARY KEY ("id")
)
;');

    $this->execute('ALTER TABLE "public"."pemakaianbarangdetail_r" OWNER TO "postgres";');

    $this->execute('CREATE TABLE "public"."pemusnahanbarangdetail_r" (
  "id" serial8,
  "pemusnahanbarangdetail_id" int4,
  "pemusnahanbarang_id" int4,
  "barang_id" int4,
  "jumlah" float8 DEFAULT 0,
  "tglkadaluarsa" date,
  "nobatch" varchar(200) COLLATE "pg_catalog"."default",
  "kondisibarang" text COLLATE "pg_catalog"."default",
  "harganetto" float8 DEFAULT 0,
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
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "pemusnahanbarangdetail_r_pkey" PRIMARY KEY ("id")
)
;');
    $this->execute('ALTER TABLE "public"."pemusnahanbarangdetail_r" OWNER TO "postgres";');

    $this->execute('CREATE TABLE "public"."penerimaanbarang_r" (
  "id" serial8,
  "penerimaanbarang_id" int4,
  "validasipobarang_id" int4,
  "no_penerimaan" varchar(100) COLLATE "pg_catalog"."default",
  "tgl_penerimaan" timestamp(0) DEFAULT (\'now\'::text)::date,
  "supplier_id" int4,
  "no_suratjalan" varchar(100) COLLATE "pg_catalog"."default",
  "tgl_suratjalan" timestamp(0) DEFAULT (\'now\'::text)::date,
  "no_faktur" varchar(100) COLLATE "pg_catalog"."default",
  "diterima_oleh" int4,
  "ruanganpenerima_id" int4,
  "peg_mengetahui" int4,
  "peg_menyetujui" int4,
  "upload_berkas" text COLLATE "pg_catalog"."default",
  "catatan_berkas" text COLLATE "pg_catalog"."default",
  "catatan" text COLLATE "pg_catalog"."default",
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
  "is_verifikasi" int2,
  "no_faktur_sementara" varchar(100) COLLATE "pg_catalog"."default",
  "status_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "penerimaanbarang_r_pkey" PRIMARY KEY ("id")
)
;');
    $this->execute('ALTER TABLE "public"."penerimaanbarang_r" OWNER TO "postgres";');

    $this->execute('CREATE TABLE "public"."penerimaanbarangdetail_r" (
  "id" serial8,
  "penerimaanbarangdetail_id" int4,
  "penerimaanbarang_id" int4,
  "validasipobarangdetail_id" int4,
  "barang_id" int4,
  "qty_po" int4,
  "po_balance" int4,
  "qty_diterima" int4,
  "s_konversibrg_id" int4,
  "tgl_kadaluarsa" date,
  "no_batch" varchar(100) COLLATE "pg_catalog"."default",
  "harga" float8 DEFAULT 0,
  "discount" float4 DEFAULT 0,
  "discount_rp" float8 DEFAULT 0,
  "jumlah" float8 DEFAULT 0,
  "keterangan" text COLLATE "pg_catalog"."default",
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
  "status_rekap" text COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(0) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sending" bool DEFAULT false,
  "is_sent" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "penerimaanbarangdetail_r_pkey" PRIMARY KEY ("id")
)
;');

    $this->execute('ALTER TABLE "public"."penerimaanbarangdetail_r" OWNER TO "postgres";');

    $this->execute('CREATE TABLE "public"."returpenerimaanbarang_r" (
  "id" serial8,
  "returpenerimaanbarang_id" int4,
  "pegawairetur_id" int4,
  "ruanganretur_id" int4,
  "no_returpenerimaanbarang" varchar(50) COLLATE "pg_catalog"."default",
  "tgl_retur" timestamp(6),
  "alasan_retur" varchar(100) COLLATE "pg_catalog"."default",
  "keterangan_retur" text COLLATE "pg_catalog"."default",
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
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "returpenerimaanbarang_r_pkey" PRIMARY KEY ("id")
)
;');

    $this->execute('ALTER TABLE "public"."returpenerimaanbarang_r" OWNER TO "postgres";');

    $this->execute('CREATE TABLE "public"."returpenerimaanbarangdetail_r" (
  "id" serial8,
  "returpenerimaanbarangdetail_id" int4,
  "returpenerimaanbarang_id" int4,
  "penerimaanbarang_id" int4,
  "penerimaanbarangdetail_id" int4,
  "penerimaansuppbrgdetail_id" int4,
  "barang_id" int4,
  "satuanbesar_id" int4,
  "tgl_kadaluarsa" timestamp(0),
  "qty_retur" int4,
  "qty_input" int4,
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
  "no_batch" varchar(100) COLLATE "pg_catalog"."default",
  "stokbarang_id" int4,
  "keterangan_rekap" varchar(255) COLLATE "pg_catalog"."default",
  "tgl_proses" timestamp(6) DEFAULT (to_char(now(), \'YYYY-MM-DD hh:mm:ss\'::text))::timestamp without time zone,
  "is_sent" bool DEFAULT false,
  "is_sending" bool DEFAULT false,
  "id_sync_sercon" text COLLATE "pg_catalog"."default",
  "sync_respon" text COLLATE "pg_catalog"."default",
  CONSTRAINT "returpenerimaanbarangdetail_r_pkey" PRIMARY KEY ("id")
)
;');

    $this->execute('ALTER TABLE "public"."returpenerimaanbarangdetail_r" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210412_113753_oddo_20210412_schematable cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210412_113753_oddo_20210412_schematable cannot be reverted.\n";

        return false;
    }
    */
}
