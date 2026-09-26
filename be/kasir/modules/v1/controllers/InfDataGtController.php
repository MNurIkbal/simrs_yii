<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\GtPenjualanObatView;
use app\modules\v1\models\GtPembelianObatView;
use app\modules\v1\models\GtPembayaranPelayananView;

class InfDataGtController extends DocoActiveController
{

    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionPenjualanResep()
    {
        $model = new GtPenjualanObatView();
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter(
            $model,
            DocoRestActiveFilter::filterMutation(
                $model,
                $query,
                [
                    'tglpenjualan' => 'date',
                    'tanggal_lahir' => 'date',
                    'data_resep.jenis_racikan' => 'json_array_like',
                    'data_resep.obatalkes_id' => 'json_array_like',
                    'data_resep.obatalkes_kode' => 'json_array_like',
                    'data_resep.obatalkes_nama' => 'json_array_like',
                    'data_resep.hargasatuan' => 'json_array_like',
                    'data_resep.harganetto' => 'json_array_like',
                    'data_resep.qty' => 'json_array_like',
                    'data_resep.sub_total' => 'json_array_like',
                ]
            )
        );
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionPembayaranLayanan()
    {
        $model = new GtPembayaranPelayananView();
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter(
            $model,
            DocoRestActiveFilter::filterMutation(
                $model,
                $query,
                [
                    'tgl_pendaftaran' => 'date',
                    'data_transaksi.pendaftaran_id' => 'json_array_like',
                    'data_transaksi.tgl_layanan' => 'json_array_date',
                    'data_transaksi.instalasi_nama' => 'json_array_like',
                    'data_transaksi.ruangan_nama' => 'json_array_like',
                    'data_transaksi.jenis_transaksi' => 'json_array_like',
                    'data_transaksi.tindakan_obat_id' => 'json_array_like',
                    'data_transaksi.tindakan_obat_nama' => 'json_array_like',
                    'data_transaksi.qty' => 'json_array_like',
                    'data_transaksi.harga_satuan' => 'json_array_like',
                    'data_transaksi.harga_cyto' => 'json_array_like',
                    'data_transaksi.sub_total' => 'json_array_like',
                    'data_pembayaran.pendaftaran_id' => 'json_array_like',
                    'data_pembayaran.total_tagihan' => 'json_array_like',
                    'data_pembayaran.total_penjamin' => 'json_array_like',
                    'data_pembayaran.total_nontunai' => 'json_array_like',
                    'data_pembayaran.total_tunai' => 'json_array_like',
                    'data_pembayaran.jenis_pembayaran' => 'json_array_like',
                ]
            )
        );
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionPurchaseOrder()
    {
        $model = new GtPembelianObatView();
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter(
            $model,
            DocoRestActiveFilter::filterMutation(
                $model,
                $query,
                [
                    'tanggal_pembelian' => 'date',
                    'data_detail_pembelian.validasipoobat_id' => 'json_array_like',
                    'data_detail_pembelian.no_batch' => 'json_array_like',
                    'data_detail_pembelian.obatalkes_id' => 'json_array_like',
                    'data_detail_pembelian.jenis_obat' => 'json_array_like',
                    'data_detail_pembelian.nama_obatalkes' => 'json_array_like',
                    'data_detail_pembelian.satuan' => 'json_array_like',
                    'data_detail_pembelian.harga_satuan' => 'json_array_like',
                    'data_detail_pembelian.quantity' => 'json_array_like',
                    'data_detail_pembelian.quantity_konversi' => 'json_array_like',
                    'data_detail_pembelian.discount' => 'json_array_like',
                    'data_detail_pembelian.discount_rp' => 'json_array_like',
                    'data_detail_pembelian.sub_total' => 'json_array_like',
                    'data_detail_pembelian.tgl_kadaluarsa' => 'json_array_date',
                ]
            )
        );
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

}
