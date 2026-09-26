<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.co.id)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanRekapPurchaseOrderBarang;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $module = $this->controller->_module;

        $array_supplier = [];
        $list_supplier = [];

        try {
            $response       = Yii::$app->docoRest->pengadaan->get('lap-rekap-purchase-order-barang/get-data-filter');
            $response       = json_decode($response->getBody(), true);
            $array_supplier = $response['response']['supplier'];

            if(is_array($array_supplier)){
                $list_supplier[] = ['id' => '0', 'text' => 'Semua'];

                foreach($array_supplier as $supplier){
                    $list_supplier[] = ['id' => $supplier['supplier_id'], 'text' => $supplier['supplier_nama']];
                }
            }

                        
        } catch (RequestException $e) {

        }
        
        return $this->controller->render('index', get_defined_vars());
    }
}
