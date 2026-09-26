<?php

namespace Doco\penjaminasuransi\actions\InformasiPasienRajalBpjs;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use yii\web\Response;

class GetDataAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $response = $this->controller->guzzleExec($this->_restPenjamin, [
            'url' => "inf-pasien-rajal-bpjs/index",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['kunjungan_id']);
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }
}
