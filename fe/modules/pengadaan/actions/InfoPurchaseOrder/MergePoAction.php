<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;

class MergePoAction extends Action {
    public function run() {
        $request = Yii::$app->request;
    	$po = $request->post('po', null);
    	if(is_null($po)) throw new \Exception("Parameter Tidak Ditemukan", 1);

    	$po = json_decode($po, true);
    	$list_po = is_array($po) ? array_column($po, 'primary') : [];
        $list_po = array_map(function($value) {
            return DocoHelpers::decrypt($value);
        }, $list_po);
		$list_po = array_values(array_unique($list_po));
        $type_po = DocoHelpers::decrypt($request->post('type_po', null));

		return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
			'url' => 'info-purchase-order/merge-po',
			'method' => 'post',
			'payload' => [
				'json' => [
					'list_po' => $list_po,
					'type_po' => $type_po
				]
			],
			'returnResponse' => true
		]);
    }
}
