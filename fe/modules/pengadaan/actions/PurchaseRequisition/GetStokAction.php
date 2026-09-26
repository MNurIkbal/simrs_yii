<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
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

class GetStokAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $oid = $request->get('oid', "");
        $type = $request->get('type', "");
        $rid = Yii::$app->docoVars->workspace('ruangan_id');

        if($type == "") {
            $type = "obat";
        }

        try {
            $get = Yii::$app->docoRest->pengadaan->get('purchase-requisition/get-stok',[
                'query' => [
                    'oid' => $oid,
                    'rid' => $rid,
                    'type' => $type
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
