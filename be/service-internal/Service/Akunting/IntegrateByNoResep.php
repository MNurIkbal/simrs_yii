<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncPengeluaranObat;
use Integrasi\Service\Akunting\Models\SyncAkuntansi;

class IntegrateByNoResep extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $noresep = $this->noresep;
        $resep = SyncPengeluaranObat::find()->where([
            'nomor' => $noresep
        ])->asArray()->all();
        $dataSync = [];
        foreach ($resep as $value) {
            $penjualanResep = $value['penjualanresep_id'];
            $dataSync[$penjualanResep] = [
                'penjualanresep_id' => $value['penjualanresep_id'],
                'is_sync' => true,
                'additional_data' => null
            ];
        }

        try {
            $encode = json_encode($resep);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('penjualanresep',[
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
                    'penjualanresep_id' => $value['penjualanresep_id'],
                    'is_sync' => false,
                    'additional_data' => $response
                ];
            }
            $dataSync = $fail;
        } 

        SyncAkuntansi::batchInsert($dataSync);

        return $response;
    }
}