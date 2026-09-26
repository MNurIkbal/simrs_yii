<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncDisbursment;
use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Service\Akunting\Models\SyncPengembalianUangMuka;
use Integrasi\Contracts\DocoImplement;
use function GuzzleHttp\json_encode;

class IntegratePengembalianUangMuka extends DocoImplement
{
    public function execute()
    {
      $uangmukaId = $this->pengembalianuangmukaId;
      
      $pendaftaranId = SyncPengembalianUangMuka::find()->where([
        'pengembalianuangmuka_id' => $uangmukaId
        ])->asArray()->one();
        
        $pengembalianUangMuka = SyncDisbursment::find()->where([
          'pendaftaran_id' => $pendaftaranId['pendaftaran_id']
          ])->asArray()->all();
        
        $dataSync = [];
        foreach ($pengembalianUangMuka as $value) {
          $pembayaranPelayananId = $value['pembayaranpelayanan_id'];
          $dataSync[$pembayaranPelayananId] = [
            'bayaruangmuka_id' => $value['pembayaranpelayanan_id'],
            'is_sync' => true,
            'additional_data' => null
          ];
        }

        try {
          $encode = json_encode($pengembalianUangMuka);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('uangmuka',[
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