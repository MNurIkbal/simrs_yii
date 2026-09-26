<?php

namespace Doco\pengadaan\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class SearchItemBarangAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $term = $request->get('term', null);
        $type = 'barang';
        
        $params = ['term' => $term, 'type' => $type];
        $response = $this->controller->guzzleExec(Yii::$app->docoRest->pengadaan,[
            'url' => 'purchase-requisition/get-item-barang',
            'method' => 'GET',
            'payload' => [
                'query' => $params
            ],
            'returnResponse' => true
        ]);
        $response = isset($response['data']) ? ArrayHelper::getValue($response['data'], 'data', []) : [];
        
        $list = [];
        foreach ($response as $dataItem) {
            $item = [
                "id" => ArrayHelper::getValue($dataItem, 'id'),
                "text" => ArrayHelper::getValue($dataItem, 'kod'). ' - ' . ArrayHelper::getValue($dataItem, 'nma'),
                "satuan" => ArrayHelper::getValue($dataItem, 'satuan'),
                "kode" => ArrayHelper::getValue($dataItem, 'kod'),
                "nama" => ArrayHelper::getValue($dataItem, 'nma'),
                "label_master" => ArrayHelper::getValue($dataItem, 'label_master'),
            ];
            $list[] = $item;
        }
        return ["results" => $list];
    }
}