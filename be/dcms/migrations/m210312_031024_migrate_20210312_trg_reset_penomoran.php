<?php

use yii\db\Migration;

/**
 * Class m210312_031024_migrate_20210312_trg_reset_penomoran
 */
class m210312_031024_migrate_20210312_trg_reset_penomoran extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('DELETE FROM penomoran_k WHERE penomoran_id = 192; ');

        $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
(192, 'No Pemakaian Barang', 'PMB', 'PMB202103120001', '202103120001', '0');");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"no_pemakaianbarang\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
vId integer := 192; --> No Pemakaian Barang (PMB)
vPrefix VARCHAR;
vNumber VARCHAR;
    
BEGIN
    SELECT 
        (RIGHT('0' || date_part('YEAR',now()),4) ||
        RIGHT('0' || date_part('month',now()),2) ||
        RIGHT('0' || date_part('DAY',now()),2) ||
        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pemakaianbarang), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
    INTO 
        vNumber
    FROM penomoran_k
        LEFT JOIN pemakaianbarang_t ON pemakaianbarang_t.created_date::DATE = CURRENT_DATE
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
        
    NEW.no_pemakaianbarang := TRIM(vPrefix) || vNumber;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."no_pemakaianbarang"() OWNER TO "postgres";');

        $this->execute('CREATE TRIGGER "no_pemakaianbarang" BEFORE INSERT ON "public"."pemakaianbarang_t"
                FOR EACH ROW
                EXECUTE PROCEDURE "public"."no_pemakaianbarang"();');

     
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210312_031024_migrate_20210312_trg_reset_penomoran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210312_031024_migrate_20210312_trg_reset_penomoran cannot be reverted.\n";

        return false;
    }
    */
}
