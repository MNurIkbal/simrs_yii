<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseRequisition;

use Yii;
use yii\base\Action;

class ApprovingProcessAction extends Action {
    public function run() {
        $id = Yii::$app->request->post('id', null);
        $type = Yii::$app->request->post('type', "OBAT");
        $qtyfinal_list = Yii::$app->request->post('qtyfinal_list', null);

        if(is_null($id) || empty($qtyfinal_list))
            throw new \Exception("Tidak dapat melakukan proses, data kosong.", 1);

        return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'purchase-requisition/approving-process',
            'method' => 'POST',
            'payload' => [
                'json' => [
                    'id' => $id,
                    'type' => $type,
                    'list' => $qtyfinal_list
                ]
            ],
            'returnResponse' => true
        ]);
    }
}
