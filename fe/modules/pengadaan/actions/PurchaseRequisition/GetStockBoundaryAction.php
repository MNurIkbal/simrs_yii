<?php

/**
 * @author : Lukman (muhamad.lukman@sirs.co.id)
 * A product of PT. Citra Raya Nusatama
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

class GetStockBoundaryAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $oid = $request->get('oid', "");
        $rid = Yii::$app->docoVars->workspace('ruangan_id');

        try {
            $get = Yii::$app->docoRest->apotek->get('allow/get-konfig-rak',[
                'query' => [
                    'obatalkes_id' => $oid,
                    'ruangan_id' => $rid
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
