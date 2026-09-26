<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncPengeluaranObat;
use Integrasi\Service\Akunting\Models\SyncAkuntansi;

class IntegrateBmhp extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $oa_id = $this->oa_id;
        $instalasi_id = $this->instalasi_id;
        $bmhp = SyncPengeluaranobat::find()->where([
            'id' => $oa_id, 
            'jenis' => 'BMHP', 
            'instalasi_id' => $instalasi_id
        ])->asArray()->all();

        $dataSync = [];
        foreach ($bmhp as $value) {
            $oaId = $value['id'];
            $dataSync[$oaId] = [
                'obatalkespasien_id' => $oaId,
                'is_sync' => true,
                'additional_data' => null
            ];
        }

        try {
            $encode = json_encode($bmhp);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('tindakanbmhp',[
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
                    'obatalkespasien_id' => $value['obatalkespasien_id'],
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