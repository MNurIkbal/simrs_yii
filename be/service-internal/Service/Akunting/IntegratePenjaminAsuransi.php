<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Service\Akunting\Models\SyncPengajuanKlaim;

class IntegratePenjaminAsuransi extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $klaimId = $this->klaimId;
        $klaimId = (int)$klaimId;

        $klaim = SyncPengajuanKlaim::find()->where([
            'id' => $klaimId
            ])->asArray()->all();   

        $dataSync = [];
        foreach ($klaim as $value) {
            $pengajuanklaim_id = $value['id'];
            $dataSync[$pengajuanklaim_id] = [
                'pengajuanklaim_id' => $value['id'],
                'is_sync' => true,
                'additional_data' => null
            ];
        }

        try {
            $encode = json_encode($klaim);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('penjaminasuransi',[
                'form_params' => [
                    'data' => $dataEncrypt
                    ]
                    ]);
                    $response = json_decode($response->getBody(),true);
                    $response = json_encode($response);
        } catch (RequestException $e) {
            $response = $e->getMessage();
            $fail = [];
            foreach ($dataSync as $value) {
                $fail[] = [
                    'pengajuanklaim_id' => $value['id'],
                    'is_sync' => true,
                    'additional_data' => $response
                ];
            }
            $dataSync = $fail;
        } 

        SyncAkuntansi::batchInsert($dataSync);

        return $response;
    }
}