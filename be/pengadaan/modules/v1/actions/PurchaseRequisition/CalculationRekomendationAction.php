<?php

/**
 * @author : Iqbal Ramadhani (iqbal.ramdhani@sirs.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use app\modules\v1\models\BaseCalRoFn;
use app\modules\v1\models\BaseCalRoFnConfigurable;
use app\modules\v1\models\MovingCriteria;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\SatuanKonversiView;
use app\modules\v1\models\KetersediaanObat;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\CalcPoOutstandingObatalkesView;
use yii\db\Expression;
use app\modules\v1\models\BaseCalRoMinResepFn;

class CalculationRekomendationAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $doi = $request->get('doi');
        $jenis_obat = $request->get('jenis_obat');
        $date = date('Y-m-d', strtotime("-1 day"));
        $recommendation = [];

        $satuan_config = 'satuanbesar_id';
        $config_farmasi = KonfigFarmasi::find()->select(['is_large_unit_pr'])->asArray()->one();

        if(!$config_farmasi['is_large_unit_pr']) {
            $satuan_config = 'satuankecil_id';
        }

        // get base calc data
        $base_calc = $this->getCollection($date, $jenis_obat);
        $collections = $base_calc['collections'];
        $list_obatalkes = $base_calc['list_obatalkes'];
        
        $list_min_resep = $this->getCallectionMinResep($jenis_obat);

        // get obatalkes info
        $data_obat = ObatAlkes::find()->where(['in', 'obatalkes_id', $list_obatalkes])->asArray()->all();
        $list_data_obat = ArrayHelper::index($data_obat, 'obatalkes_id');

        // get all stok obatalkes
        $stok_obat = $this->getAllStok($list_obatalkes);
        $list_stok = $stok_obat['list_stok'];
        $stok_ruangan = $stok_obat['stok_ruangan'];

        // get convertion
        $list_konversi = $this->listSatuanKonv($list_data_obat);

        // get po outstanding
        $list_po_outstanding = $this->listPoOutstanding($list_data_obat);

        // get convertion from master 06-12-2021
        $list_konversi_master = $this->listNilaiKonversiMaster($list_data_obat);

        // get criteria
        $criteria = MovingCriteria::find()->asArray()->all();
        $list_criteria = ArrayHelper::index($criteria, 'criteria');

        foreach($collections as $item) {
            if(!isset($list_konversi[$item['obatalkes_id']])) {
                continue;
            }

            if(!isset($list_konversi_master[$item['obatalkes_id']])) {
                continue;
            }

            $qty_outstanding = 0;
            if(isset($list_po_outstanding[$item['obatalkes_id']])) {
                $qty_outstanding = $list_po_outstanding[$item['obatalkes_id']]['qty_outstanding'];
            }

            // calculation
            $factor = $list_criteria[$item['move_category']]['factor'];
            $ssmin  = $list_criteria[$item['move_category']]['ss_min'];
            $demand = ($doi * $item[strtolower($factor)]) + ($ssmin * $item['avg']);
            $qoh = $list_stok[$item['obatalkes_id']];
            $countUsed = $item['count'];
            $minResep = ArrayHelper::getValue($list_min_resep, $item['obatalkes_id']);

            if( $list_criteria[$item['move_category']]['criteria'] == "SLOW" || $list_criteria[$item['move_category']]['criteria'] == "VERY SLOW" ) {
                $demand = ($countUsed / 2) * $minResep;
                $qty_recommendation = ($demand - $qoh) / $list_konversi[$item['obatalkes_id']]['nilai_konversi'];
            } else {
                $qty_recommendation = ($demand - $qoh) / $list_konversi[$item['obatalkes_id']]['nilai_konversi'];
            }

            $stok_rs = round($qoh / $list_konversi[$item['obatalkes_id']]['nilai_konversi']);
            $kebutuhan = ceil($demand / $list_konversi[$item['obatalkes_id']]['nilai_konversi']);

            $move_category_id = !empty($item['movingcriteria_id']) ? $item['movingcriteria_id'] : $list_criteria[$item['move_category']]['movingcriteria_id'];

            if($qty_recommendation > 0) {
                // casting
                $data['obatalkes_id']             = $item['obatalkes_id'];
                $data['obatalkes_kode']           = $list_data_obat[$item['obatalkes_id']]['obatalkes_kode'];
                $data['obatalkes_nama']           = $list_data_obat[$item['obatalkes_id']]['obatalkes_nama'];
                $data['satuankecil_id']           = $list_konversi[$item['obatalkes_id']]['satuanbesar_id'];
                $data['satuankecil_nama']         = $list_konversi[$item['obatalkes_id']]['satuan_kecil'];
                $data['nilai_konversi']           = $list_konversi[$item['obatalkes_id']]['nilai_konversi'];
                $data['doi']                      = $doi;
                $data['ss_min']                   = ($satuan_config == "satuanbesar_id") ? round((($ssmin * $item['avg']) / ArrayHelper::getValue($list_konversi[$item['obatalkes_id']], 'nilai_konversi', 0)),2) : round(($ssmin * $item['avg']),2);
                $data['stok_rs']                  = $stok_rs;
                $data['stok_saatini']             = $qoh;
                $data['kebutuhan']                = $kebutuhan;
                $data['presentase']               = ($demand == 0) ? 100 : round(($qoh / $demand) * 100, 2);
                $data['status']                   = $this->status($data['presentase']);
                $data['rekomendasi_order']        = ceil($qty_recommendation);
                $data['satuan_rekomendasi_order'] = $list_konversi[$item['obatalkes_id']]['satuan_besar'];
                $data['rekomendasi_qty_po']       = !empty($list_data_obat[$item['obatalkes_id']]['reorder']) ? ceil($qty_recommendation) : 0;
                $data['satuan_rekomendasi_po']    = $list_konversi[$item['obatalkes_id']]['satuan_besar'];
                $data['stok_gudang']              = ($satuan_config == "satuanbesar_id") ? ($stok_ruangan[$item['obatalkes_id']]['stok_gudang'] / $list_konversi[$item['obatalkes_id']]['nilai_konversi']) : $stok_ruangan[$item['obatalkes_id']]['stok_gudang'];
                $data['stok_farmasi']             = ($satuan_config == "satuanbesar_id") ? ($stok_ruangan[$item['obatalkes_id']]['stok_farmasi'] / $list_konversi[$item['obatalkes_id']]['nilai_konversi']) : $stok_ruangan[$item['obatalkes_id']]['stok_farmasi'];
                $data['stok_lain']                = ($satuan_config == "satuanbesar_id") ? ($stok_ruangan[$item['obatalkes_id']]['stok_lain'] / $list_konversi[$item['obatalkes_id']]['nilai_konversi']) : $stok_ruangan[$item['obatalkes_id']]['stok_lain'];
                $data['last_7']                   = ($satuan_config == "satuanbesar_id") ? round(($item['last_7'] / $list_konversi[$item['obatalkes_id']]['nilai_konversi']), 2) : round($item['last_7'], 2);
                $data['last_14']                  = ($satuan_config == "satuanbesar_id") ? round(($item['last_14'] / $list_konversi[$item['obatalkes_id']]['nilai_konversi']), 2) : round($item['last_14'], 2);
                $data['last_30']                  = ($satuan_config == "satuanbesar_id") ? round(($item['last_30'] / $list_konversi[$item['obatalkes_id']]['nilai_konversi']), 2) : round($item['last_30'],2);
                $data['qty_outstanding']          = ($satuan_config == "satuanbesar_id") ? ($qty_outstanding / $list_konversi[$item['obatalkes_id']]['nilai_konversi']) : $qty_outstanding;
                $data['konversi_label']           = $list_konversi_master[$item['obatalkes_id']]['konversi'];
                $data['reorder']                  = $list_data_obat[$item['obatalkes_id']]['reorder'];
                $data['move_category_id']         = $move_category_id;
                $data['move_category']            = $this->getMoveCategory($item['move_category']);
                
                array_push($recommendation, $data);
            }
        }

        return $this->controller->responseJson(200, 'Rekomendasi order berhasil', $recommendation);
    }

    private function getCollection($date, $jenis_obat)
    {
        $get_config = KonfigFarmasi::find()->select(['basecalc_config'])->one()->basecalc_config;

        $model = new BaseCalRoFnConfigurable;
        $collections = $model::getData(
            $jenis_obat,
            ArrayHelper::getValue($get_config, 'pemakaian_ruangan', "false"),
            ArrayHelper::getValue($get_config, 'bmhp', "false"),
            ArrayHelper::getValue($get_config, 'mutasi', "false")
        );

        $list_obatalkes = ArrayHelper::getColumn($collections, 'obatalkes_id');

        return [
            'collections' => $collections,
            'list_obatalkes' => $list_obatalkes
        ];
    }

    private function getAllStok($list_obatalkes)
    {
        $connection = Yii::$app->db;

        $all_stok = KetersediaanObat::find()
                        ->select(['obatalkes_id', 'sum(qty_stok) as qty_tersedia'])
                        ->where(['in', 'obatalkes_id', $list_obatalkes])
                        ->groupBy('obatalkes_id')
                        ->asArray()
                        ->all();

        $list_stok = ArrayHelper::map($all_stok, 'obatalkes_id', 'qty_tersedia');
        $stok_ruangan = [];
        
        if(!empty($list_obatalkes)) {
            $getLookupTransaksi = LookupTransaksi::find()
                                    ->select(['kode_transaksi', 'kode_id'])
                                    ->where(['in', 'kode_transaksi', ['farmasi_utama', 'gudang_farmasi']])
                                    ->asArray()
                                    ->all();

            $arrLookupTransaksi = ArrayHelper::index($getLookupTransaksi, 'kode_transaksi');
            $farmasi_utama = $arrLookupTransaksi['farmasi_utama']['kode_id'];
            $gudang_farmasi = $arrLookupTransaksi['gudang_farmasi']['kode_id'];
            
            $getStokRuangan = KetersediaanObat::find()
                ->select(['obatalkes_id',
                    new Expression("TO_CHAR(SUM(CASE WHEN ruangan_id = {$farmasi_utama} THEN qty_stok ELSE 0 END), 'FM999999999990.999999')::NUMERIC AS stok_farmasi"),
                    new Expression("TO_CHAR(SUM(CASE WHEN ruangan_id = {$gudang_farmasi} THEN qty_stok ELSE 0 END), 'FM999999999990.999999')::NUMERIC AS stok_gudang"),
                    new Expression("TO_CHAR(SUM(CASE WHEN ruangan_id not in ({$farmasi_utama}, {$gudang_farmasi}) THEN qty_stok ELSE 0 END), 'FM999999999990.999999')::NUMERIC AS stok_lain")
                ])
                ->where(['in', 'obatalkes_id', $list_obatalkes])
                ->groupBy('obatalkes_id')
                ->asArray()
                ->all();
            $stok_ruangan = ArrayHelper::index($getStokRuangan, 'obatalkes_id');
        }
        

        return [
            'all_stok' => $all_stok,
            'list_stok' => $list_stok,
            'stok_ruangan' => $stok_ruangan
        ];
    }

    private function listSatuanKonv($list_data_obat)
    {
        $satuan_digunakan = 'satuanbesar_id';
        $config = KonfigFarmasi::find()->select(['is_large_unit_pr'])->asArray()->one();

        if(!$config['is_large_unit_pr']) {
            $satuan_digunakan = 'satuankecil_id';
        }

        $konversi = SatuanKonversiView::find()
                        ->where(['in', 'obatalkes_id', array_keys($list_data_obat)])
                        ->asArray()
                        ->all();

        $list_konversi = [];
        foreach($konversi as $item) {
            if(
                in_array($item['obatalkes_id'], array_keys($list_data_obat)) && 
                $item['satuanbesar_id'] == $list_data_obat[$item['obatalkes_id']][$satuan_digunakan]
            ) {
                $list_konversi[] = $item;
            }
        }

        $list_konversi = ArrayHelper::index($list_konversi, 'obatalkes_id');

        return $list_konversi;
    }

    private function listNilaiKonversiMaster($list_data_obat) {
        $inObat = array_keys($list_data_obat);
        $list_obat_master = [];
        if(!empty($list_data_obat)) {
            $query=new \yii\db\Query();
            $result = $query->select(["a.obatalkes_id", "b.satuanunit_id", "b.satuanunit_nama", "c.satuanunit_id", "c.satuanunit_nama", "a.kemasan_besar", "concat('1 ', b.satuanunit_nama, ' = ', a.kemasan_besar, ' ', c.satuanunit_nama) AS konversi"])
                        ->from('obatalkes_m a')
                        ->join('LEFT JOIN', 'satuanunit_m b', 'b.satuanunit_id = a.satuanbesar_id')
                        ->join('LEFT JOIN', 'satuanunit_m c', 'c.satuanunit_id = a.satuankecil_id')
                        ->where(['in', 'a.obatalkes_id', array_keys($list_data_obat)])->all();
            $list_obat_master = ArrayHelper::index($result, 'obatalkes_id');
        }

        return $list_obat_master;
    }

    private function listPoOutstanding($list_data_obat)
    {
        $list_po_outstanding = [];
        $connection = Yii::$app->db;

        if(!empty($list_data_obat)) {
            $arr_obatalkes_id = array_keys($list_data_obat);
            $data = CalcPoOutstandingObatalkesView::find()->select(['obatalkes_id', 'qty_outstanding'])->where(['in', 'obatalkes_id', $arr_obatalkes_id])->asArray()->all();
            $list_po_outstanding = ArrayHelper::index($data, 'obatalkes_id');
        }

        return $list_po_outstanding;
    }

    private function status($value){
        $status = '';
        switch ($value) {
            case 0 :
                $status = "Tidak Terpenuhi";
                break;
            case $value > 0 && $value < 100:
                $status = "Sebagian Terpenuhi";
                break;
            case $value >= 100:
                $status = "Terpenuhi";
                break;
            default:
                $status = "Tidak Terpenuhi";
                break;
        }
        return $status;
    }

    private function getMoveCategory($strCategory) {
        $strName = "";
        if (!empty($strCategory)) {
            $arrStr = explode(' ', $strCategory);
            foreach ($arrStr as $kata) {
                $strName .= substr($kata, 0, 1);
            }
        }
        return $strName;
    }

    private function getCallectionMinResep($jenis_obat)
    {
        $model = new BaseCalRoMinResepFn;
        $query = $model::find()
            ->where(['in', 'jenisobatalkes_id', $jenis_obat])
            ->asArray()->all();

        return ArrayHelper::map($query, 'obatalkes_id', 'min_resep');
    }
}
