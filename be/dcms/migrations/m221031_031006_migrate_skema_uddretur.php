<?php

use yii\db\Migration;

/**
 * Class m221031_031006_migrate_skema_uddretur
 */
class m221031_031006_migrate_skema_uddretur extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."uddretur_t" (
  "uddretur_id" serial NOT NULL ,
  "udd_id" int4,
  "tglretur" timestamp(6),
  "nouddretur" varchar(15) COLLATE "pg_catalog"."default",
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
  "alasan_retur" varchar(32) COLLATE "pg_catalog"."default",
  CONSTRAINT "uddretur_t_pkey" PRIMARY KEY ("uddretur_id")
);        ');

        $this->execute('
            CREATE TABLE IF NOT EXISTS "public"."uddreturdetail_t" (
  "uddreturdetail_id" serial NOT NULL,
  "uddretur_id" int4,
  "obatalkes_id" int4,
  "qtyretur" int4,
  "satuaninput_id" int4,
  "satuankecil_id" int4,
  "satuankonversi_id" int4,
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
  "udd_detail_id" int4,
  CONSTRAINT "uddreturdetail_t_pkey" PRIMARY KEY ("uddreturdetail_id")
);       ');

$this->execute('
    ALTER TABLE "public"."stokobatalkes_t" ADD IF NOT EXISTS "uddreturdetail_id" int4;
');

$this->execute('
    DELETE from penomoran_k WHERE penomoran_id = 205;
');

$this->execute('
	INSERT INTO "public"."penomoran_k" ("penomoran_id", "penomoran_nama", "prefix", "last_generate", "last_number", "flag_refresh", "konfig_penomoran") VALUES (205, \'udd_retur\', \'RTU\', \'RTU202209120004\', \'202209120004\', \'0\', NULL);
    
');
		
		$this->execute('
		DROP TRIGGER IF EXISTS no_uddretur ON uddretur_t;
		');
   
        $this->execute('DROP FUNCTION if exists public.no_uddretur();');
		
		

        $this->execute("
            CREATE OR REPLACE FUNCTION public.no_uddretur()
  RETURNS pg_catalog.trigger AS \$BODY\$
	
DECLARE
	vId integer := 205; --> Retur UDD
	vPrefix VARCHAR;
	vNumber VARCHAR;
	
BEGIN
	SELECT 
		(RIGHT('0' || date_part('YEAR',now()),4) ||
		RIGHT('0' || date_part('month',now()),2) ||
		RIGHT('0' || date_part('DAY',now()),2) ||
		CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(nouddretur), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
	INTO 
		vNumber
	FROM penomoran_k
		LEFT JOIN uddretur_t ON uddretur_t.created_date::DATE = CURRENT_DATE
	WHERE penomoran_id = 205; 
		
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
		
	NEW.nouddretur := TRIM(vPrefix) || vNumber;

	RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

 $this->execute('

	 CREATE TRIGGER "no_uddretur" BEFORE INSERT ON "public"."uddretur_t"
	 FOR EACH ROW
	 EXECUTE PROCEDURE "public"."no_uddretur"();
	 ');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221031_031006_migrate_skema_uddretur cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221031_031006_migrate_skema_uddretur cannot be reverted.\n";

        return false;
    }
    */
}
