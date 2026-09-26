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
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class UpdateAction extends Action {
    public function run($id) {
        $request = Yii::$app->request;
        $reference = $request->post('reference', null);
        $detail = $request->post('list_data','{}');
        $type = $request->post('type','{}');

        if($type == "obat") {
            $header = [
                'purchasereq_id' => DocoHelpers::decrypt($id),
                'reference' => $reference
            ];
        } else {
            $header = [
                'purchasereqbrg_id' => DocoHelpers::decrypt($id),
                'reference' => $reference
            ];
        }

        $post = [
            'header' => $header,
            'type' => $type,
            'details' => json_decode($detail)
        ];

        /** remove attr karna hanya untuk calculate di UI */
        foreach ($post['details'] as $key => $value) {
            unset($post['details']->$key->ext_st_farmasi);
            unset($post['details']->$key->ext_st_gudang);
            unset($post['details']->$key->ext_st_lain);
            unset($post['details']->$key->ext_qty_sugesstion);
        }

        try {
            $post = Yii::$app->docoRest->pengadaan->post('purchase-requisition/update-pr', [
                'json' => $post
            ]);
            $response = json_decode($post->getBody(), true);

            return DocoHelpers::response($response);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
