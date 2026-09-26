<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class SearchItemAction extends Action {
    public function run($instalasi_id) {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = Yii::$app->docoRest->pengadaan->get('allow/list-item',[
                'query' => [
                    'term' => $request->get('term'),
                    'instalasi_id' => $instalasi_id,
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                if($instalasi_id == 15) {
                    $response[] = [
                        'id' => $value['barang_id'].'-B',
                        'text' => $value['barang_nama'],
                    ];
                }
                else {
                    $response[] = [
                        'id' => $value['obatalkes_id'].'-O',
                        'text' => $value['obatalkes_nama'],
                    ];
                }
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }
}