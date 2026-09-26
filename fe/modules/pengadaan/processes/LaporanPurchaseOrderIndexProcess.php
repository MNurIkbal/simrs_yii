<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * A product of PT. CRN
 * Powered by Sirs
 */

namespace app\modules\pengadaan\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;

class LaporanPurchaseOrderIndexProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $title = $controller->_title;
        $module = $controller->_module;
        $response = Yii::$app->docoRest->pengadaan->get('purchase-requisition/get-status-po');
        $body = json_decode($response->getBody(), true);
        foreach ($body['response'] as $key => $value) {
            $status_po[$value['lookup_name']] = $value['lookup_name'];
        }

        $get_list_supplier = Yii::$app->docoRest->pengadaan->get('allow/get-list-supplier', [
            'form_params' => []
        ]);
        $get_list_supplier = json_decode($get_list_supplier->getBody(), true);
        $list_supplier = isset($get_list_supplier['response']) ? $get_list_supplier['response'] : [];

        return $controller->render($controller->action->defaultView, get_defined_vars());
    }

}
