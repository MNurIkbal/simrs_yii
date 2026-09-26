<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class CheckTransactionAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($request->get('id'));
        try {
            $response = Yii::$app->docoRest->master->get('margin-harga/check-transaction?id='.$id,
                            ['form_params' => []
                        ]);

            $body = json_decode($response->getBody(), true);
            $resResponse = $body['response']['title'];
            $resmetadata = $body['metadata']['status'];
            return $resmetadata;
        } catch (Exception $e) {
            return 422;
        }
    }
}