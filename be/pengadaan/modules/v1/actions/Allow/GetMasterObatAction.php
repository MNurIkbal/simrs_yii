<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\ObatAlkes;

class GetMasterObatAction extends Action
{
    public function run()
    {
        try {
            $connection = Yii::$app->db;
            $request = Yii::$app->request;
            $term = strtolower($request->get('term', ''));
            $nameOnly = $request->get('name_only');
            $codeOnly = $request->get('code_only');
            $ruangan_id = $request->get('ruangan_id', Yii::$app->jwt->ruangan_id);
            $selectionTerm = " (LOWER(om.obatalkes_nama) LIKE '%$term%' OR LOWER(om.obatalkes_kode) LIKE '%$term%') ";
            if($nameOnly) $selectionTerm = " LOWER(om.obatalkes_nama) LIKE '%$term%' ";
            if($codeOnly) $selectionTerm = " LOWER(om.obatalkes_kode) LIKE '%$term%' ";
            $data = $connection->createCommand("
                SELECT
                    sm.satuankonversi_id as sid ,om.obatalkes_id AS id,
                    om.obatalkes_kode AS kod, om.obatalkes_nama AS nma,
                    TRIM(sm2.satuanunit_nama) AS sk, TRIM(sm3.satuanunit_nama) AS sb,
                    sm3.satuanunit_id as sbid, sm.nilai_konversi AS konv,
                    concat(1,' ', TRIM(sm3.satuanunit_nama), ' = ', sm.nilai_konversi, ' ' ,TRIM(sm2.satuanunit_nama)) AS lbl
                FROM obatalkes_m om
                JOIN satuankonversi_m sm ON om.obatalkes_id = sm.obatalkes_id
                JOIN satuanunit_m sm2 ON sm.satuankecil_id = sm2.satuanunit_id
                JOIN satuanunit_m sm3 ON sm.satuanbesar_id = sm3.satuanunit_id
                WHERE $selectionTerm
                AND sm.is_active = true
                AND om.is_active = true
                AND om.is_deleted = false
                ORDER BY om.obatalkes_nama , sm.nilai_konversi
                LIMIT 15
            ")
            ->queryAll();

            $data_array = ArrayHelper::index($data, 'sid', 'id');
            $grouping = [];
            foreach ($data_array as $id_obat => $obat) {
                $first =  $obat[array_keys($obat)[0]];
                $grouping[$id_obat] = [
                    'id' => $first['id'],
                    'nma' => $first['nma'],
                    'kod' => $first['kod'],
                    'satuan' => []
                ];
                foreach ($obat as $satuan) {
                    $grouping[$id_obat]['satuan'][] = [
                        'sbid' => $satuan['sbid'],
                        'sb' => $satuan['sb'],
                        'konv' => $satuan['konv'],
                        'lbl' => $satuan['lbl'],
                    ];
                }
            }
            return $grouping;
        } catch (\Exception $e) {
            return [];
        }
    }
}