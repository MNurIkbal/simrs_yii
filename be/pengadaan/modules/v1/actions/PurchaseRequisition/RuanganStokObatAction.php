<?php

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\KetersediaanObat;
use yii\db\Expression;

class RuanganStokObatAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $item_id = $request->get('oid');

        $lookupTransaksi = LookupTransaksi::find()->select(['kode_transaksi', 'kode_id'])
        ->where(['in', 'kode_transaksi', ['farmasi_utama', 'gudang_farmasi']])
        ->asArray()->all();

        $arrLookupTransaksi = ArrayHelper::index($lookupTransaksi, 'kode_transaksi');
        $farmasi_utama = $arrLookupTransaksi['farmasi_utama']['kode_id'];
        $gudang_farmasi = $arrLookupTransaksi['gudang_farmasi']['kode_id'];

        $getStokRuangan = KetersediaanObat::find()->select([
            'obatalkes_id',
            'obatalkes_nama',
            new Expression("array_to_json(array_remove(array_agg(CASE WHEN ruangan_id = {$farmasi_utama} THEN ruangan_id end), NULL)) AS farmasi_utama"),
            new Expression("array_to_json(array_remove(array_agg(CASE WHEN ruangan_id = {$gudang_farmasi} THEN ruangan_id end), NULL)) AS gudang_farmasi"),
            new Expression("array_to_json(array_remove(array_agg(CASE WHEN ruangan_id not in ({$farmasi_utama}, {$gudang_farmasi}) THEN ruangan_id END), NULL)) as ruangan_lain")
        ])->where(['IN', 'obatalkes_id', $item_id])
        ->groupBy(['obatalkes_id', 'obatalkes_nama'])
        ->asArray()->one();

        $data = [
            'obatalkes_id' => ArrayHelper::getValue($getStokRuangan, 'obatalkes_id'),
            'obatalkes_nama' => ArrayHelper::getValue($getStokRuangan, 'obatalkes_nama'),
            'farmasi_utama' => json_decode(ArrayHelper::getValue($getStokRuangan, 'farmasi_utama')),
            'gudang_farmasi' => json_decode(ArrayHelper::getValue($getStokRuangan, 'gudang_farmasi')),
            'ruangan_lain' => json_decode(ArrayHelper::getValue($getStokRuangan, 'ruangan_lain')),
        ];
        return $this->controller->responseJson(200, 'success', $data);
    }
}
