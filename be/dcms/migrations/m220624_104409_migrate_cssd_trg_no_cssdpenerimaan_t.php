<?php

use yii\db\Migration;

/**
 * Class m220624_104409_migrate_cssd_trg_no_cssdpenerimaan_t
 */
class m220624_104409_migrate_cssd_trg_no_cssdpenerimaan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopengajuansterilisasi_v;');
        $this->execute('DROP VIEW if exists public.infopengajuansterilisasi_;');
        $this->execute('DROP VIEW if exists public.lapcssdsterilisasi_v;');

        $this->execute('DROP TABLE IF EXISTS cssdpenerimaan_t');

        $this->execute('DROP TABLE IF EXISTS cssdpenerimaandetail_t');

        $this->execute('DROP TRIGGER IF EXISTS "no_cssdpenerimaan_t" ON "public"."cssdpenerimaanunit_t";
            ');
        
        $this->execute("
            DROP FUNCTION if exists public.no_cssdpenerimaan_t;
            ");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_cssdpenerimaan_t\"()
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_104409_migrate_cssd_trg_no_cssdpenerimaan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_104409_migrate_cssd_trg_no_cssdpenerimaan_t cannot be reverted.\n";

        return false;
    }
    */
}
