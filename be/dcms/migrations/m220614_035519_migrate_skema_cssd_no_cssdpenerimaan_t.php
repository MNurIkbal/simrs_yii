<?php

use yii\db\Migration;

/**
 * Class m220614_035519_migrate_skema_cssd_no_cssdpenerimaan_t
 */
class m220614_035519_migrate_skema_cssd_no_cssdpenerimaan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS "public"."cssdpenerimaanunit_t" (
          "cssdpenerimaanunit_id" serial8 NOT NULL,
          "cssd_id" int4,
          "nocssdpenerimaan" varchar(255) COLLATE "pg_catalog"."default",
          "tglterima" timestamp(6),
          "ruanganpenerima_id" int4,
          "ruanganasal_id" int4,
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          "peg_mengetahui_id" int4,
          "peg_menyetujui_id" int4,
          CONSTRAINT "cssdpenerimaanunit_t_pkey" PRIMARY KEY ("cssdpenerimaanunit_id")
          )
          ;
          ');

        $this->execute('ALTER TABLE "public"."cssdpenerimaanunit_t" 
          OWNER TO "postgres";
          ');

        $this->execute("
            DROP FUNCTION if exists public.no_cssdpenerimaan_t;
            ");

        $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"no_cssdpenerimaan_t\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE
    vId integer := 201; --> No Terima Sterilisasi CSSD - PMK
    vPrefix VARCHAR;
    vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(nocssdpenerimaan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN cssdpenerimaanunit_t ON cssdpenerimaanunit_t.created_date::DATE = CURRENT_DATE
    WHERE penomoran_id = 195; 
        
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
        
    NEW.nocssdpenerimaan := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
            ");


        $this->execute('CREATE TRIGGER "no_cssdpenerimaan_t" BEFORE INSERT ON "public"."cssdpenerimaanunit_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."no_cssdpenerimaan_t"();
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220614_035519_migrate_skema_cssd_no_cssdpenerimaan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220614_035519_migrate_skema_cssd_no_cssdpenerimaan_t cannot be reverted.\n";

        return false;
    }
    */
}
