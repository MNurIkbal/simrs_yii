<?php

namespace Doco\fisioterapi\actions\InformasiPasienFisioterapi;

use Yii;
use yii\web\Response;
use app\components\DocoHelpers;

class KunjunganAction extends BaseCurrentAction
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $filter = [];
        $request = Yii::$app->request;
        $response = Yii::$app->docoRest->fisioterapi->get('informasi-pasien-fisioterapi/index', [
            'query' => $filter
        ]);
        $body = json_decode($response->getBody(), True);
        $no   = $request->get('start', 1);
        $data = [];
        foreach ($body['response']['data'] as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
            $value['primary']   = $primaryKey;
            $value['id']        = $primaryKey;
            $value['pasien_id'] = DocoHelpers::encrypt($value['pasien_id']);
            $data[] = $value;
        }
        return ['data' => $data];
    }
}
