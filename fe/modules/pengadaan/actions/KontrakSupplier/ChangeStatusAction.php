<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;

class ChangeStatusAction extends Action {
	public function run($id, $status) {
        return $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'kontrak-supplier/update-status',
            'method' => 'PUT',
            'payload' => [
                'query' => [
                    'id' => DocoHelpers::decrypt($id)
                ],
                'form_params' => [
                    'is_active' => $status
                ]
            ],
            'returnResponse' => true
        ]);
	}	
}
