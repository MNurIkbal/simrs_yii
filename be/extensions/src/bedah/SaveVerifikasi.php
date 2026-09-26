<?php

/**
 * @author : Budi
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Extensions\bedah;

use Yii;
use Doco\exceptions\ValidationException;
use Doco\models\Bedah\TimOperasi;
use Doco\models\Bedah\InpostOperasiDetail;
use Doco\Traits\ControllerHelperTrait;

class SaveVerifikasi extends \Doco\processes\SaveVerifikasiProcess
{
    use ControllerHelperTrait;
    
    protected function save()
    {
    	$restSerconn = Yii::$app->serconn->guzzle();
    	$getServices = TimOperasi::find()
	      ->select([
	          'timoperasi_t.inpostoperasi_id',
	          'timoperasi_t.posisi_tim',
	          'timoperasi_t.harga',
	          'timoperasi_t.persentase',
	          'tindakanoperasi_mp.daftartindakan_id',
	          'timoperasi_t.pegawai_id'
	      ])
	      ->rightJoin('tindakanoperasi_mp', 'timoperasi_t.posisi_tim = tindakanoperasi_mp.timoperasi_id')
	      ->where(['inpostoperasi_id' => $this->headerBill['inpostoperasi_id']])
	      ->asArray()->all();

	    $getBillsDetail = InpostOperasiDetail::find()
		    ->where(['inpostoperasi_id' => $this->headerBill['inpostoperasi_id']])
		    ->all();
              
      $detailIntegration = [];
      $isCyto = false;
      $isPenyulit = false;
      foreach($getBillsDetail as $bill){
          if( $bill->is_cyto ){
              $isCyto = $bill->is_cyto;
          }
          if( $bill->is_penyulit ){
              $isPenyulit = $bill->is_penyulit;
          }
          $detailIntegration[] = [
              'dokter_id' => $bill->dokter_id,
              'perawat_id' => null,
              'tipepaket_id' => null,
              'daftartindakan_id' => $bill->daftartindakan_id,
              'is_cyto' => $bill->is_cyto,
              'is_penyulit' => $bill->is_penyulit,
              'qty' => 1,
              'harga' => $bill->harga
          ];
      }
      foreach($getServices as $service){
          $detailIntegration[] = [
              'dokter_id' => $service['pegawai_id'],
              'perawat_id' => null,
              'tipepaket_id' => null,
              'daftartindakan_id' => $service['daftartindakan_id'],
              'is_cyto' => $isCyto,
              'is_penyulit' => $isPenyulit,
              'qty' => 1,
              'harga' => (int) $service['harga']
          ];
      }
      $integrationData = [
          'no_pendaftaran' => $this->headerBill['no_pendaftaran'],
          'tgl_transaksi' => date('Y-m-d H:i:s'),
          'instalasi_id' => $this->headerBill['instalasi_id'],
          'ruangan_id' => $this->headerBill['ruangan_id'],
          'penjamin_id' => $this->headerBill['penjamin_id'],
          'kelas_pelayanan_id' => $this->headerBill['kelaspelayanan_id'],
          'detail_tindakan' => $detailIntegration
      ];
      $headers = [
          'authorization' => Yii::$app->request->headers['authorization'],
          'x-owner' => Yii::$app->request->headers['x-owner'],
      ];
      $request = $restSerconn->post('on/integrasibilling/verifikasitagihan', [
          'form_params' => [ 
              'bills' => $integrationData,
              'headers' => $headers
          ]
      ]);
      $response = json_decode($request->getBody(), true);
      $responseStatus = $response['Results'][0]['data']['metadata']['status'] ? $response['Results'][0]['data']['metadata']['status'] : 200;
      $responseBody = isset($response['Results'][0]['data']['response']) ? $response['Results'][0]['data']['response'] : [];
      if( $responseStatus != 200 ){
          return $this->responseJson($responseStatus,'Terjadi Kesalahan Integrasi');
      }
    }
}
