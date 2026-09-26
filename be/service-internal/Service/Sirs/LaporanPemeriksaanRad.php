<?php 
/**
* @author: [Ripan][ripan.majid@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;

class LaporanPemeriksaanRad extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      $filter = $this->filter;
      $tipe = ArrayHelper::getValue($filter, 'tipe', 1);
      $cacheFiles = Yii::$app->cacheFiles;
      $attributes = $this->getDataAttibutes($filter);
      if($tipe == 1) {
         $row = $tmpCache = [];
         $no = 1;
         $prefix = 0;
         foreach ($attributes as $value) {  
            $tarif_satuan = ArrayHelper::getValue($value, 'tarif_satuan', 0);
            $tarifcyto_tindakan = ArrayHelper::getValue($value, 'tarifcyto_tindakan', 0);
            $qty_tindakan = ArrayHelper::getValue($value, 'qty_tindakan', 0);
            $catatan_dokter = ArrayHelper::getValue($value, 'catatan_dokter', null);
            $catatan_dokterpengirim = ArrayHelper::getValue($value, 'catatan_dokterpengirim', '');
            
            $tmp[1] = $no;
            $tmp[2] = !empty($value['tgl_periksa']) ? date('d-M-Y H:i:s', strtotime($value['tgl_periksa'])) : '';
            $tmp[3] = ArrayHelper::getValue($value, 'no_pendaftaran', '');
            $tmp[4] = ArrayHelper::getValue($value, 'no_rekam_medik', '');
            $tmp[5] = ArrayHelper::getValue($value, 'nama_pasien', '');
            $tmp[6] = ArrayHelper::getValue($value, 'dokter_perujuk_nama', '');
            $tmp[7] = ArrayHelper::getValue($value, 'dokter', '');
            $tmp[8] = $catatan_dokter ?? $catatan_dokterpengirim;
            $tmp[9] = ArrayHelper::getValue($value, 'kelaspelayanan_nama', '');
            // $tmp[9] = ArrayHelper::getValue($value, 'nama_kelompok', '');
            // $tmp[10] = ArrayHelper::getValue($value, 'jenispemeriksaanrad_nama', '');
            // $tmp[11] = ArrayHelper::getValue($value, 'status_contrast', '');
            $tmp[10] = ArrayHelper::getValue($value, 'daftartindakan_nama', '');
            $tmp[11] = ($tarif_satuan+$tarifcyto_tindakan)*$qty_tindakan;
            
            $row[] = $tmp;
            $tmpCache[] = $tmp;
            if (($no%50) == 0) {
               Yii::$app->redis->executeCommand('PUBLISH', [
                  'channel' => 'export-excel:'.$this->unique_str,
                  'message' => json_encode(['unique_process' => $this->unique_str]),
               ]);
               $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
               $prefix++;
               $tmpCache = [];
            }
            $no++;
         }
         
         $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
      } else {
         $cacheFiles->set($this->unique_str, $attributes);
      }

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode(['unique_process' => $this->unique_str]),
      ]);
      
      return json_encode([
         'service' => 'Sirs-LaporanPemeriksaanRad',
         'timestamp' => date('Y-m-d H:i:s'),
         'response' => $this->unique_str
      ]);
   }

   private function getDataAttibutes($filter)
   {
      $client = $this->setEndPoint();
      $params = $this->params;
      $getDataUrl = ArrayHelper::getValue($params, 'getDataUrl');
      try {
         $response = $client->get($getDataUrl, [
            'query' => $filter
         ]);
         $response = json_decode($response->getBody(), true);
         $response = $response['response'];
         return $response;
      } catch (\GuzzleHttp\Exception\RequestException $e) {
         if($e->hasResponse()) {
            $response = $e->getResponse();
            return $response->getBody();
         }
      }
   }

   private function setEndPoint()
	{
		$params = $this->params;
      $baseUri = ArrayHelper::getValue($params, 'base_uri');
      $header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => $baseUri,
			'headers' => $header
		]);

		return $client;
	}
}
