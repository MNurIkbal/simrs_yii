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

class SplitPOAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $title = \Yii::t('fe', 'Alih Supplier');
            $transaksi_id = DocoHelpers::decrypt($request->get('id'));
            $type_po = DocoHelpers::decrypt($request->get('type_po'));
            $item_key = $request->get('item_key');

            $response = Yii::$app->docoRest->pengadaan->get('info-purchase-order/split-po?', [
                'query' => [
                    'id' => $transaksi_id,
                    'type_po' => $type_po,
                    'item_key' => $item_key
                ]
            ]);

            $response = json_decode($response->getBody(),true);
            $data = $response['response']['detail'];
            $list_supplier = $response['response']['list_supplier'];
            $list_konversi = $response['response']['list_konversi'];

            return $this->controller->renderAjax('modal-split-po', get_defined_vars());
        } catch (RequestException $e) {
           return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}
