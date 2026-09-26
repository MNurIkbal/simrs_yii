<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use yii\base\View;
use app\components\DocoHelpers;
use app\components\DocoConstants;

class SplitPOSaveAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'method' => 'post',
            'url' => 'info-purchase-order/save-split-po',
            'payload' => [
                'form_params' => [
                    'item_id' => $request->post('item_id'),
                    'po_detail' => $request->post('po_detail'),
                    'pr_nomor' => $request->post('pr_nomor'),
                    'detail' => $request->post('detail'),
                    'type_po' => $request->post('type_po')
                ]
            ],
            'returnResponse' => true
        ]);
    }
}
