<?php

use yii\db\Migration;

/**
 * Class m190919_065118_func_area
 */
class m190919_065118_func_area extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('DROP FUNCTION if exists public.fgetnamaarea(integer, integer, integer, integer);');

         $this->execute('
            CREATE OR REPLACE FUNCTION public.fgetnamaarea(xpropinsi_id integer, xkabupaten_id integer, xkecamatan_id integer, xkelurahan_id integer)
  RETURNS character varying AS
$BODY$
DECLARE varea_name VARCHAR;
BEGIN
    IF(COALESCE(xpropinsi_id,0) <> 0)
    THEN
        SELECT propinsi_nama INTO varea_name
        FROM propinsi_m
        WHERE propinsi_id = xpropinsi_id;
    END IF;
    
    IF(COALESCE(xkabupaten_id,0) <> 0)
    THEN
        SELECT kabupaten_nama INTO varea_name
        FROM kabupaten_m
        WHERE kabupaten_id = xkabupaten_id;
    END IF;
    
    IF(COALESCE(xkecamatan_id,0) <> 0)
    THEN
        SELECT kecamatan_nama INTO varea_name
        FROM kecamatan_m
        WHERE kecamatan_id = xkecamatan_id;
    END IF;
    
    IF(COALESCE(xkelurahan_id,0) <> 0)
    THEN
        SELECT kelurahan_nama INTO varea_name
        FROM kelurahan_m
        WHERE kelurahan_id = xkelurahan_id;
    END IF;
    
    RETURN varea_name;
        
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;');


          $this->execute('ALTER FUNCTION public.fgetnamaarea(integer, integer, integer, integer)
  OWNER TO postgres;');

     


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190919_065118_func_area cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190919_065118_func_area cannot be reverted.\n";

        return false;
    }
    */
}
