<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\KonfigFarmasi;

class GetItemAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', '');
        $term = strtolower($request->get('term', ''));
        $is_consignment = $request->get('is_consignment', false);
        $satuan_digunakan = 'sb';
        $limit = $request->get('limit', 50);
        $config = KonfigFarmasi::find()->select(['is_large_unit_pr'])->asArray()->one();

        if (!$config['is_large_unit_pr']) {
            $satuan_digunakan = 'sk';
        }

        if ($term != '') {
            $term = str_replace("'", "''", $term); // escape query for single quote
        }

        if ($type == "barang") {
            $query = "
                SELECT
                    sm.satuankonversibrg_id as sid ,om.barang_id AS id,
                    om.barang_kode AS kod, om.barang_nama AS nma,
                    TRIM(sm2.satuanunit_nama) AS sk, TRIM(sm3.satuanunit_nama) AS sb,
                    sm3.satuanunit_id as sbid, sm.nilai_konversi AS konv,
                    concat(1,' ', TRIM(sm3.satuanunit_nama), ' = ', sm.nilai_konversi, ' ' ,TRIM(sm2.satuanunit_nama)) AS lbl
                FROM barang_m om
                JOIN satuankonversibrg_m sm ON om.barang_id = sm.barang_id 
                JOIN satuanunit_m sm2 ON sm.satuankecil_id = sm2.satuanunit_id
                JOIN satuanunit_m sm3 ON sm.satuanbesar_id = sm3.satuanunit_id
                WHERE (LOWER(om.barang_nama) LIKE '%$term%' OR LOWER(om.barang_kode) LIKE '%$term%')
                AND sm.is_active = true AND sm.is_deleted = false AND om.is_deleted = false
                AND om.is_active = true
                ORDER BY om.barang_nama , sm.nilai_konversi
                LIMIT 120;
            ";
        } else {
            $query = "
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
                WHERE (LOWER(om.obatalkes_nama) LIKE '%$term%' OR LOWER(om.obatalkes_kode) LIKE '%$term%')
                AND sm.is_active = true AND sm.is_deleted = false AND om.is_deleted = false AND om.is_consigment = $is_consignment
                AND om.is_active = true
                ORDER BY om.obatalkes_nama , sm.nilai_konversi
                LIMIT 120;
            ";
        }

        $connection = Yii::$app->db;
        $data = $connection->createCommand($query)->queryAll();
        $data_array = ArrayHelper::index($data, 'sid', 'id');
        $list_data_obat = array_keys($data_array);
        $list_konversi_master = $this->listNilaiKonversiMaster($list_data_obat);

        $grouping = [];
        foreach ($data_array as $id_obat => $obat) {
            $first =  $obat[array_keys($obat)[0]];
            $grouping[$id_obat] = [
                'id' => $first['id'],
                'nma' => $first['nma'],
                'kod' => $first['kod'],
                'satuan' => [],
                'label_master' => !empty($list_konversi_master[$first['id']]) ? trim($list_konversi_master[$first['id']]['konversi']) : "-",
            ];
            foreach ($obat as $satuan) {
                $selected = 0;
                if (count($obat) == 1) {
                    $selected = 1;
                } elseif ($satuan_digunakan == "sb") {
                    if ($satuan['konv'] > 1) {
                        $selected = 1;
                    }
                } elseif ($satuan_digunakan == "sk") {
                    if ($satuan['konv'] == 1) {
                        $selected = 1;
                    }
                }

                $grouping[$id_obat]['satuan'][] = [
                    'sbid' => $satuan['sbid'],
                    'sb' => $satuan['sb'],
                    'konv' => $satuan['konv'],
                    'lbl' => $satuan['lbl'],
                    'sel' => $selected
                ];
            }
        }

        // limit response data
        $sliceGrouping = array_slice($grouping, 0, $limit);
        return $this->controller->responseJson(200, 'Sukses', $sliceGrouping);
    }

    private function listNilaiKonversiMaster($list_data_obat)
    {
        $list_obat_master = [];
        if (!empty($list_data_obat)) {
            $query = new \yii\db\Query();
            $result = $query->select(["a.obatalkes_id", "b.satuanunit_id", "b.satuanunit_nama", "c.satuanunit_id", "c.satuanunit_nama", "a.kemasan_besar", "concat('1 ', b.satuanunit_nama, ' = ', a.kemasan_besar, ' ', c.satuanunit_nama) AS konversi"])
                ->from('obatalkes_m a')
                ->join('LEFT JOIN', 'satuanunit_m b', 'b.satuanunit_id = a.satuanbesar_id')
                ->join('LEFT JOIN', 'satuanunit_m c', 'c.satuanunit_id = a.satuankecil_id')
                ->where(['in', 'a.obatalkes_id', $list_data_obat]);
            $list_obat_master = ArrayHelper::index($result->all(), 'obatalkes_id');
        }

        return $list_obat_master;
    }
}
