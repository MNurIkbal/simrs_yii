<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncCollections;
use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Contracts\DocoImplement;
use function GuzzleHttp\json_encode;

class IntegratePembayaranUangMuka extends DocoImplement
{
    public function execute()
    {
      $tandaBuktiBayarId = $this->uangMukaId;
      $uangMuka = SyncCollections::find()->where([
        'pembayaranpelayanan_id' => $tandaBuktiBayarId
        ])->asArray()->all();

        $dataSync = [];
        foreach ($uangMuka as $value) {
          $pembayaranPelayananId = $value['pembayaranpelayanan_id'];
          $dataSync[$pembayaranPelayananId] = [
            'bayaruangmuka_id' => $value['pembayaranpelayanan_id'],
            'is_sync' => true,
            'additional_data' => null
          ];
        }

        try {
          $encode = json_encode($uangMuka);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('kasir',[
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
                    'bayaruangmuka_id' => $value['pembayaranpelayanan_id'],
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