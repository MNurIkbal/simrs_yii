<?php 
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;

class LapRekapLab extends \Integrasi\Contracts\DocoImplement
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
            $tglPersetujuan = ArrayHelper::getValue($value, 'tgl_persetujuan');
            $tmp[1] = $no;
            $tmp[2] = !empty($tglPersetujuan) ? date('d-M-Y', strtotime($tglPersetujuan)) : '-';
            $tmp[3] = ArrayHelper::getValue($value, 'kelaspelayanan_nama');
            $tmp[4] = ArrayHelper::getValue($value, 'daftartindakan_nama');
            $tmp[5] = ArrayHelper::getValue($value, 'tipe_prosedur');
            $tmp[6] = ArrayHelper::getValue($value, 'jumlah');
            $tmp[7] = number_format(ArrayHelper::getValue($value, 'total_harga', 0));

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
      }
      else {
         $cacheFiles->set($this->unique_str, $attributes);
      }

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-excel:'.$this->unique_str,
         'message' => json_encode(['unique_process' => $this->unique_str]),
      ]);
      
      return json_encode([
         'service' => 'Sirs-LapRekapLab',
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
