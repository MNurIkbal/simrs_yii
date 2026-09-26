<?php

use yii\db\Migration;

/**
 * Class m190423_102348_rekaptempattidur_f_add
 */
class m190423_102348_rekaptempattidur_f_add extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP FUNCTION IF EXISTS rekaptempattidur_f(VARCHAR);
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION rekaptempattidur_f(vtahun varchar)
            RETURNS TABLE(
                ruangan_id int4, 
                ruangan_nama VARCHAR,
                bulan_01 int4,
                bulan_02 int4,
                bulan_03 int4,
                bulan_04 int4,
                bulan_05 int4,
                bulan_06 int4,
                bulan_07 int4,
                bulan_08 int4,
                bulan_09 int4,
                bulan_10 int4,
                bulan_11 int4,
                bulan_12 int4
            ) AS $BODY$
            DECLARE vbulan VARCHAR; 
            DECLARE vtahun_current VARCHAR; 
            BEGIN
                vbulan := RIGHT(CONCAT(\'0\',date_part(\'month\', CURRENT_DATE)),2);
                vtahun_current := date_part(\'year\', CURRENT_DATE);
                RETURN QUERY 
                SELECT 
                    ruangan_m.ruangan_id::int4,
                    ruangan_m.ruangan_nama::VARCHAR,
                    COALESCE(bulan1.jumlah_tt::int4,0) AS bulan1,
                    COALESCE(bulan2.jumlah_tt::int4,0) AS bulan2,
                    COALESCE(bulan3.jumlah_tt::int4,0) AS bulan3,
                    COALESCE(bulan4.jumlah_tt::int4,0) AS bulan4,
                    COALESCE(bulan5.jumlah_tt::int4,0) AS bulan5,
                    COALESCE(bulan6.jumlah_tt::int4,0) AS bulan6,
                    COALESCE(bulan7.jumlah_tt::int4,0) AS bulan7,
                    COALESCE(bulan8.jumlah_tt::int4,0) AS bulan8,
                    COALESCE(bulan9.jumlah_tt::int4,0) AS bulan9,
                    COALESCE(bulan10.jumlah_tt::int4,0) AS bulan10,
                    COALESCE(bulan11.jumlah_tt::int4,0) AS bulan11,
                    COALESCE(bulan12.jumlah_tt::int4,0) AS bulan12
                FROM ruangan_m
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'01\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current) 
                )bulan1 ON ruangan_m.ruangan_id = bulan1.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'02\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan2 ON ruangan_m.ruangan_id = bulan2.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'03\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan3 ON ruangan_m.ruangan_id = bulan3.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'04\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan4 ON ruangan_m.ruangan_id = bulan4.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'05\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan5 ON ruangan_m.ruangan_id = bulan5.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'06\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan6 ON ruangan_m.ruangan_id = bulan6.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'07\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan7 ON ruangan_m.ruangan_id = bulan7.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'08\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan8 ON ruangan_m.ruangan_id = bulan8.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'09\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan9 ON ruangan_m.ruangan_id = bulan9.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'10\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan10 ON ruangan_m.ruangan_id = bulan10.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'11\'
                    AND CONCAT(bulan, tahun) <> CONCAT(vbulan,vtahun_current)
                )bulan11 ON ruangan_m.ruangan_id = bulan11.ruangan_id
                LEFT JOIN (
                    SELECT 
                        rekaptempattidur_r.bulan,
                        rekaptempattidur_r.tahun,
                        rekaptempattidur_r.ruangan_id,
                        rekaptempattidur_r.jumlah_tt
                    FROM rekaptempattidur_r
                    WHERE rekaptempattidur_r.tahun = vtahun
                    AND bulan = \'12\'
                    AND bulan <> vbulan
                )bulan12 ON ruangan_m.ruangan_id = bulan12.ruangan_id
                WHERE ruangan_m.instalasi_id = 3
                ORDER BY ruangan_id;
            END; $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100
              ROWS 1000


        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
            DROP FUNCTION IF EXISTS rekaptempattidur_f(VARCHAR);
        ');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190423_102348_rekaptempattidur_f_add cannot be reverted.\n";

        return false;
    }
    */
}
