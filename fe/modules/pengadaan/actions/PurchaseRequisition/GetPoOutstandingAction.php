<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class GetPoOutstandingAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $oid = $request->get('oid', "");

        try {
            $get = Yii::$app->docoRest->pengadaan->get('purchase-requisition/get-po-outstanding',[
                'query' => [
                    'oid' => $oid
                ]
            ]);
            $response = json_decode($get->getBody(), true);

            return DocoHelpers::response($response);
        } catch (\Exception $e) {
            echo json_encode($e->getMessage()); die;

            return $e->getMessage();
        }
    }
}
