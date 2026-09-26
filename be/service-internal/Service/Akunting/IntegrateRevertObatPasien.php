<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncRevertPengeluaranObat;
use Integrasi\Service\Akunting\Models\SyncAkuntansi;

class IntegrateRevertObatPasien extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $oaId = $this->oaId;
        $jenis_obat = 'BMHP'; 
        $instalasi_id = $this->instalasi_id;
        $crud_method = $this->crud_method;
        $obat = SyncRevertPengeluaranObat::find()->where([
            'id' => $oaId,
            'instalasi_id' => $instalasi_id,
            'jenis' => $jenis_obat
            ])->asArray()->all();
        $dataSync = [];
        $dataMethod = [];
        foreach ($obat as $value) {
            $row = $value;
            $row['crud_method'] = $crud_method;
            $dataMethod[] = $row;
            $jenisObatAlkes = $value['id'];
            $dataSync[$jenisObatAlkes] = [
                'obatalkespasien_id' => $value['id'],
                'is_sync' => true,
                'additional_data' => null
            ];
        }

        try {
            $encode = json_encode($dataMethod);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('tindakanbmhp',[
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
                'obatalkespasien_id' => $value['id'],
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