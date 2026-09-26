<?php

use yii\db\Migration;

/**
 * Class m190327_095412_fgethargajualobat
 */
class m190327_095412_fgethargajualobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."fgethargajualobat"("vobatalkes_id" int4)
              RETURNS "pg_catalog"."float8" AS $BODY$
            DECLARE vharga_jual float8;
            BEGIN
                SELECT
                    (((
                    obatalkes_m.harganetto + (( obatalkes_m.harganetto * fgetpersenmargin ( obatalkes_m.harganetto )) / ( 100 ) :: DOUBLE PRECISION )) - (((
                    obatalkes_m.harganetto + (( obatalkes_m.harganetto * fgetpersenmargin ( obatalkes_m.harganetto )) / ( 100 ) :: DOUBLE PRECISION )) * konfigfarmasi_k.persen_diskon 
                    ) / ( 100 ) :: DOUBLE PRECISION 
                    )) + ((((
                    obatalkes_m.harganetto + (( obatalkes_m.harganetto * fgetpersenmargin ( obatalkes_m.harganetto )) / ( 100 ) :: DOUBLE PRECISION )) - (((
                    obatalkes_m.harganetto + (( obatalkes_m.harganetto * fgetpersenmargin ( obatalkes_m.harganetto )) / ( 100 ) :: DOUBLE PRECISION )) * konfigfarmasi_k.persen_diskon 
                    ) / ( 100 ) :: DOUBLE PRECISION 
                    )) * konfigfarmasi_k.persenppn 
                    ) / ( 100 ) :: DOUBLE PRECISION 
                    )) AS harga_jual 
                INTO vharga_jual
                FROM
                    obatalkes_m
                    JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                    JOIN lookup_m ven ON obatalkes_m.ven = ven.lookup_id
                    JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted =
                    FALSE LEFT JOIN satuanunit_m satuan_kecil ON obatalkes_m.satuankecil_id = satuan_kecil.satuanunit_id 
                WHERE obatalkes_m.obatalkes_id = vobatalkes_id ;
                
                RETURN COALESCE(vharga_jual,0);
                    
            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_095412_fgethargajualobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_095412_fgethargajualobat cannot be reverted.\n";

        return false;
    }
    */
}
