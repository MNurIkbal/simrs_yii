<?php

/**
 * @author Randy Vianda Putra
 * @todo Query Apotek
 * @copyright 13 Maret 2018
 */

namespace app\components;

use yii\db\QueryBuilder;
use yii;

class QueryApotek
{

    public static function queryDetailReturByPenjualan(array $obatalkes_id, $ruangan_id, array $obatalkespasien_id)
    {
        $obatalkes_id = implode(',', $obatalkes_id);
        $obatalkespasien_id = implode(',', $obatalkespasien_id);
        $connection = Yii::$app->db;
        $sql = "WITH infopenjualanresepdetail AS (
	            SELECT * FROM infopenjualanresepdetail_v 
                WHERE obatalkespasien_id IN ({$obatalkespasien_id})
            ) SELECT
                stokobat.obatalkes_id,
                stokobat.id_stok AS stokobatalkesasal_id,
                SUM (
                    stokobat.qtystok_out - stokobat.qtystok_in
                ) AS qty_oa,
                stokobat.tglkadaluarsa,
                stokobat.nobatch,
                i.obatalkespasien_id,
                i.obatalkes_nama,
                i.noresep,
                p.jenispenjualan,
                p.totalhargajual,
                i.signa_oa,
                i.hargajual_oa,
                i.harganetto_oa,
                i.hargasatuan_oa,
                i.rke,
                i.satuankecil_id,
                p.penjualanresep_id,
                p.tglpenjualan,
                stokobat.harganetto,
                stokobat.persendiscount,
                stokobat.jmldiscount,
                stokobat.persenppn,
                stokobat.jmlppn,
                stokobat.persenmargin,
                stokobat.jmlmargin
            FROM (
                SELECT
                    (
                        CASE
                        WHEN stokobatalkesasal_id IS NULL THEN
                            stokobatalkes_id
                        ELSE
                            stokobatalkesasal_id
                        END
                    ) AS id_stok,
                    obatalkes_id,
                    qtystok_in,
                    qtystok_out,
                    nobatch,
                    tglkadaluarsa,
                    obatalkespasien_id,
                    COALESCE(harganetto,0) as harganetto,
                    COALESCE(persendiscount,0) as persendiscount,
                    COALESCE(jmldiscount,0) as jmldiscount,
                    COALESCE(persenppn,0) as persenppn,
                    COALESCE(jmlppn,0) as jmlppn,
                    COALESCE(persenmargin,0) as persenmargin,
                    COALESCE(jmlmargin,0) as jmlmargin
                FROM stokobatalkes_t
                WHERE obatalkes_id IN ({$obatalkes_id})
                AND obatalkespasien_id IN ({$obatalkespasien_id})
            ) AS stokobat
            JOIN infopenjualanresepdetail i ON i.obatalkespasien_id = stokobat.obatalkespasien_id
            JOIN penjualanresep_t P ON P .penjualanresep_id = i.penjualanresep_id
            WHERE qty_oa > 0
            GROUP BY
                stokobat.id_stok,
                stokobat.tglkadaluarsa,
                stokobat.obatalkes_id,
                stokobat.nobatch,
                i.obatalkespasien_id,
                i.obatalkes_nama,
                i.noresep,
                p.jenispenjualan,
                p.totalhargajual,
                i.signa_oa,
                i.hargajual_oa,
                i.harganetto_oa,
                i.hargasatuan_oa,
                i.rke,
                i.satuankecil_id,
                p.penjualanresep_id,
                P.tglpenjualan,
                stokobat.harganetto,
                stokobat.persendiscount,
                stokobat.jmldiscount,
                stokobat.persenppn,
                stokobat.jmlppn,
                stokobat.persenmargin,
                stokobat.jmlmargin
            ORDER BY
                stokobat.tglkadaluarsa ASC
        ";

        $data = $connection->createCommand($sql)->queryAll();

        return $data;
    }

    public static function queryQtyStokReturByPenjualan(array $obatalkes_id, $ruangan_id, array $obatalkespasien_id)
    {
        $obatalkes_id = implode(',', $obatalkes_id);
        $obatalkespasien_id = implode(',', $obatalkespasien_id);
        $connection = Yii::$app->db;
        $sql = "SELECT
                obatalkes_id,
                id_stok,
                SUM (
                    stokobat.qtystok_out - stokobat.qtystok_in
                ) AS total_stok,
                stokobat.tglkadaluarsa,
                nobatch
            FROM
                (
                    SELECT
                        (
                            CASE
                            WHEN stokobatalkesasal_id IS NULL THEN
                                stokobatalkes_id
                            ELSE
                                stokobatalkesasal_id
                            END
                        ) AS id_stok,
                        obatalkes_id,
                        qtystok_in,
                        qtystok_out,
                        nobatch,
                        tglkadaluarsa
                    FROM
                        stokobatalkes_t
                    WHERE
                        obatalkes_id IN ({$obatalkes_id})
                    AND obatalkespasien_id IN ({$obatalkespasien_id})
                ) AS stokobat
            GROUP BY
                stokobat.id_stok,
                stokobat.tglkadaluarsa,
                obatalkes_id,
                nobatch
            ORDER BY
                stokobat.tglkadaluarsa ASC
        ";

        $data = $connection->createCommand($sql)->queryAll();

        return $data;
    }

    public static function queryGetPenjualan($penjualan_id)
    {
        $connection = Yii::$app->db;
        $sql = "SELECT * FROM infopenjualanresepdetail_v WHERE penjualanresep_id = {$penjualan_id}";
        $data = $connection->createCommand($sql)->queryAll();

        return $data;
    }
}