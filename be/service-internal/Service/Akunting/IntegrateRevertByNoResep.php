<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncRevertPengeluaranObat;
use Integrasi\Service\Akunting\Models\SyncAkuntansi;

class IntegrateRevertByNoResep extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $noresep = $this->noresep;
        $crud_method = $this->crud_method;
        $resep = SyncRevertPengeluaranObat::find(true)->where([
            'nomor' => $noresep,
            'is_deleted' => true,
        ])->asArray()->all();
        // return json_encode($resep);
        $dataSync = [];
        $dataMethod = [];
        foreach ($resep as $value) {
            $row = $value;
            $row['crud_method'] = $crud_method;
            $dataMethod[] = $row;
            $penjualanResep = $value['penjualanresep_id'];
            $dataSync[$penjualanResep] = [
                'penjualanresep_id' => $value['penjualanresep_id'],
                'is_sync' => true,
                'additional_data' => null
            ];
        }

        try {
            $encode = json_encode($dataMethod);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('penjualanresep',[
                        'form_params' => [
                            'data' => $dataEncrypt
                        ],
                        'query' => [
                            'crud_method' => $crud_method
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