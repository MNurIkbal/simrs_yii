<?php

namespace Integrasi\Service\Akunting;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Akunting\Models\SyncCollections;
use Integrasi\Service\Akunting\Models\SyncAkuntansi;
use Integrasi\Service\Akunting\Models\TagianPasienSudahBayar;
use Integrasi\Service\Akunting\Models\ObatAlkesPasien;
use Integrasi\Service\Akunting\Models\SyncPasienSudahBayar;
use Integrasi\Contracts\DocoImplement;

class IntegrateKasir extends DocoImplement
{
    public function execute()
    {
      $tindakanObat = $this->tindakan_obat;
      $pembayaranId = [];
      $tindakan = [];
      $obat = [];
      
      if(isset($tindakanObat['tindakan'])){
        $tindakan = TagianPasienSudahBayar::find()->where([
          'pelayanan_id' => array_keys($tindakanObat['tindakan']),
          'is_obat' => false
          ])->asArray()->all();
          
          foreach ($tindakan as $value) {
            $pembayaranId[$value['pembayaranpelayanan_id']] = $value['pembayaranpelayanan_id'];
          }
        }
        
        if(isset($tindakanObat['obat'])){
          $obat = TagianPasienSudahBayar::find()->where([
            'pelayanan_id' => array_keys($tindakanObat['obat']),
            'is_obat' => true
            ])->asArray()->all();

            /* Case untuk reseptur*/
            if(empty($obat)){
              $obatAlkes = ObatAlkesPasien::find()->where([
                'obatalkespasien_id' =>  array_keys($tindakanObat['obat'])
              ])->asArray()->all();

              foreach($obatAlkes as $key => $value) {
                $resepId = $value['penjualanresep_id'];
                $obat = SyncPasienSudahBayar::find()->where([
                  'penjualanresep_id' => $resepId
                ])->asArray()->all();
              }
            }
            
            foreach ($obat as $value) {
              $pembayaranId[$value['pembayaranpelayanan_id']] = $value['pembayaranpelayanan_id'];
            }
          }
          
      $pasienPulang = SyncCollections::find()->where([
        'pembayaranpelayanan_id' => array_keys($pembayaranId)
        ])->asArray()->all();

        $dataSync = [];
        foreach ($pasienPulang as $value) {
          $pembayaranPelayananId = $value['pembayaranpelayanan_id'];
          $dataSync[$pembayaranPelayananId] = [
            'pembayaranpelayanan_id' => $value['pembayaranpelayanan_id'],
            'is_sync' => true,
            'additional_data' => null
          ];
        }

        try {
          $encode = json_encode($pasienPulang);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('kasir',[
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
                    'pembayaranpelayanan_id' => $value['pembayaranpelayanan_id'],
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