<?php

namespace Doco\components;
use Yii;

class DocoAntrian
{
     /**
     * @todo get sisa antrian
     * @param integer id = jenisntrian_id
     * @author Randy Vianda Putra <randy@docotel.com>
     */
    public static function getSisaAntrian($id)
    {
        if (empty($id)) return [];
        $startDate = date('Y-m-d') . ' 00:00:00';
        $endDate = date('Y-m-d') . ' 23:59:59';
        $connection = Yii::$app->db;
        $sql = "SELECT
                sub.*,
                k.group_carabayar,
                k.kode_antrian AS kode,
                k.klasifikasipasien_nama,
            CASE

                    WHEN ( sub.sisa IS NULL ) THEN
                    0 ELSE sub.sisa
                END AS total_sisa
            FROM
                (
                SELECT
                    groupcarabayar_id,
                    klasifikasipasien_id,
                    COUNT ( antrian_id ) AS sisa
                FROM
                    antrian_t
                WHERE
                    status_antrian = 0
                    AND jenisantrian_id = {$id}
                    AND is_online = FALSE
                    AND tgl_antrian BETWEEN '{$startDate}' AND '{$endDate}'
                GROUP BY
                    groupcarabayar_id,
                    klasifikasipasien_id
                ) AS sub
                RIGHT JOIN konfigantrian_v K ON sub.groupcarabayar_id = k.group_id
                AND sub.klasifikasipasien_id = k.klasifikasipasien_id
            WHERE
                k.jenisantrian_id = {$id}
            ORDER BY
                k.kode_antrian,
                k.klasifikasipasien_nama,
                k.group_carabayar
        ";
        $data = $connection->createCommand($sql)->queryAll();
        $group_data = [];
        foreach ($data as $key => $value) {
            $group_data[$value['kode']][] = $value;
        }

        return $group_data;
    }
}
