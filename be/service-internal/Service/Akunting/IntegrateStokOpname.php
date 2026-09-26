<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Service\Akunting\Models\SyncStokOpname;

class IntegrateStokOpname extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $noStock = $this->noStok;
        $jenis_obat = ucfirst(DocoConstants::JENIS_OBAT);
        $jenis_barang = DocoConstants::JENIS_BARANG;

        $stock = SyncStokOpname::find()->where([
            'nostokopname' => $noStock
            ])->asArray()->all();            
        $dataSync = [];
        foreach ($stock as $value) {
            $id = $value['id'];
            $tipe = $value['tipe_transaksi'];

            if($tipe == $jenis_obat){
                $dataSync[$id] = [
                    'stokopname_id' => $value['id'],
                    'is_sync' => true,
                    'additional_data' => null
                ];
            }else{
                $dataSync[$id] = [
                    'stokopnamebarang_id' => $value['id'],
                    'is_sync' => true,
                    'additional_data' => null
                ];
            }
        }

        try {
            $encode = json_encode($stock);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('stokopname',[
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
                    'tipe' => $value['tipe_transaksi'],
                    'id' => $value['id'],
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