<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use app\components\ApotekComponent;
use app\modules\v1\models\InfoStokOpnameDetailView;
use SirsCore\models\StokObatAlkes;

class StokOpnameDetail {
    protected $_stokOpnameDetail;

    public function __get($name) {
        if (array_key_exists($name, $this->_stokOpnameDetail)) {
            return $this->_stokOpnameDetail[$name];
        }

        return null;
    }

    public function loadViewById($stokopname_id) {
        $detail = InfoStokOpnameDetailView::find()->where(['stokopname_id' => $stokopname_id])->orderBy(['obatalkes_nama'=>SORT_ASC, 'tglkadaluarsa'=>SORT_ASC])->asArray()->all();
        if(empty($detail)) throw new \Exception("Error Processing Request", 1);

        $this->_stokOpnameDetail = $detail;
        return $this;
    }

    public function calcWeightedAvgTotal($configBasePriceVal = null)
    {
        $total_wa_fisik = $total_wa_sistem = 0;
        foreach ($this->_stokOpnameDetail as $value) {
            if($configBasePriceVal != null && $configBasePriceVal == 1) {
                $total_wa_fisik += floatval($value['base_price']) * floatval($value['volume_fisik']);
                $total_wa_sistem += floatval($value['base_price']) * floatval($value['volume_sistem']);
            } else {
                $total_wa_fisik += floatval($value['weighted_avg']) * floatval($value['volume_fisik']);
                $total_wa_sistem += floatval($value['weighted_avg']) * floatval($value['volume_sistem']);
            }
        }

        return [
            'total_wa_fisik' => $total_wa_fisik,
            'total_wa_sistem' => $total_wa_sistem
        ];
    }

    public function updateWeightedAverage($configVerifikasi = true, $ruanganId) {
        $arr_weighted_avg = $arr_keys = $arr_stok_akhir = $arr_selisih_akhir = [];
        $isBackdate = $configVerifikasi['config'] ? false : true;
        $this->_stokOpnameDetail = ArrayHelper::index($this->_stokOpnameDetail, 'obatalkes_id');

        if($isBackdate) {
            $lastStok = StokObatAlkes::find()
                ->select([
                    'ruangan_id', 'obatalkes_id', 
                    'sum(qtystok_in) as stok_in', 'sum(qtystok_out) as stok_out', 'sum(qtystok_in - qtystok_out) as total_stok'
                ])
                ->where(['is_deleted' => false])
                ->andWhere(['ruangan_id' => $ruanganId])
                ->andWhere(['IN', 'obatalkes_id', array_keys($this->_stokOpnameDetail)])
                ->andWhere(['<=', 'COALESCE(tglstok_in, tglstok_out)', date('Y-m-d H:i:s', strtotime($configVerifikasi['tgl_implementasi']))])
                ->groupBy(['ruangan_id', 'obatalkes_id'])
                ->asArray()->all();
            $lastStok = ArrayHelper::index($lastStok, 'obatalkes_id');
        }

        $total_stok = 0;
        foreach($this->_stokOpnameDetail as $key => $value) {
            $arr_keys[] = $value['stokopnamedetail_id'];
            $arr_weighted_avg[] = floatval($value['weighted_avg']);
            if($isBackdate) {
                // jika backdate, stok akhir diambil dari stok terakhir di tanggal ditariknya formulir
                $total_stok = isset($lastStok[$key]['total_stok']) ? floatval($lastStok[$key]['total_stok']) : 0;
                $arr_stok_akhir[] = $total_stok;
                $arr_selisih_akhir[] = floatval($value['volume_fisik']) - $total_stok;
            } else {
                $arr_stok_akhir[] = floatval($value['stok_sistem']);
                $arr_selisih_akhir[] = floatval($value['stok_selisih']);
            }
        }

        if(count($arr_weighted_avg) > 0) {
            $update_data = [
                'weighted_avg' => $arr_weighted_avg,
                'stok_akhir' => $arr_stok_akhir,
                'selisih_akhir' => $arr_selisih_akhir
            ];

            $updateCondition = [
                'stokopnamedetail_id' => $arr_keys
            ];

            $updateWeightedAvg = ApotekComponent::updateMultiple('stokopnamedetail_t', $update_data, $updateCondition);
            if(!$updateWeightedAvg) {
                throw new \Exception("Gagal update nilai weighted average", 1);
            }
        }
    }
}
