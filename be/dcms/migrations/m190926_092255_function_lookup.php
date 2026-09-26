<?php

use yii\db\Migration;

/**
 * Class m190926_092255_function_lookup
 */
class m190926_092255_function_lookup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /*fgetvaluelookup*/
        $this->execute('DROP FUNCTION if exists public.fgetvaluelookup(integer);');

        $this->execute('
            CREATE OR REPLACE FUNCTION public.fgetvaluelookup(vlookup_id integer)
  RETURNS character varying AS
$BODY$
DECLARE vlookup_value VARCHAR;
BEGIN
    SELECT lookup_value INTO vlookup_value
    FROM lookup_m
    WHERE lookup_id = vlookup_id;
    
    RETURN vlookup_value;
        
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');

        $this->execute('ALTER FUNCTION public.fgetvaluelookup(integer)
  OWNER TO postgres;');


     /*fgetvaluelookupkeperawatan*/
        $this->execute('DROP FUNCTION if exists public.fgetvaluelookupkeperawatan(integer);');

        $this->execute('
            CREATE OR REPLACE FUNCTION public.fgetvaluelookupkeperawatan(vlookup_id integer)
  RETURNS character varying AS
$BODY$
DECLARE vlookup_value VARCHAR;
BEGIN
    SELECT lookup_value INTO vlookup_value
    FROM lookupkeperawatan_m
    WHERE lookupkeperawatan_id = vlookup_id;
    
    RETURN vlookup_value;
        
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');

        $this->execute('ALTER FUNCTION public.fgetvaluelookupkeperawatan(integer)
  OWNER TO postgres;');

    /*fgetkodelookup*/
        $this->execute('DROP FUNCTION if exists public.fgetkodelookup(integer);');
 
        $this->execute('
            CREATE OR REPLACE FUNCTION public.fgetkodelookup(vlookup_id integer)
  RETURNS character varying AS
$BODY$
DECLARE vlookup_kode VARCHAR;
BEGIN
    SELECT lookup_kode INTO vlookup_kode
    FROM lookup_m
    WHERE lookup_id = vlookup_id;
    
    RETURN vlookup_kode;
        
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');
 
        $this->execute('ALTER FUNCTION public.fgetkodelookup(integer)
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190926_092255_function_lookup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190926_092255_function_lookup cannot be reverted.\n";

        return false;
    }
    */
}
