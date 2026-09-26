<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\InfoPoDetailView;
use app\modules\v1\models\SatuanKonversi;
use app\modules\v1\models\SatuanKonversiBarang;

class SplitPOAction extends Action {
    public function run($id, $type_po, $item_key) {
    	$connection = Yii::$app->db;
        $item_key = explode('-', $item_key);

        $nomor_pr = $item_key[0];
        $obat_barang_id = $item_key[1];

        $detail = InfoPoDetailView::find()->where([
            'transaksi_id' => $id,
            'jenis' => strtolower($type_po),
            'nomor' => $nomor_pr,
            'obat_barang_id' => $obat_barang_id
        ])->one();

        $query = $connection->createCommand("
            SELECT
                supplier_m.supplier_id,
                supplier_m.supplier_nama,
                kontraksupplier.harga AS supplier_harga,
                kontraksupplier.satuankonv1_id AS supplier_satuan
            FROM supplier_m
            LEFT JOIN (
                SELECT 
                    supplier_id, 
                    harga,
                    satuankonv1_id
                FROM kontraksupplier_v 
                WHERE obatalkes_id = '".$obat_barang_id."'
            ) AS kontraksupplier ON supplier_m.supplier_id = kontraksupplier.supplier_id
            WHERE supplier_m.is_active IS TRUE
            ORDER BY kontraksupplier.harga, supplier_m.supplier_id 
        ")->queryAll();

        $list_supplier = [];
        $list_konversi = [];

        foreach ($query as $key => $value) {
        	$list_supplier[$value['supplier_id']] = $value;
        }


        if($type_po == DocoConstants::JENIS_OBAT) {
            $data_konversi = SatuanKonversi::find()->where([
                'obatalkes_id' => $obat_barang_id
            ])
            ->select(['satuanbesar_id', 'nilai_konversi'])
            ->asArray()
            ->all();
        } else {
            $data_konversi = SatuanKonversiBarang::find()->where([
                'barang_id' => $obat_barang_id
            ])
            ->select(['satuanbesar_id', 'nilai_konversi'])
            ->asArray()
            ->all();
        }

        foreach ($data_konversi as $key => $value) {
            $list_konversi[$value['satuanbesar_id']] = $value;
        }

        return [
            "detail" => $detail, 
            "list_supplier" => $list_supplier,
            "list_konversi" => $list_konversi
        ];
    }
}
