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
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $title = Yii::t('fe', 'Informasi Purchase Order (PO)');
        $module = '/pengadaan/info-purchase-order/';
        try {
            $response = Yii::$app->docoRest->pengadaan->get('info-purchase-order/get-attributes', [
                'form_params' => []
            ]);
            $response = json_decode($response->getBody(),true);
            $status_po = isset($response['response']['status_po']) ? $response['response']['status_po'] : [];
            $payterm = isset($response['response']['payterm']) ? $response['response']['payterm'] : [];
        } catch (RequestException $e) {
            $status_po = $payterm = [];
        }
        return $this->controller->render('index', get_defined_vars());
    }
}