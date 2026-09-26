<?php

namespace Doco\gudang\actions\LaporanAdjustmentBarang;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\Traits\ControllerHelperTrait;

class GetDataAction extends Action
{
    use ControllerHelperTrait;
    public function run($tipe = 'obat')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['tipe'] = $tipe;
        $endPoint = $this->controller->_endpoint;
        $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => $endPoint . 'get-data',
            'payload' => [
                'query' => $payload,
            ]
        ]);
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }
}
