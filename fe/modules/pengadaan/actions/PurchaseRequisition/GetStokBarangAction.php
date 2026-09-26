<?php

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\web\Response;

class GetStokBarangAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $barangId = $request->get('barang_id', null);
        $ruanganId = Yii::$app->docoVars->workspace('ruangan_id');

        return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan,[
            'url' => 'purchase-requisition/get-stok-barang',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'barang_id' => $barangId,
                    'ruangan_id' => $ruanganId
                ]
            ],
            'returnResponse' => true
        ]);
    }
}
