<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPurchaseOrder;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\helpers\HandlingValueHelper as SetValue;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use yii\base\Controller;

class GetDataLaporanAction extends Action {
    
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $request = Yii::$app->request;
        if (isset($request->get()['is_prcyto'])) {
            $payload['advanced-filter']['po_cito'] = $request->get('is_prcyto');
        }
        if (isset($request->get()['is_admin'])) {
            $payload['advanced-filter']['po_admin'] = $request->get('is_admin');
        }
        if (isset($request->get()['is_consignment'])) {
            $payload['advanced-filter']['po_consigment'] = $request->get('is_consignment');
        }
        $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'info-purchase-order/laporan-analisa-po',
            'payload' => [
                'query' => $payload,
            ]
        ]);
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }
}
