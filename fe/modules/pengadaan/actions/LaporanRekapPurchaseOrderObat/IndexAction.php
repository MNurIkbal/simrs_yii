<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanRekapPurchaseOrderObat;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
		$module = $this->controller->_module;

        $suppliers = [];

        try {
            $response       = Yii::$app->docoRest->pengadaan->get('lap-rekap-purchase-order-obat/get-supplier');
            $response       = json_decode($response->getBody(), true);

            $suppliers[] = ['id' => '0', 'text' => 'Semua'];

            foreach ($response['response'] as $key => $value) {
                $suppliers[] = ['id' => $key, 'text' => $value];
            }
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }

		return $this->controller->render('index', get_defined_vars());
    }
}
