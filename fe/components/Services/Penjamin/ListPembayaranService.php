<?php

namespace app\components\Services\Penjamin;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;


class ListPembayaranService extends BaseCurrentService
{
    public function execute()
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);
        $additionalPayload = $request->get('additionalPayload', []);
        $pengajuanKlaimId = ArrayHelper::getValue($additionalPayload, 'pengajuanklaim_id');
        $pengajuanKlaimId = DocoHelpers::decrypt($pengajuanKlaimId);
        $additionalPayload['pengajuanklaim_id'] = $pengajuanKlaimId;
        $response = $this->guzzleExec($this->restPenjaminAsuransi, [
            'url' => 'select-options/list-pembayaran',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'additionalPayload' => $additionalPayload
                ]
            ]
        ]);
        $resData = [
            'result' => $response,
            'pagination' => [
                'more' => count($response) == 10 ? true : false,
            ],
        ];
        return $this->responseJson(200, 'Data berhasil diambil!', $resData);
    }
}
