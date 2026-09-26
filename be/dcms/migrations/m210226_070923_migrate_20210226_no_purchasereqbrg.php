<?php

use yii\db\Migration;

/**
 * Class m210226_070923_migrate_20210226_no_purchasereqbrg
 */
class m210226_070923_migrate_20210226_no_purchasereqbrg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"no_purchasereqbrg\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 191; --No Purchase Requese Barang 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    v_day VARCHAR;
    v_reset VARCHAR;
    
BEGIN
    SELECT date_part('DAY',now()) INTO v_day;
    
    IF(v_day = '1')
    THEN
        SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) AS VARCHAR(4)), 4, '0'))
            INTO v_reset
        FROM penomoran_k WHERE penomoran_id = vId;
        
        IF(v_reset <> '0001')
        THEN
            UPDATE penomoran_k SET 
                last_generate = '0001'
            WHERE penomoran_id = vId;
        END IF;
        
    END IF;
    
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 4) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vDate,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vDate || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;
    
    NEW.no_pr = v_Nomor;

    RETURN NEW;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('ALTER FUNCTION "public"."no_purchasereqbrg"() OWNER TO "postgres";');

$this->execute('CREATE TRIGGER "no_purchasereqbrg" BEFORE INSERT ON "public"."purchasereqbrg_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."no_purchasereqbrg"();');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_070923_migrate_20210226_no_purchasereqbrg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_070923_migrate_20210226_no_purchasereqbrg cannot be reverted.\n";

        return false;
    }
    */
}
