<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Service\Akunting\Models\SyncPenerimaanSupplier;
use Integrasi\Contracts\DocoImplement;
use function GuzzleHttp\json_encode;

class IntegratePenerimaanSupplier extends DocoImplement
{
    public function execute()
    {
      $noPenerimaan = $this->noPenerimaan;
      $jenis = $this->jenis;
      $adjBarang = strtoupper(DocoConstants::JENIS_BARANG);
      $adjObat = strtoupper(DocoConstants::JENIS_OBAT);
      
      $noPenerimaanVerfikasi = SyncPenerimaanSupplier::find()->where([
        'no_penerimaan' => $noPenerimaan
        ])->asArray()->all();
        
        $dataSync = [];
        foreach ($noPenerimaanVerfikasi as $value) {   
          if($jenis == DocoConstants::ADJ.DocoConstants::JENIS_BARANG || DocoConstants::ADJ.DocoConstants::JENIS_OBAT){
            $jenis = substr($value['tipe_transaksi'], 0 ,4);
            $tipe = substr($value['tipe_transaksi'], 5,9);
          }  
          if($jenis == DocoConstants::JENIS_BARANG) {
            $penerimaanBarangId = $value['id'];
            $dataSync[$penerimaanBarangId] = [
              'penerimaanbarang_id' => $value['id'],
              'is_sync' => true,
              'additional_data' => null
            ];
          }
          if($jenis == DocoConstants::JENIS_OBAT) {
            $penerimaanObatId = $value['id'];
            $dataSync[$penerimaanObatId] = [
              'penerimaanobat_id' => $value['id'],
              'is_sync' => true,
              'additional_data' => null
            ];
          }
          if($jenis == DocoConstants::ADJM){
            if($tipe ==$adjBarang){
              $penerimaanBarangId = $value['id'];
              $dataSync[$penerimaanBarangId] = [
                'adjusmenbarang_id' => $value['id'],
                'is_sync' => true,
                'additional_data' => null
              ];
            }
            if($tipe == $adjObat){
              $penerimaanObatId = $value['id'];
              $dataSync[$penerimaanObatId] = [
                'adjusmenobat_id' => $value['id'],
                'is_sync' => true,
                'additional_data' => null
              ];
            }
          }
          if($jenis == DocoConstants::ADJK){
            if($tipe == $adjBarang){
              $penerimaanBarangId = $value['id'];
              $dataSync[$penerimaanBarangId] = [
                'adjusmenbarangkeluar_id' => $value['id'],
                'is_sync' => true,
                'additional_data' => null
              ];
            }
            if($tipe == $adjObat){
              $penerimaanObatId = $value['id'];
              $dataSync[$penerimaanObatId] = [
                'adjusmenobatkeluar_id' => $value['id'],
                'is_sync' => true,
                'additional_data' => null
              ];
            }
          }
        }
        // return json_encode($dataSync);
        
        try {
          $encode = json_encode($noPenerimaanVerfikasi);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('gudang',[
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
                    'penerimaan_id' => $value['id'],
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