<?php

use yii\db\Migration;

/**
 * Class m220624_061236_migrate_cssd_trg_no_pengajuan_sterilisasi
 */
class m220624_061236_migrate_cssd_trg_no_pengajuan_sterilisasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER IF EXISTS "no_pengajuan_sterilisasi" ON "public"."cssd_t";
            ');
        
        $this->execute("
            DROP FUNCTION if exists public.no_pengajuan_sterilisasi;
            ");

        $this->execute("
                CREATE OR REPLACE FUNCTION \"public\".\"no_pengajuan_sterilisasi\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE
    vId integer := 196; --> No Pengajuan Sterilisasi
    vPrefix VARCHAR;
    vNumber VARCHAR;
    
BEGIN
SELECT 
        (RIGHT('0' || date_part('YEAR',now()),2) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pengajuan_sterilisasi), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN cssd_t ON cssd_t.created_date::DATE = CURRENT_DATE
    WHERE penomoran_id = 196; 
        
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
        
    NEW.no_pengajuan_sterilisasi := TRIM(vPrefix) || vNumber;

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
        echo "m220624_061236_migrate_cssd_trg_no_pengajuan_sterilisasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_061236_migrate_cssd_trg_no_pengajuan_sterilisasi cannot be reverted.\n";

        return false;
    }
    */
}
