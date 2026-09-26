<?php

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\InfoStokBarangView;
use yii\db\Expression;

class GetStockBarangAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $barangId = $request->get('barang_id', null);
        $ruanganId = $request->get('ruangan_id', 'null');

        $lookupTransaksi = LookupTransaksi::find()
            ->select(['kode_transaksi', 'kode_id'])
            ->where(['in', 
                'kode_transaksi', [
                    DocoConstants::LT_FARMASI_UTAMA, 
                    DocoConstants::LT_GUDANG_FARMASI
                ]
            ])
            ->asArray()
            ->all();
        $lookupTransaksiIndex = ArrayHelper::index($lookupTransaksi, 'kode_transaksi');
        $farmasiUtama = isset($lookupTransaksiIndex['farmasi_utama']) ? ArrayHelper::getValue($lookupTransaksiIndex['farmasi_utama'], 'kode_id') : 'NULL';
        $gudangFarmasi = isset($lookupTransaksiIndex['gudang_farmasi']) ? ArrayHelper::getValue($lookupTransaksiIndex['gudang_farmasi'], 'kode_id') : 'NULL';
        
        $stokBarang = InfoStokBarangView::find()->select([
            new Expression("SUM(CASE WHEN ruangan_id = {$ruanganId} THEN qty_stok ELSE 0 END) AS qty_tersedia"),
            new Expression("SUM(CASE WHEN ruangan_id = {$ruanganId} THEN qty_stok ELSE 0 END) AS stok_gudang"),
            new Expression("SUM(CASE WHEN ruangan_id != {$ruanganId} THEN qty_stok ELSE 0 END) AS stok_lain")
        ])->where(['barang_id' => $barangId])
        ->asArray()->one();

        return $this->controller->responseJson(200, 'success', $stokBarang);
    }
}
