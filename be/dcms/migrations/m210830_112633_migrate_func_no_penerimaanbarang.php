<?php

use yii\db\Migration;

/**
 * Class m210830_112633_migrate_func_no_penerimaanbarang
 */
class m210830_112633_migrate_func_no_penerimaanbarang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_penerimaanbarang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    vId integer := 165; --> Penerimaan Barang (TRB)
        vPrefix VARCHAR;
        vNumber VARCHAR;
        
BEGIN
---------------------------------------------no. penerimaan barang---------------------------------------------------       
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_penerimaan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN penerimaanbarang_t ON penerimaanbarang_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.no_penerimaan := TRIM(vPrefix) || vNumber;
    
    
    
     RETURN NEW;    
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
        
        $this->execute('ALTER FUNCTION "public"."no_penerimaanbarang"() OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210830_112633_migrate_func_no_penerimaanbarang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210830_112633_migrate_func_no_penerimaanbarang cannot be reverted.\n";

        return false;
    }
    */
}
