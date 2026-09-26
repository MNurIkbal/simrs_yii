<?php

use yii\db\Migration;

/**
 * Class m190717_093812_rekap_kamar_tidur_function
 */
class m190717_093812_rekap_kamar_tidur_function extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
         DROP FUNCTION IF exists public.rekap_kamar_tidur();
        ');


          $this->execute('
         CREATE OR REPLACE FUNCTION public.rekap_kamar_tidur()
  RETURNS character varying AS
$BODY$
    DECLARE vout VARCHAR;
    vmonth VARCHAR;
    vyear VARCHAR;
    vcount int4;
    
BEGIN
    vout := \'Affected rows: \';
    vmonth := RIGHT(CONCAT(\'0\', (EXTRACT(MONTH FROM CURRENT_DATE))::VARCHAR),2);
    vyear := EXTRACT(YEAR FROM CURRENT_DATE)::VARCHAR;
    
    SELECT COUNT(*) INTO vcount
    FROM (
    SELECT
        x.ruangan_id,
        x.ruangan,
        COUNT(x.no_bed) as total_tt
    FROM
    (SELECT 
        kamarruangan_m.ruangan_id,
        ruangan_m.ruangan_nama as ruangan,
        kamarruangan_m.kamarruangan_nokamar as kamar,
        kamartempattidur_m.no_tempattidur as no_bed
        FROM kamartempattidur_m
        JOIN kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                                                     AND kamarruangan_m.is_deleted=FALSE 
                                                     AND kamarruangan_m.is_active=TRUE
        JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id 
                                            AND ruangan_m.is_deleted=FALSE
                                            AND ruangan_m.is_active=TRUE
        WHERE kamartempattidur_m.is_deleted=FALSE 
        AND kamartempattidur_m.is_active=true) x
        GROUP BY  x.ruangan_id, x.ruangan
    )y;
    
    
    DELETE FROM rekaptempattidur_r
    WHERE bulan = vmonth
    AND tahun = vyear;

    INSERT INTO rekaptempattidur_r(bulan, tahun, ruangan_id, jumlah_tt, created_date) 
    SELECT
        vmonth,
        vyear,
        x.ruangan_id,
        COUNT(x.no_bed) as total_tt,
        CURRENT_DATE
    FROM(
        SELECT 
            kamarruangan_m.ruangan_id,
            ruangan_m.ruangan_nama as ruangan,
            kamarruangan_m.kamarruangan_nokamar as kamar,
            kamartempattidur_m.no_tempattidur as no_bed
        FROM kamartempattidur_m
        JOIN kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
            AND kamarruangan_m.is_deleted=FALSE 
            AND kamarruangan_m.is_active=TRUE
        JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id 
            AND ruangan_m.is_deleted=FALSE
            AND ruangan_m.is_active=TRUE
        WHERE kamartempattidur_m.is_deleted=FALSE 
        AND kamartempattidur_m.is_active=true
    ) x
    GROUP BY  x.ruangan_id, x.ruangan;

    
    
    RETURN vout||vcount::VARCHAR;
END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
        ');


           $this->execute('
        ALTER FUNCTION public.rekap_kamar_tidur()
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190717_093812_rekap_kamar_tidur_function cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190717_093812_rekap_kamar_tidur_function cannot be reverted.\n";

        return false;
    }
    */
}
