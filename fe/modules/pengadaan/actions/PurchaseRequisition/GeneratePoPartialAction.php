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

class GeneratePoPartialAction extends Action {
    public function run($id) {
        $request = Yii::$app->request;
        $detail = $request->post('details',[]);
        $post = [
            'type' => $request->get('type'),
            'purchasereq_id' => $id,
            'details' => $detail
        ];

        try {
            $post = Yii::$app->docoRest->pengadaan->post('generate-po/by-detail-pr', [
                'json' => $post
            ]);
            $response = json_decode($post->getBody(), true);

            return DocoHelpers::response($response);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}