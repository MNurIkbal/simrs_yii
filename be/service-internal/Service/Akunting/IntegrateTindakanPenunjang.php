<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Service\Akunting\Models\SyncTindakan;
use Integrasi\Service\Akunting\Models\SyncInfoPenunjang;

class IntegrateTindakanPenunjang extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $penunjang_id = $this->pasienmasukpenunjang_id;
        $instalasi_id = $this->instalasi_id;
        $infoPenunjang = SyncInfoPenunjang::find()->where([
            'pasienmasukpenunjang_id' => $penunjang_id
            ])->asArray()->one();
        $pendaftaran_id = $infoPenunjang['pendaftaran_id'];
        $tindakan = SyncTindakan::find()->where([
            'pendaftaran_id' => $pendaftaran_id,
            'instalasi_id' => $instalasi_id
            ])->asArray()->all();
        $dataSync = [];
        foreach ($tindakan as $value) {
            $komponentarif_kode = $value['id'];
            $dataSync[$komponentarif_kode] = [
                'tindakankomponen_id' => $value['id'],
                'is_sync' => true,
                'additional_data' => null
            ];
        }
        try {
            $encode = json_encode($tindakan);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('tindakan',[
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