<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;

class SearchObatAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan, [
            'url' => 'purchase-requisition/get-item',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'term' => $request->get('term', null),
                    'is_consignment' => $request->get('is_consignment', false)
                ]
            ],
            'returnResponse' => true
        ]);

        $list = [];
        foreach ($response['data']['data'] as $data_obat) {
            $item = [
                "id" => $data_obat['id'],
                "text" => $data_obat['kod']." - ".$data_obat['nma'],
                "satuan" => $data_obat['satuan'],
                "kode" => $data_obat['kod'],
                "nama" => $data_obat['nma'],
                "label_master" => $data_obat['label_master'],
            ];
            $list[] = $item;
        }

        return ["results" => $list];
    }
}
