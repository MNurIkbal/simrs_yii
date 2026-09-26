<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\modules\pengadaan\models\InfoPoForm;
use Doco\pengadaan\components\access\PurchaseOrderValidasiAccess;
use yii\helpers\ArrayHelper;

class DetailAction extends Action {
    public function run($id, $type_po) {
        $model = new InfoPoForm;
        $title = "Detail Purchase Order (PO)";
        $status_batal = false;
        $nomor_pr = '-';

        try {
            $response = Yii::$app->docoRest->pengadaan->get('info-purchase-order/get-detail', [
                'query' => [
                    'id' => DocoHelpers::decrypt($id),
                    'type_po' => DocoHelpers::decrypt($type_po)
                ]
            ]);
            $response = json_decode($response->getBody(),true);

            $response['response']['header']['tgl_perubahan'] = date('d-M-Y H:i:s', strtotime($response['response']['header']['tgl_perubahan']));

            $isValidasi = $response['response']['header']['is_validasi'];
            $instalasi_id = $response['response']['header']['instalasi_id'];
            $ruangan_id = $response['response']['header']['ruangan_id'];
            $model->attributes = $response['response']['header'];
            $payterm = $response['response']['payterm'];
            $pajak = $response['response']['pajak'];
            $detail = $response['response']['detail'];
            $supplier = $response['response']['supplier'];
            $master_harga = $response['response']['master_harga'];
            $konversi = $response['response']['konversi'];
            $hasilKonversi = $response['response']['hasil_konversi'];
            $labelKonversi = $response['response']['label_konversi'];
            $detailKonversi = $response['response']['detail_konversi'];
            $nilaiDefault = $response['response']['nilai_default'];
            $order_name = $response['response']['header'];

            if(!$model->is_validasi) {
                $disableValidasi = false;
            } else {
                $disableValidasi = true;
            }

            if($model->status_penerimaan == DocoConstants::STATUS_PO_BELUM_SEMUA_DITERIMA ||
                $model->status_penerimaan == DocoConstants::STATUS_PO_EXPIRED) {
                $disableSimpan = true;
            } else {
                $disableSimpan = false;
            }

            if ($model->status_penerimaan == DocoConstants::STATUS_BATAL_PO) {
                $status_batal = true;
            }

            $list_nomor = [];

            foreach ($detail as $key => $value) {
                if(isset($value['nomor']) && !empty($value['nomor'])) {
                    $list_nomor[] = $value['nomor'];
                }
            }

            if(count($list_nomor) > 0) {
                $nomor_pr = array_unique($list_nomor);
                $nomor_pr = implode(", ", $nomor_pr);
            }
            
            $btnValidasiAccess = (new PurchaseOrderValidasiAccess)->check($model);
            $optionAttributes = [];
        } catch (RequestException $e) {
            $detail = $payterm = $pajak = $konversi = $hasilKonversi = $master_harga = $supplier = [];
            $nilaiDefault = $detailKonversi = [];
        }
        return $this->controller->render('detail', get_defined_vars());
    }
}
