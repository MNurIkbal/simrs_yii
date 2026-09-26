<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\web\Response;
use app\components\DocoHelpers;
use app\modules\pengadaan\models\PurchaseRequisitionForm;
use GuzzleHttp\Exception\RequestException;

class CancelAction extends Action {
    public function run($id,$type) {
        $request = Yii::$app->request;
        $detail = $request->post('list_data','{}');
        $post = [
            'type' => $type,
            'purchasereq_id' => $id,
            'details' => json_decode($detail)
        ];

        try {
            $post = Yii::$app->docoRest->pengadaan->post('purchase-requisition/cancel-pr', [
                'json' => $post
            ]);
            $response = json_decode($post->getBody(), true);

            return DocoHelpers::response($response);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}