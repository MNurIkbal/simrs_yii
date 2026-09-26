<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncPengeluaranObat;
use Integrasi\Service\Akunting\Models\SyncAkuntansi;

class IntegrateObatPasien extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $no_pendaftaran = $this->no_pendaftaran;
        $jenis_obat = 'BMHP'; 
        $instalasi_id = $this->instalasi_id;
        $obat = SyncPengeluaranObat::find()->where([
            'no_pendaftaran' => $no_pendaftaran,
            'instalasi_id' => $instalasi_id,
            'jenis' => $jenis_obat
            ])->asArray()->all();
            $dataSync = [];
            foreach ($obat as $value) {
                $jenisObatAlkes = $value['id'];
                $dataSync[$jenisObatAlkes] = [
                    'obatalkespasien_id' => $value['id'],
                    'is_sync' => true,
                    'additional_data' => null
                ];
            }

        try {
            $encode = json_encode($obat);
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