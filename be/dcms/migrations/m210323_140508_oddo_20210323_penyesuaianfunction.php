<?php

use yii\db\Migration;

/**
 * Class m210323_140508_oddo_20210323_penyesuaianfunction
 */
class m210323_140508_oddo_20210323_penyesuaianfunction extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE from penomoran_k WHERE penomoran_id in (69,70);');

        $this->execute("
    INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
    (70, 'Obatalkes Pasien', 'OBP', 'OBT202132301440', '01440', '0'),
    (69, 'Tindakan Pelayanan', 'TND', 'TND202103234787', '202103234787', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_obatalkespasien\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE
vId integer := 70; --> Obatalkes Pasien (OBP)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_obatalkespasien), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN obatalkespasien_t ON obatalkespasien_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.no_obatalkespasien := TRIM(vPrefix) || vNumber;
    
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."generate_obatalkespasien"() OWNER TO "postgres";');

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"generate_tindakanpelayanan\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
DECLARE
vId integer := 69; --> Tindakan Pelayanan (TND)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_tindakanpelayanan), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN tindakanpelayanan_t ON tindakanpelayanan_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.no_tindakanpelayanan := TRIM(vPrefix) || vNumber;
    
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."generate_tindakanpelayanan"() OWNER TO "postgres";');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210323_140508_oddo_20210323_penyesuaianfunction cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210323_140508_oddo_20210323_penyesuaianfunction cannot be reverted.\n";

        return false;
    }
    */
}
