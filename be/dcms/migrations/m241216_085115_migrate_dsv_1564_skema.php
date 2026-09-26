<?php

use yii\db\Migration;

/**
 * Class m241216_085115_migrate_dsv_1564_skema
 */
class m241216_085115_migrate_dsv_1564_skema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    /**
	     * Table
	     */
		
		$this->execute('ALTER TABLE "public"."pendaftaran_t" ADD COLUMN if not exists "asuransi_id" int4;');
		
		$this->execute('
		CREATE TABLE IF NOT EXISTS "public"."asuransi_t" (
		  "asuransi_id" serial8 NOT NULL ,
		  "provider_id" int4,
		  "penjamin_id" int4,
		  "no_klaim" varchar(20) COLLATE "pg_catalog"."default",
		  "no_kartu" varchar(20) COLLATE "pg_catalog"."default",
		  "no_polis" varchar(20) COLLATE "pg_catalog"."default",
		  "no_sep" varchar(20) COLLATE "pg_catalog"."default",
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
		  "additional_pendaftaran" text COLLATE "pg_catalog"."default",
		  CONSTRAINT "asuransi_t_pkey" PRIMARY KEY ("asuransi_id")
		);');
        $this->execute("ALTER TABLE public.asuransi_t ADD IF NOT EXISTS is_cob bool DEFAULT false NULL;");
        $this->execute("ALTER TABLE public.asuransi_t ADD IF NOT EXISTS benefit_id varchar NULL;");		
		$this->execute('ALTER TABLE "public"."penjamin_m" ADD COLUMN if not exists "konfigasuransi_id" int4;');
		
		$this->execute('CREATE TABLE IF NOT EXISTS "public"."konfigasuransi_k" (
		  "konfigasuransi_id" serial4 NOT NULL,
		  "provider_id" int4 NOT NULL,
		  "base_url" text COLLATE "pg_catalog"."default",
		  "auth" text COLLATE "pg_catalog"."default",
		  "url_cek_eligibilitas" text COLLATE "pg_catalog"."default",
		  "url_autentikasi" text COLLATE "pg_catalog"."default",
		  "url_get_referensi" text COLLATE "pg_catalog"."default",
		  "url_referensi_benefit" text COLLATE "pg_catalog"."default",
		  "url_pengesahan" text COLLATE "pg_catalog"."default",
		  "url_pendaftaran" text COLLATE "pg_catalog"."default",
		  "url_jaminan" text COLLATE "pg_catalog"."default",
		  "url_kunjungan" text COLLATE "pg_catalog"."default",
		  "session_expired" varchar(10) COLLATE "pg_catalog"."default",
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
		  "provider_code" text COLLATE "pg_catalog"."default",
		  CONSTRAINT "konfigasuransi_k_pkey" PRIMARY KEY ("konfigasuransi_id")
		  );');
		  
		  $this->execute('CREATE TABLE IF NOT EXISTS "public"."log_asuransitransaksi_t" (
  		  "log_asuransitransaksi_id" serial8 NOT NULL ,
  		  "is_batal" bool DEFAULT false,
  		  "tgl_batal" timestamp(6),
 		  "pegawaibatal_id" int4,
  		  "is_pengesahan" bool DEFAULT false,
  		  "tgl_pengesahan" timestamp(6),
  		  "pegawaipengesahan_id" int4,
  		  "is_resend" bool DEFAULT false,
  		  "tgl_resend" timestamp(6),
  		  "pegawairesend_id" int4,
  		  "asuransi_id" int4,
  		  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  		  "created_by" int4,
  		  "modified_count" int4,
  		  "last_modified_date" timestamp(6),
  		  "last_modified_by" int4,
  		  "is_deleted" bool NOT NULL DEFAULT false,
  		  "is_active" bool NOT NULL DEFAULT true,
  		  "deleted_date" timestamp(6),
  		  "deleted_by" int4,
  		  CONSTRAINT "log_asuransitransaksi_t_pkey" PRIMARY KEY ("log_asuransitransaksi_id")
	  		);;');

		$this->execute('CREATE TABLE IF NOT EXISTS "public"."integrasi_tindakanobat_asuransi_t" (
  	  "integrasi_tindakanobat_asuransi_id" serial8 NOT NULL  ,
  	"pendaftaran_id" int4,
  	"asuransi_id" int4,
  	"pelayanan_id" int4,
  	"is_sending" bool DEFAULT false,
  	"created_by" int4,
  	"modified_count" int4,
  	"last_modified_date" timestamp(6),
  	"last_modified_by" int4,
  	"is_deleted" bool NOT NULL DEFAULT false,
  	"is_active" bool NOT NULL DEFAULT true,
  	"deleted_date" timestamp(6),
  	"deleted_by" int4,
  	"type" varchar COLLATE "pg_catalog"."default",
  	"item_code" varchar COLLATE "pg_catalog"."default",
  	"qty" float8,
  	"subtotal" numeric(18,2),
  		CONSTRAINT "integrasi_tindakanobat_asuransi_t_pk" PRIMARY KEY ("integrasi_tindakanobat_asuransi_id")
	);');
	
    /**
     * view
     */
	
    $this->execute("DROP VIEW IF EXISTS dashboardintegrasiasuransi_v");
    $dashboardintegrasiasuransi_v = file_get_contents(__DIR__ . '/definitions/dashboardintegrasiasuransi_v.sql');
    $this->execute($dashboardintegrasiasuransi_v);
	
	
    $this->execute("DROP VIEW IF EXISTS rekap_asuransi_data_v");
    $rekap_asuransi_data_v = file_get_contents(__DIR__ . '/definitions/rekap_asuransi_data_v.sql');
    $this->execute($rekap_asuransi_data_v);

	$this->execute('
		CREATE INDEX IF NOT EXISTS "pendaftaran_asuransi_id_idx" ON "public"."pendaftaran_t" USING btree (
			"asuransi_id"
		);
	');
    }

    /**
     * View
     */

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241216_085115_migrate_dsv_1564_skema cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241216_085115_migrate_dsv_1564_skema cannot be reverted.\n";

        return false;
    }
    */
}
