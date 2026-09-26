<?php

use yii\db\Migration;

/**
 * Class m241107_004355_hotfix_function_generate_nobuktibayar
 */
class m241107_004355_hotfix_function_generate_nobuktibayar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."generate_nobuktibayar"()
  RETURNS "pg_catalog"."trigger" AS $BODY$DECLARE
    vId integer := 2; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    
BEGIN
    
    SELECT 
        prefix,
        date_part(\'YEAR\',now()) as year, 
        RIGHT(\'0\' || date_part(\'month\',now()),2) as month,
        RIGHT(\'0\' || date_part(\'DAY\',now()),2) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, \'0\')) last_no
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
    
    NEW.nobuktibayar = v_Nomor;

    RETURN NEW;
END$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241107_004355_hotfix_function_generate_nobuktibayar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241107_004355_hotfix_function_generate_nobuktibayar cannot be reverted.\n";

        return false;
    }
    */
}
