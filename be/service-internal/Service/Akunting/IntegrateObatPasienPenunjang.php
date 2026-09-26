<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncPengeluaranObat;
use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Service\Akunting\Models\SyncInfoPenunjang;

class IntegrateObatPasienPenunjang extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $penunjang_id = $this->pasienmasukpenunjang_id;
        $instalasi_id = $this->instalasi_id;
        $jenis_obat = 'BMHP'; 
        $infoPenunjang = SyncInfoPenunjang::find()->where([
            'pasienmasukpenunjang_id' => $penunjang_id
            ])->asArray()->one();
        $pendaftaran_id = $infoPenunjang['pendaftaran_id'];
        $obat = SyncPengeluaranObat::find()->where([
            'pendaftaran_id' => $pendaftaran_id,
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