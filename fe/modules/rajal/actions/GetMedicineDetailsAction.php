<?php

namespace Doco\rajal\actions;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class GetMedicineDetailsAction extends Action {
    public function run()
    {
        $obatalkes_id = Yii::$app->request->get('obatalkes_id', null);
        $additionalFilters = [];
        $limit = 10;

        if ( !is_null($obatalkes_id) ) {
            $additionalFilters['additional_filters'] = [
                'obatalkes_id' => $obatalkes_id
            ];
            $limit = count($obatalkes_id);
        }

        return$this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'allow/get-list-stok-apotek',
            'payload' => [
                'query' => array_merge([
                    'penjamin_id'  => Yii::$app->request->get('penjamin_id', null),
                    'group_jenisobat' => Yii::$app->request->get('group_jenisobat', null),
                    'ruangan_id' => Yii::$app->request->get('ruangan_id', null),
                    'kelaspelayanan_id' => Yii::$app->request->get('kelaspelayanan_id', null),
                    'get_konfig_stok' => true,
                    'limit' => $limit
                ], $additionalFilters)
            ],
            'returnResponse' => true
        ]);
    }
}
