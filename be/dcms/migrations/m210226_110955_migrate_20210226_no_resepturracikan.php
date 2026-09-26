<?php

use yii\db\Migration;

/**
 * Class m210226_110955_migrate_20210226_no_resepturracikan
 */
class m210226_110955_migrate_20210226_no_resepturracikan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"no_resepturracikan\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 189; --No Reseptur Racikan 
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
        SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) AS VARCHAR(5)), 5, '0'))
            INTO v_reset
        FROM penomoran_k WHERE penomoran_id = vId;
        
        IF(v_reset <> '00001')
        THEN
            UPDATE penomoran_k SET 
                last_generate = '00001'
            WHERE penomoran_id = vId;
        END IF;
        
    END IF;
    
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 5) AS INT), 0) + 1 AS VARCHAR(5)), 5, '0')) last_no
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
    
    NEW.no_racikan = v_Nomor;

    RETURN NEW;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('ALTER FUNCTION "public"."no_resepturracikan"() OWNER TO "postgres";');

    $this->execute('CREATE TRIGGER "no_racikan" BEFORE INSERT ON "public"."resepturracikan_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."no_resepturracikan"();');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210226_110955_migrate_20210226_no_resepturracikan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210226_110955_migrate_20210226_no_resepturracikan cannot be reverted.\n";

        return false;
    }
    */
}
