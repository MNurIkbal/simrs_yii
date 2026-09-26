<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Service\Akunting\Models\SyncRetur;

class IntegrateReturSupplier extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $noRetur = $this->noRetur;
        $jenis = $this->jenis;

        $retur = SyncRetur::find()->where([
            'no_returpenerimaanobat' => $noRetur
            ])->asArray()->all();            
            $dataSync = [];
            
            foreach ($retur as $value) {
                if($jenis == DocoConstants::JENIS_OBAT){
                    $returId = $value['id'];
                    $dataSync[$returId] = [
                        'returpenerimaanobat_id' => $value['id'],
                        'is_sync' => true,
                        'additional_data' => null
                    ];
                }
                if($jenis == DocoConstants::JENIS_BARANG){
                    $returId = $value['id'];
                    $dataSync[$returId] = [
                        'returpenerimaanbarang_id' => $value['id'],
                        'is_sync' => true,
                        'additional_data' => null
                    ];
                }
            }
            try {
            $encode = json_encode($retur);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('retur',[
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
                    'no_returpenerimaanobat' => $value['id'],
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