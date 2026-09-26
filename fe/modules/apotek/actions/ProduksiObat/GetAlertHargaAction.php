<?php

/**
 * @author : Asri Nurul M
 * Powered by Sirs
 */

namespace Doco\apotek\actions\ProduksiObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;

class GetAlertHargaAction extends Action {
    public function run($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $query = [
            "id" => $id
        ];

        $data = [];
        try {
            $compareHarga = $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
                'url' => 'inf-produksi-obat/get-alert-Harga',
                'payload' => [
                    'query' => $query
                ],
                'returnResponse' => true
            ]);
            $harga = ArrayHelper::getValue($compareHarga, 'data', []);
            $no = 1;
            $_count = count($harga);
            if($_count > 0) {
                foreach ($harga as $row => $value) {
                    $value["rowNum"] = $no;
                    $value["obatalkes_nama"] = $value["obatalkes_nama"];
                    $value["harganetto_ygdipakai"] = $value["harganetto_awal"];
                    $value["harga_sugesstion"] = isset($value['qty_produksi']) && $value['qty_produksi'] != 0 ? $value["harganetto_baru"] / $value['qty_produksi'] : 0;
                    $value["harga_transaksi"] = $value["harganetto_baru"];
                    $data[$row] = $value;
                    $no++;
                }
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $_count;
            $result['recordsFiltered'] = $_count;
        } catch (\Exception $e) {
            $result['data'] = [];
        }
        return DocoHelpers::response($result);
    }
}
