<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
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

class SearchItemAction extends Action {
    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $term = $request->get('term', null);
        $type = 'barang';
        
        try {
            $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
                'url' => 'purchase-requisition/get-item',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'term' => $term,
                        'type' => $type
                    ]
                ],
            ]);

            $list = [];
            foreach ($response['data'] as $data_item) {
                
                $item = [
                    "id" => $data_item['id'],
                    "text" => $data_item['kod']." - ".$data_item['nma'],
                    "satuan" => $data_item['satuan'],
                    "kode" => $data_item['kod'],
                    "nama" => $data_item['nma'],
                    "label_master" => $data_item['label_master']
                ];
                $list[] = $item;
            }

            return ["results" => $list];
        } catch (\Exception $e) {
            return $e->getMessage();
            return [];
        }
    }
}
