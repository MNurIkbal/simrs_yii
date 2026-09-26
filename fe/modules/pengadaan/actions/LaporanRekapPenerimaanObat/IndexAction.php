<?php

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanRekapPenerimaanObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $module = $this->controller->_module;

        // var supplier 
        $array_supplier = [];
        $list_supplier = [];

        // var payment 
        $array_payment = [];
        $list_payment = [];

        try {
            $response       = Yii::$app->docoRest->pengadaan->get('lap-rekap-penerimaan-obat/get-data-filter');
            $response       = json_decode($response->getBody(), true);
            $array_supplier = $response['response']['supplier'];
            $array_payment = $response['response']['payterm'];

            // supplier 
            if(is_array($array_supplier)){
                $list_supplier[] = ['id' => '0', 'text' => 'Semua'];

                foreach($array_supplier as $supplier){
                    $list_supplier[] = ['id' => $supplier['supplier_id'], 'text' => $supplier['supplier_nama']];
                }
            }

            // payment 
            if (is_array($array_payment)) {
                $list_payment[] = ['id' => '0', 'text' => 'Semua'];

                foreach ($array_payment as $payment) {
                    $list_payment[] = ['id' => $payment['payterm_id'], 'text' => $payment['payterm_nama']];
                }
            }

                        
        } catch (RequestException $e) {

        } 

        return $this->controller->render('index', get_defined_vars());
    }
}
