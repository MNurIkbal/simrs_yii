<?php

/**
 * @author : Ardi Pratama (ardi@docotel.co.id)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\purchasing;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\InformasiForm;

class LaporanAllPoMhbg extends \app\components\DocoBaseProcessExtension
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

        return $controller->render('@app/extensions/purchasing/views/laporan-all-po-mhbg', get_defined_vars());
    }
}
