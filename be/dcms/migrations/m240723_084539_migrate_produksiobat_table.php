<?php

use yii\db\Migration;

/**
 * Class m240723_084539_migrate_produksiobat_table
 */
class m240723_084539_migrate_produksiobat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    /**
	     * {Pemesanan produksi}
	     */
		
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."pemesananproduksiobat_t" (
		  "pemesananproduksiobat_id" serial8,
		  "instalasi_id" int4,
		  "ruangan_id" int4,
		  "nopemesanan" varchar(50) COLLATE "pg_catalog"."default",
		  "tglpemesanan" timestamp(6),
		  "pegawaipemesanan_id" int4,
		  "status_pemesanan" int4,
		  "tgl_aprove" timestamp(6),
		  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
		  "created_by" int4,
		  "modified_count" int4,
		  "last_modified_date" timestamp(6),
		  "last_modified_by" int4,
		  "deleted_date" timestamp(6),
		  "deleted_by" int4,
		  "pegawaiaprove_id" int4,
		  "is_deleted" bool DEFAULT false,
		  "is_active" bool DEFAULT true,
		  "catatan_bahanbaku" text COLLATE "pg_catalog"."default",
		  CONSTRAINT "pemesananproduksiobat_t_pkey" PRIMARY KEY ("pemesananproduksiobat_id")
		);');
		
        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 238;');
        $this->execute('INSERT INTO "public"."penomoran_k" ("penomoran_id", "penomoran_nama", "prefix", "last_generate", "last_number", "flag_refresh", "konfig_penomoran") VALUES (238, \'Pemesanan Produksi Obat\', \'PMP\', NULL, NULL, \'0\', NULL);');
		
        $this->execute('DROP TRIGGER if exists no_pemesananproduksi ON public.pemesananproduksiobat_t;');
        $this->execute('DROP FUNCTION if exists public.no_pemesananproduksi();');
	   		
		$this->execute("CREATE OR REPLACE FUNCTION \"public\".\"no_pemesananproduksi\"()
  		   				RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
      		   				DECLARE
  		   				vId integer := 238; --> No Pemesanan Produksi Obat (PMP)
  		   				vPrefix VARCHAR;
  		   				vNumber VARCHAR;
    
  		   				BEGIN
      		   				SELECT 
          		   				(RIGHT('0' || date_part('YEAR',now()),4) ||
          		   				RIGHT('0' || date_part('month',now()),2) ||
          		   				RIGHT('0' || date_part('DAY',now()),2) ||
          		   				CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(nopemesanan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
      		   				INTO 
          		   				vNumber
      		   				FROM penomoran_k
          		   				LEFT JOIN pemesananproduksiobat_t ON pemesananproduksiobat_t.created_date::DATE = CURRENT_DATE
      		   				WHERE penomoran_id = vId; 
        
      		   				SELECT 
          		   				prefix 
      		   				INTO 
          		   				vPrefix 
      		   				FROM penomoran_k 
      		   				WHERE penomoran_id = vId; 
    
      		   				UPDATE penomoran_k SET
          		   				last_number = vNumber,
          		   				last_generate = TRIM(vPrefix) || vNumber
    		   				WHERE penomoran_id = vId;
        
      		   				NEW.nopemesanan := TRIM(vPrefix) || vNumber;

      		   				RETURN NEW;
  		   				END
  		   				\$BODY\$
    		   				LANGUAGE plpgsql VOLATILE
    		   				COST 100;"
	);
		
		 $this->execute('CREATE TRIGGER "no_pemesananproduksi" BEFORE INSERT ON "public"."pemesananproduksiobat_t" FOR EACH ROW EXECUTE PROCEDURE "public"."no_pemesananproduksi"();');
	
		
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."pemesananproduksiobatdetail_t" (
  		  "pemesananproduksiobatdetail_id" serial8,
  		  "pemesananproduksiobat_id" int4,
  		  "obatalkes_id" int4,
  		  "qty" float8,
  		  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  		  "created_by" int4,
  		  "modified_count" int4,
  		  "last_modified_date" timestamp(6),
  		  "last_modified_by" int4,
  		  "deleted_date" timestamp(6),
  		  "deleted_by" int4,
  		  "is_deleted" bool DEFAULT false,
  		  "is_active" bool DEFAULT true,
  		  "satuan_id" int4,
  		  "additional_data" text COLLATE "pg_catalog"."default",
  		  "qty_konversi" int4,
  		  CONSTRAINT "pemesananproduksiobatdetail_t_pkey" PRIMARY KEY ("pemesananproduksiobatdetail_id")
		  );');
		  
		  
  	    /**
  	     * {skema produksi Obat}
  	     */
		
        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 239;');
        $this->execute('INSERT INTO "public"."penomoran_k" ("penomoran_id", "penomoran_nama", "prefix", "last_generate", "last_number", "flag_refresh", "konfig_penomoran") VALUES (239, \'no produksi obatalkes\', \'POP\', NULL, NULL, \'0\', NULL);');
		
		$this->execute('
		CREATE TABLE  IF NOT EXISTS "public"."produksiobatalkes_t" (
		  "produksiobatalkes_id" serial4,
		  "noproduksiobat" varchar(20) COLLATE "pg_catalog"."default",
		  "tglproduksiobat" timestamp(6),
		  "pemesananproduksiobat_id" int4,
		  "status_produksi" int4,
		  "is_approve" bool DEFAULT false,
		  "pegawai_approve" int4,
		  "tgl_approve" timestamp(6),
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
		  CONSTRAINT "produksiobatalkes_t_pkey" PRIMARY KEY ("produksiobatalkes_id")
		);');
		
        $this->execute('DROP TRIGGER if exists no_produksiobat ON public.produksiobatalkes_t;');
        $this->execute('DROP FUNCTION if exists public.no_produksiobat();');
		
		$this->execute("
			CREATE OR REPLACE FUNCTION \"public\".\"no_produksiobat\"()
			  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
			    DECLARE
			vId integer := 239; --> no produksi obatalkes (POP)
			vPrefix VARCHAR;
			vNumber VARCHAR;
    
			BEGIN
			    SELECT 
			        (RIGHT('0' || date_part('YEAR',now()),4) ||
			        RIGHT('0' || date_part('month',now()),2) ||
			        RIGHT('0' || date_part('DAY',now()),2) ||
			        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(noproduksiobat), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
			    INTO 
			        vNumber
			    FROM penomoran_k
			        LEFT JOIN produksiobatalkes_t ON produksiobatalkes_t.created_date::DATE = CURRENT_DATE
			    WHERE penomoran_id = vId; 
        
			    SELECT 
			        prefix 
			    INTO 
			        vPrefix 
			    FROM penomoran_k 
			    WHERE penomoran_id = vId; 
    
			    UPDATE penomoran_k SET
			        last_number = vNumber,
			        last_generate = TRIM(vPrefix) || vNumber
			  WHERE penomoran_id = vId;
        
			    NEW.noproduksiobat := TRIM(vPrefix) || vNumber;

			    RETURN NEW;
			END
			\$BODY\$
			  LANGUAGE plpgsql VOLATILE
			  COST 100;");
	
	 $this->execute('CREATE TRIGGER "no_produksiobat" BEFORE INSERT ON "public"."produksiobatalkes_t" FOR EACH ROW EXECUTE PROCEDURE "public"."no_produksiobat"();');
		  
	
	$this->execute('
  		 CREATE TABLE "public"."produksiobatalkesdetail_t" (
  		  "produksiobatalkesdetail_id" serial4,
  		  "produksiobatalkes_id" int4,
  		  "obatalkes_id" int4,
  		  "satuankecil_id" int4,
  		  "qty_produksi" int4,
  		  "pemesananproduksiobatdetail_id" int8,
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
  		 CONSTRAINT "produksiobatalkesdetail_t_pkey" PRIMARY KEY ("produksiobatalkesdetail_id")
	);');
	
	$this->execute('
	  	CREATE TABLE "public"."produksiobatalkesbahanbaku_t" (
  		  "produksiobatalkesbahanbaku_id" serial8,
  		  "produksiobatalkesdetail_id" int4,
  		  "obatalkes_id" int4,
  		  "satuankecil_id" int4,
  		  "qty_obat" float8,
  		  "harganetto" float8,
  		  "harganetto_satuan" float8,
  		  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  		  "created_by" int4,
  		  "modified_count" int4,
  		  "last_modified_date" timestamp(6),
  		  "last_modified_by" int4,
  		  "is_deleted" bool NOT NULL DEFAULT false,
  		  "is_active" bool NOT NULL DEFAULT true,
  		  "deleted_date" timestamp(6),
  		  "deleted_by" int4,
  		  "additional_data" text COLLATE "pg_catalog"."default",
  		  CONSTRAINT "produksiobatalkesbahanbaku_t_pkey" PRIMARY KEY ("produksiobatalkesbahanbaku_id")
	); ');
	
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240723_084539_migrate_produksiobat_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240723_084539_migrate_produksiobat_table cannot be reverted.\n";

        return false;
    }
    */
}
