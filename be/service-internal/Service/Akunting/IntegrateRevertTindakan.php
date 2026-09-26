<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Service\Akunting\Models\SyncRevertTindakan;
use Integrasi\Service\Akunting\Models\Tindakankomponen;

class IntegrateRevertTindakan extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $tindakanpelayanan_id = $this->tindakanpelayanan_id;
        $instalasi_id = $this->instalasi_id;
        $crud_method = $this->crud_method;

        $tindakanKomponen = Tindakankomponen::find(true)->where([
            'tindakanpelayanan_id' => $tindakanpelayanan_id,
        ])->asArray()->all();

        foreach ($tindakanKomponen as $value) {
            $komponenId = $value['tindakankomponen_id'];
        }

        $tindakan = SyncRevertTindakan::find()->where([
                'id' => $komponenId,
                'instalasi_id' => $instalasi_id,
        ])->asArray()->all(); 
        
        
        $dataSync = [];
        $dataMethod = [];
        foreach ($tindakan as $value) {
            $row = $value;
            $row['crud_method'] = $crud_method;
            $dataMethod[] = $row;
            $komponentarif_kode = $value['id'];
            $dataSync[$komponentarif_kode] = [
                'tindakankomponen_id' => $value['id'],
                'is_sync' => true,
                'additional_data' => null
            ];
        }
        try {
            $encode = json_encode($dataMethod);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('tindakan',[
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
                    'tindakankomponen_id' => $value['id'],
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