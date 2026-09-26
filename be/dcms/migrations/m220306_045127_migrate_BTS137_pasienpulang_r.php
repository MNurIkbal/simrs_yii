<?php

use yii\db\Migration;

/**
 * Class m220306_045127_migrate_BTS137_pasienpulang_r
 */
class m220306_045127_migrate_BTS137_pasienpulang_r extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."pasienpulang_r" (
          "pasienpulangrekap_id" serial4,
          "pasienpulang_id" int4,
          "pasien_id" int4,
          "pasienbatalpulang_id" int4,
          "pendaftaran_id" int4,
          "pasienadmisi_id" int4,
          "carakeluar_id" int4 NOT NULL,
          "kondisikeluar_id" int4,
          "tglpasienpulang" timestamp(6) NOT NULL,
          "ruanganakhir_id" int8,
          "penerima_pasien" varchar(100) COLLATE "pg_catalog"."default",
          "lama_rawat" int2 DEFAULT 0,
          "satuan_lamarawat" varchar(50) COLLATE "pg_catalog"."default" NOT NULL DEFAULT \'Hari\'::character varying,
          "is_meninggal" bool NOT NULL DEFAULT false,
          "keterangan_keluar" text COLLATE "pg_catalog"."default",
          "hari_perawatan" int4 DEFAULT 0,
          "satuan_hariperawatan" varchar(50) COLLATE "pg_catalog"."default" NOT NULL DEFAULT \'Hari\'::character varying,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "tgl_meninggal" timestamp(6),
          "is_rencanakontrol" bool,
          "tgl_rencanakontrol" timestamp(0),
          "pasiendirujukkeluar_id" int4,
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          "dpjp_id" int4,
          "konsulpoli_id" int4,
          "tempattidurtujuan_id" int4,
          "catatan_lain" text COLLATE "pg_catalog"."default",
          "status_jenazah" varchar(50) COLLATE "pg_catalog"."default",
          "tgl_kremasi" timestamp(6),
          "nama_pemeriksa_jenazah" varchar(100) COLLATE "pg_catalog"."default",
          "kualifikasi_pemeriksa" varchar(50) COLLATE "pg_catalog"."default",
          "waktu_pemeriksaan_jenazah" timestamp(6),
          "dasar_diagnosis" varchar(50) COLLATE "pg_catalog"."default",
          "kelompok_kematian" varchar(50) COLLATE "pg_catalog"."default",
          "tempat_kematian" varchar(100) COLLATE "pg_catalog"."default",
          "penyebab_langsung" varchar(100) COLLATE "pg_catalog"."default",
          "penyebab_antara" varchar(100) COLLATE "pg_catalog"."default",
          "penyebab_dasar" varchar(100) COLLATE "pg_catalog"."default",
          "kondisi_lain" varchar(100) COLLATE "pg_catalog"."default",
          "penyebab_utama_bayi" varchar(100) COLLATE "pg_catalog"."default",
          "penyebab_utama_ibu" varchar(100) COLLATE "pg_catalog"."default",
          "penyebab_lain_bayi" varchar(100) COLLATE "pg_catalog"."default",
          "penyebab_lain_ibu" varchar(100) COLLATE "pg_catalog"."default",
          "pihak_menerima" varchar(100) COLLATE "pg_catalog"."default",
          "hubungan_penerima" varchar(50) COLLATE "pg_catalog"."default",
          "infeksi" text COLLATE "pg_catalog"."default",
          "no_surat_kematian" varchar(100) COLLATE "pg_catalog"."default",
          CONSTRAINT "pasienpulang_r_pkey" PRIMARY KEY ("pasienpulangrekap_id")
          )
          ;');

        $this->execute('ALTER TABLE "public"."pasienpulang_r" 
          OWNER TO "postgres";
        ');

        $this->execute('COMMENT ON COLUMN "public"."pasienpulang_r"."dpjp_id" IS \'diisi saat pulang ranap\';
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220306_045127_migrate_BTS137_pasienpulang_r cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220306_045127_migrate_BTS137_pasienpulang_r cannot be reverted.\n";

        return false;
    }
    */
}
