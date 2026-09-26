<?php

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class RuanganStokObatAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $oid = $request->get('oid');

        return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan,[
            'url' => 'purchase-requisition/ruangan-stok-obat',
            'method' => 'GET',
            'payload' => [
                'query' => ['oid' => explode(" ", $oid)]
            ],
            'returnResponse' => true
        ]);
    }
}
