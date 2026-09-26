<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use app\modules\v1\models\HargaNettoObatView;
use Doco\components\BatchUpdate;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\businessLogic\UpdateHargaNetto;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\PenerimaanSupplier;
use Doco\components\DocoConstants;
use Doco\Repositories\KonfigRepositories;
use yii\helpers\ArrayHelper;

class UpdateBasePriceAction extends Action {
    protected function checkHarga($transaksi_id, $tipe) {
        $model = new HargaNettoObatView;
        $query = $model::find()->where([
            'transaksi_id' => $transaksi_id,
            'tipe' => $tipe
        ])->andWhere('harga_netto_sekarang <> harga_disarankan')->asArray()->all();
        
        return ArrayHelper::index($query, 'obatalkes_id');
    }

    public function run($transaksi_id, $tipe, $detail) {
        return (new BatchUpdate(ObatAlkes::tableName(), function($query) use ($transaksi_id, $detail, $tipe) {
            $hargaNetto = $this->checkHarga($transaksi_id, $tipe);
            $detail = ArrayHelper::index(json_decode($detail, true), 'obatalkes_id');
            $label = [];
            $isHargaNettoTerkecil = false;

            switch($tipe) {
                case 'ADJ_MASUK':
                    $keterangan = UpdateHargaNetto::KET_ADJUSTMENOBAT;
                    $label['harganetto'] = 'harganetto';
                    $label['qty'] = 'qtystok_in';
                    $isHargaNettoTerkecil = true;
                    break;
                
                case 'PO':
                    $keterangan = UpdateHargaNetto::KET_PENERIMAANSUPP;
                    $label['harganetto'] = 'harga';
                    $label['qty'] = 'qty_diterima';
                    $isHargaNettoTerkecil = true;
                    break;

                case 'PEN_SUPP':
                    $keterangan = UpdateHargaNetto::KET_PENERIMAANMANUAL;
                    $label['harganetto'] = 'harganetto';
                    $label['qty'] = 'qty_kecil';
                    $isHargaNettoTerkecil = true;
                    break;

                default:
                    $isHargaNettoTerkecil = false;
                    break;
            }

            $listKey = [];
            $konfig = KonfigRepositories::getKonfigFarmasi();
            $hna = 0;

            foreach ($detail as $key => $value) {
                if(!in_array($key, ArrayHelper::getColumn($hargaNetto, 'obatalkes_id'))) {   
                    $hna = $isHargaNettoTerkecil ? $value[$label['harganetto']] : (float) $value[$label['harganetto']] / $value[$label['qty']];
                    
                    if($tipe != 'ADJ_MASUK') {
                        $discount = 0;
                        if($konfig['use_discount']) {
                            $discount = $value['jmldiscount'];
                        }
                        
                        $ppn = 0;
                        if($konfig['use_ppn']) {
                            $ppn = $value['jmlppn'];
                        }

                        $hna = $hna - $discount + $ppn;
                    }

                    $query->set([
                        'ket_ubah_harga' => $keterangan,
                        'harganetto' => $hna,
                        'last_modified_by' => Yii::$app->user->identity->id,
                        'last_modified_date' => date('Y-m-d H:i:s'),
                        'modified_count' => "obatalkes_m.modified_count + 1",
                    ], "obatalkes_id = {$key}", $key);
                    array_push($listKey, $key);

                    if(count($listKey) > 0){
                        $id = implode(", ", $listKey);
                        $query->where('obatalkes_id in('.$id.')');
                    }
                }
            }
        }))->execute();
    }
}
