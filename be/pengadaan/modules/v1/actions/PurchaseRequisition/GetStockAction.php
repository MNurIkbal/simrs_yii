<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * Last Modified by:   Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Last Modified time: 2021-02-24 14:42:00
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\LookupTransaksi;

class GetStockAction extends Action
{
    public function run()
    {
        try {
            $connection = Yii::$app->db;
            $request = Yii::$app->request;
            $item_id = $request->get('oid', false);
            $ruangan_id = $request->get('rid', false);
            $type = $request->get('type', false);

            $getLookupTransaksi = LookupTransaksi::find()
                                    ->select(['kode_transaksi', 'kode_id'])
                                    ->where(['in', 'kode_transaksi', ['farmasi_utama', 'gudang_farmasi']])
                                    ->asArray()
                                    ->all();

            if(empty($getLookupTransaksi)) {
                throw new \Exception("Data ruangan farmasi dan gudang farmasi tidak terdaftar.", 1);
            }

            $arrLookupTransaksi = ArrayHelper::index($getLookupTransaksi, 'kode_transaksi');
            $farmasi_utama = $arrLookupTransaksi['farmasi_utama']['kode_id'];
            $gudang_farmasi = $arrLookupTransaksi['gudang_farmasi']['kode_id'];

            if($type == "barang") {
                $query = "
                    SELECT
                        qty_sisa AS qty_tersedia
                    FROM stokbarang_r sr
                    WHERE barang_id = {$item_id} and ruangan_id = {$ruangan_id}
                ";
            } else {
                $query = "
                    SELECT
                        SUM(CASE WHEN sr.ruangan_id = {$ruangan_id} THEN qty_stok ELSE 0 END) AS qty_tersedia,
                        SUM(CASE WHEN sr.ruangan_id = {$farmasi_utama} THEN qty_stok ELSE 0 END) AS stok_farmasi,
                        SUM(CASE WHEN sr.ruangan_id = {$gudang_farmasi} THEN qty_stok ELSE 0 END) AS stok_gudang,
                        SUM(CASE WHEN sr.ruangan_id not in ({$farmasi_utama}, {$gudang_farmasi}) THEN qty_stok ELSE 0 END) AS stok_lain
                    FROM ketersediaanobat_v sr
                    WHERE obatalkes_id = {$item_id}
                ";
            }

            $data = $connection->createCommand($query)->queryOne();

            if (!$data)
                throw new \Exception("Data tidak ada", 1);

            return $data;
        } catch (\Exception $e) {
            $this->logError($e);
            return [
                'qty_tersedia' => 0
            ];
        }
    }
}
