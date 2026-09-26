<?php

use yii\db\Migration;

/**
 * Class m220318_073816_migrate_ODH396_trigger_no_invoicegabung
 */
class m220318_073816_migrate_ODH396_trigger_no_invoicegabung extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM penomoran_k
            WHERE penomoran_id = 109;
        ');

        $this->execute('
            INSERT INTO "public"."penomoran_k"("penomoran_id", "penomoran_nama", "prefix", "last_generate", "last_number", "flag_refresh", "konfig_penomoran") VALUES (109, \'Invoice Gabung\', \'IVG\', \'IVG20210002\', \'20210002\', \'0\', NULL);
        ');
        
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."no_invoicegabung"()
              RETURNS "pg_catalog"."trigger" AS $BODY$

            DECLARE
            vId integer := 109; --> Invoice Gabung(IVG)
            vPrefix VARCHAR;
            vNumber VARCHAR;
                    
            BEGIN
                SELECT 
                    (RIGHT(\'0\' || date_part(\'YEAR\',now()),4) ||
                    RIGHT(\'0\' || date_part(\'month\',now()),2) ||
                    RIGHT(\'0\' || date_part(\'DAY\',now()),2) ||
                    CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_invoicegabung), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, \'0\'))) last_no
                INTO 
                    vNumber
                FROM penomoran_k
                    LEFT JOIN invoicegabung_t ON invoicegabung_t.created_date::DATE = CURRENT_DATE
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
                    
                NEW.no_invoicegabung := TRIM(vPrefix) || vNumber;

                RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');

        $this->execute('
            DROP TRIGGER IF EXISTS "no_invoicegabung" ON "public"."invoicegabung_t";
        ');

        $this->execute('
            CREATE TRIGGER "no_invoicegabung" BEFORE INSERT ON "public"."invoicegabung_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."no_invoicegabung"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220318_073816_migrate_ODH396_trigger_no_invoicegabung cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220318_073816_migrate_ODH396_trigger_no_invoicegabung cannot be reverted.\n";

        return false;
    }
    */
}
