<?php

use yii\db\Migration;

/**
 * Class m220202_142720_migrate_skema_fisioterapi
 */
class m220202_142720_migrate_skema_fisioterapi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."programterapi_t" (
          "programterapi_id" serial8,
          "pendaftaran_id" int4,
          "pasienmasukpenunjang_id" int4,
          "pasien_id" int4,
          "tgl_permintaan" timestamp(6),
          "diagnosa" text COLLATE "pg_catalog"."default",
          "frekuensi" float4,
          "catatan" text COLLATE "pg_catalog"."default",
          "dokterperujuk_id" int4,
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
          CONSTRAINT "programterapi_t_pkey" PRIMARY KEY ("programterapi_id")
          )
          ;
        ');

        $this->execute('
            ALTER TABLE "public"."programterapi_t" 
            OWNER TO "postgres";
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."programterapidetail_t" (
          "programterapidetail_id" serial8,
          "programterapi_id" int4,
          "daftartindakan_id" int4,
          "frekuensi" float4,
          "catatan" text COLLATE "pg_catalog"."default",
          "dokterperujuk_id" int4,
          "terapis_id" int4,
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
          "tipepaket_id" int4,
          "tariftindakan_id" int4,
          "pemeriksaanfisio_id" int4,
          CONSTRAINT "programterapidetail_t_pkey" PRIMARY KEY ("programterapidetail_id")
          )
          ;
        ');

        $this->execute('
            ALTER TABLE "public"."programterapidetail_t" 
            OWNER TO "postgres";
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."pemeriksaanfisio_m" (
          "pemeriksaanfisio_id" serial4,
          "daftartindakan_id" int4,
          "jenispemeriksaanfisio_id" int4,
          "pemeriksaanfisio_kode" varchar(50) COLLATE "pg_catalog"."default",
          "pemeriksaanfisio_nama" varchar(100) COLLATE "pg_catalog"."default",
          "kelompokpemeriksaanfisio_id" int4,
          "tipepaket_id" int4,
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
          CONSTRAINT "pemeriksaanfisio_m_pkey" PRIMARY KEY ("pemeriksaanfisio_id")
          )
          ;
        ');

        $this->execute('
            ALTER TABLE "public"."pemeriksaanfisio_m" 
            OWNER TO "postgres";
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."kelompokpemeriksaanfisio_m" (
          "kelompokpemeriksaanfisio_id" serial4,
          "kode_kelompok" varchar(25) COLLATE "pg_catalog"."default" NOT NULL,
          "nama_kelompok" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
          "keterangan_kelompok" varchar(255) COLLATE "pg_catalog"."default",
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
          CONSTRAINT "kelompokpemeriksaanfisio_m_pkey" PRIMARY KEY ("kelompokpemeriksaanfisio_id")
          )
          ;
        ');

        $this->execute('
            ALTER TABLE "public"."kelompokpemeriksaanfisio_m" 
            OWNER TO "postgres";
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."jenispemeriksaanfisio_m" (
          "jenispemeriksaanfisio_id" serial4,
          "jenispemeriksaanfisio_kode" varchar(10) COLLATE "pg_catalog"."default" NOT NULL,
          "jenispemeriksaanfisio_nama" varchar(100) COLLATE "pg_catalog"."default" NOT NULL,
          "jenispemeriksaanfisio_namalain" varchar(100) COLLATE "pg_catalog"."default",
          "kelompokpemeriksaanfisio_id" int4 NOT NULL,
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
          CONSTRAINT "jenispemeriksaanfisio_m_pkey" PRIMARY KEY ("jenispemeriksaanfisio_id")
          )
          ;
        ');

        $this->execute('
            ALTER TABLE "public"."jenispemeriksaanfisio_m" 
            OWNER TO "postgres";
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."soapfisioterapi_t" (
          "soapfisioterapi_id" serial8,
          "pendaftaran_id" int4 NOT NULL,
          "pasien_id" int4 NOT NULL,
          "terapis_id" int4 NOT NULL,
          "tgl_soapfisioterapi" timestamp(0) DEFAULT (\'now\'::text)::date,
          "subject" text COLLATE "pg_catalog"."default",
          "object" text COLLATE "pg_catalog"."default",
          "assesment" text COLLATE "pg_catalog"."default",
          "planning" text COLLATE "pg_catalog"."default",
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
          CONSTRAINT "soapfisioterapi_t_pkey" PRIMARY KEY ("soapfisioterapi_id")
          )
          ;
        ');

        $this->execute('
            ALTER TABLE "public"."soapfisioterapi_t" 
            OWNER TO "postgres";
        ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."jadwalterapifisio_t" (
          "jadwalterapifisio_id" serial8,
          "programterapi_id" int4,
          "programterapidetail_id" int4,
          "pasien_id" int4,
          "tgl_penjadwalan" timestamp(6),
          "tgl_realisasi" timestamp(6),
          "pendaftaran_id" int4,
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
          "kunjunganke" int4,
          CONSTRAINT "jadwalterapifisio_t_pkey" PRIMARY KEY ("jadwalterapifisio_id")
          )
          ;
        ');

        $this->execute('
            ALTER TABLE "public"."jadwalterapifisio_t" 
            OWNER TO "postgres";
        ');

        $this->execute('
            ALTER TABLE "public"."tindakanpelayanan_t" 
            ADD COLUMN IF NOT EXISTS "programterapidetail_id" int4,
            ADD COLUMN IF NOT EXISTS "programterapi_id" int4;
        ');


        $this->execute('ALTER TABLE "public"."pasienmasukpenunjang_t" 
            ADD COLUMN IF NOT EXISTS "programterapi_id" int4
        ');

        $this->execute('ALTER TABLE "public"."programterapi_t" 
          ADD COLUMN IF NOT EXISTS "status_fisio" varchar(50) COLLATE "pg_catalog"."default";
        ');

        $this->execute('COMMENT ON COLUMN "public"."programterapi_t"."status_fisio" IS \'Lihat di lookup_m type status_fisio\';
          ');

        $this->execute('ALTER TABLE "public"."soapfisioterapi_t" 
          ADD COLUMN IF NOT EXISTS "a_diag_utama" json,
          ADD COLUMN IF NOT EXISTS "a_diag_penyerta" json,
          ADD COLUMN IF NOT EXISTS "catatan_dokter" text COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "instruksi" text COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "programterapi_id" int4,
          ADD COLUMN IF NOT EXISTS "pasienmasukpenunjang_id" int4,
          ADD COLUMN IF NOT EXISTS "tipe_instalasi" varchar(8) COLLATE "pg_catalog"."default";
        ');


        $this->execute("
            DELETE from lookup_m where lookup_id IN (1121,1122,1123);
        ");

        $this->execute("
            INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1123, 'status_fisio', 'Selesai', 'Selesai', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1122, 'status_fisio', 'Belem Bayar', 'Belum Bayar', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (1121, 'status_fisio', 'Belum Periksa', 'Belum Periksa', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'tipe_instalasi';
        ");

        $this->execute("
            INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES 
            ('tipe_instalasi', 1, 'Memisahkan Instalasi RJ', 'RJ', NULL, NULL),
            ('tipe_instalasi', 3, 'Memisahkan Instalasi RI', 'RI', NULL, NULL);
            ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'instalasi_fisio';
        ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'instalasi_ri';
        ");

        $this->execute("
            DELETE from lookuptransaksi_m where kode_transaksi = 'instalasi_rj';
        ");

        $this->execute("
          INSERT INTO public.lookuptransaksi_m(kode_transaksi, kode_id, kode_fungsi, additional_value, kode_nama, kode_singkatan) VALUES 
          ('instalasi_fisio', 7, 'Instalasi Fisioterapi', NULL, NULL, NULL),
          ('instalasi_ri', 3, 'Instalasi Rawat Inap', NULL, NULL, NULL),
          ('instalasi_rJ', 1, 'Instalasi Rawat Jalan', NULL, NULL, NULL);
        ");

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."programterapirajal_r" (
          "programterapirajal_id" serial4,
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

        $this->execute('
          ALTER TABLE "public"."soapfisioterapi_t" 
          ADD COLUMN IF NOT EXISTS "is_edit" bool DEFAULT false;
        ');

        $this->execute('
          COMMENT ON COLUMN "public"."soapfisioterapi_t"."is_edit" IS \'sebagai penanda diagnosa yang di coret\';
        ');

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

        $this->execute('
          ALTER TABLE "public"."jadwalterapifisio_t" 
          ADD COLUMN IF NOT EXISTS "status_kunjungan_fisio" int4;
          ');

        $this->execute('
          COMMENT ON COLUMN "public"."jadwalterapifisio_t"."status_kunjungan_fisio" IS \'lookup_m = status_kunjungan_fisio\';
          ');
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220202_142720_migrate_skema_fisioterapi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220202_142720_migrate_skema_fisioterapi cannot be reverted.\n";

        return false;
    }
    */
}
