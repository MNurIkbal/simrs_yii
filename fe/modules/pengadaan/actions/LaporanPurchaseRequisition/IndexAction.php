<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanPurchaseRequisition;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class IndexAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $module = $this->controller->_module;
        $response = Yii::$app->docoRest->pengadaan->get('purchase-requisition/get-status-po');
        $body = json_decode($response->getBody(), true);
        foreach ($body['response'] as $key => $value) {
            $status_po[$value['lookup_name']] = $value['lookup_name'];
        }
        return $this->controller->render('index', get_defined_vars());
    }
}
