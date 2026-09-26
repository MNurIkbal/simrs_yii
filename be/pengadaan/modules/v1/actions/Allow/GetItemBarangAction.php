<?php

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\MasterBarang;
use app\modules\v1\models\KonfigFarmasi;
use yii\db\Expression;

class GetItemBarangAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $term = strtolower($request->get('term', ''));
        
        $query = MasterBarang::find()->select([
            new Expression('sm.satuankonversibrg_id as sid'),
            new Expression('barang_m.barang_id AS id'),
            new Expression('barang_m.barang_kode AS kod'),
            new Expression('barang_m.barang_nama AS nma'),
            new Expression('TRIM(sm2.satuanunit_nama) AS sk'),
            new Expression('TRIM(sm3.satuanunit_nama) AS sb'),
            new Expression('sm3.satuanunit_id as sbid'),
            new Expression('sm.nilai_konversi AS konv'),
            new Expression("concat(1,' ', TRIM(sm3.satuanunit_nama), ' = ', sm.nilai_konversi, ' ' ,TRIM(sm2.satuanunit_nama)) AS lbl"),
        ])
        ->join('JOIN', 'satuankonversibrg_m sm', 'barang_m.barang_id = sm.barang_id')
        ->join('JOIN', 'satuanunit_m sm2', 'sm.satuankecil_id = sm2.satuanunit_id')
        ->join('JOIN', 'satuanunit_m sm3', 'sm.satuanbesar_id = sm3.satuanunit_id')
        ->where([
            'sm.is_active' => true,
            'sm.is_deleted' => false,
            'barang_m.is_active' => true,
            'barang_m.is_deleted' => false,
        ])
        ->andWhere(['LIKE', 'LOWER(barang_m.barang_nama)', $term])
        ->orWhere(['LIKE', 'LOWER(barang_m.barang_kode)', $term])
        ->orderBy('barang_m.barang_nama, sm.nilai_konversi')
        ->limit(15)
        ->asArray()->all();

        $list_barang = ArrayHelper::index($query, 'sid', 'id');
        $list_barang_id = array_keys($list_barang);
        $list_konversi_master = $this->listNilaiKonversiMaster($list_barang_id);
        
        $config = KonfigFarmasi::find()->select(['is_large_unit_pr'])->asArray()->one();
        $satuan_digunakan = 'sb';
        if(!$config['is_large_unit_pr']) {
            $satuan_digunakan = 'sk';
        }

        $grouping = [];
        foreach ($list_barang as $id_barang => $barang) {
            $first =  $barang[array_keys($barang)[0]];
            $grouping[$id_barang] = [
                'id' => $first['id'],
                'nma' => $first['nma'],
                'kod' => $first['kod'],
                'satuan' => [],
                'label_master' => !empty($list_konversi_master[$first['id']]) ? trim($list_konversi_master[$first['id']]['konversi']) : "-",
            ];

            foreach($barang as $satuan) {
                $selected = 0;
                if( count($barang) == 1 ) {
                    $selected = 1;
                } elseif($satuan_digunakan == "sb") {
                    if($satuan['konv'] > 1) {
                        $selected = 1;
                    }
                } elseif($satuan_digunakan == "sk") {
                    if($satuan['konv'] == 1) {
                        $selected = 1;
                    }
                }

                $grouping[$id_barang]['satuan'][] = [
                    'sbid' => $satuan['sbid'],
                    'sb' => $satuan['sb'],
                    'konv' => $satuan['konv'],
                    'lbl' => $satuan['lbl'],
                    'sel' => $selected
                ];   
            }
        }
        
        return $this->controller->responseJson(200, 'success', $grouping);
    }

    private function listNilaiKonversiMaster($list_barang_id) {
        $list_barang_master = [];
        if (!empty($list_barang_id)) {
            $query = MasterBarang::find()->select([
                "barang_m.barang_id", 
                "b.satuanunit_id", 
                "b.satuanunit_nama", 
                "c.satuanunit_id", 
                "c.satuanunit_nama",
                new Expression("concat('1 ', b.satuanunit_nama, ' = ', barang_m.isi_satuan1, ' ', c.satuanunit_nama) as konversi"),
            ])
            ->join('JOIN', 'satuanunit_m b', 'b.satuanunit_id = barang_m.satuan1_id')
            ->join('JOIN', 'satuanunit_m c', 'c.satuanunit_id = barang_m.satuankecil_id')
            ->where(['IN', 'barang_m.barang_id', $list_barang_id])
            ->asArray()->all();
            
            $list_barang_master = ArrayHelper::index($query, 'barang_id');
        }
        return $list_barang_master;
    }
}
