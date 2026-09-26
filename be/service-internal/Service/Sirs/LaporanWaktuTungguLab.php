<?php 
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Doco\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;

class LaporanWaktuTungguLab extends \Integrasi\Contracts\DocoImplement
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
         $help = new DocoHelpers();
         $rujukanMasuk = 2;
         $rujukanKeluar = 4;
         foreach ($attributes as $value) {
            $tgl_dirujuk = ArrayHelper::getValue($value, 'tgl_dirujuk');
            $tanggal_lahir = ArrayHelper::getValue($value, 'tanggal_lahir');
            $tglmasukpenunjang = ArrayHelper::getValue($value, 'tglmasukpenunjang', '');
            $tgl_ambilsample = ArrayHelper::getValue($value, 'tgl_ambilsample', '');
            $tgl_expertise = ArrayHelper::getValue($value, 'tgl_expertise', '');
            $jam_ambilsample = ArrayHelper::getValue($value, 'jam_ambilsample', '');
            $jenis_rujukan_id =  ArrayHelper::getValue($value, 'jenis_rujukan_id');
            $rujukandari_nama =  ArrayHelper::getValue($value, 'rujukandari_nama', '');

            $rsRujukan = '';
            if ($jenis_rujukan_id == $rujukanMasuk || $jenis_rujukan_id == $rujukanKeluar) {
               $rsRujukan = $rujukandari_nama ;
            }
   
            $tmp[1] = $no;
            $tmp[2] = !empty($tgl_dirujuk) ? date('d-M-Y H:is', strtotime($tgl_dirujuk)) : '';
            $tmp[3] = ArrayHelper::getValue($value, 'no_pendaftaran');
            $tmp[4] = ArrayHelper::getValue($value, 'nama_pasien');
            $tmp[5] = ArrayHelper::getValue($value, 'no_rekam_medik');
            $tmp[6] = !empty($tanggal_lahir) ? date('d-M-Y', strtotime($tanggal_lahir)) : '';
            $tmp[7] = ArrayHelper::getValue($value, 'dokter');
            $tmp[8] = ArrayHelper::getValue($value, 'nama_sample');
            $tmp[9] = ArrayHelper::getValue($value, 'pemeriksaanlab_nama');
            $tmp[10] = ArrayHelper::getValue($value, 'jenis_rujukan');
            $tmp[11] = ArrayHelper::getValue($value, 'asalrujukan_nama');
            $tmp[12] = $rsRujukan;
            $tmp[13] = !empty($tglmasukpenunjang) ? date('d-M-Y H:is', strtotime($tglmasukpenunjang)) : '';
            $tmp[14] = !empty($tglmasukpenunjang) ? date('d-M-Y H:is', strtotime($tglmasukpenunjang)) : '';
            $tmp[15] = !empty($tgl_ambilsample) ? date('d-M-Y H:is', strtotime($tgl_ambilsample.' '. $jam_ambilsample)) : '';
            $tmp[16] = ArrayHelper::getValue($value, 'tgl_hasilpemeriksaanlab');
            $tmp[17] = $tgl_expertise;
            $tmp[18] = $help->getLamaTunggu($tgl_ambilsample.' '. $jam_ambilsample, $tgl_expertise);
            $tmp[19] = $help->getLamaTunggu($tglmasukpenunjang, $tgl_expertise);
            
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
         'service' => 'Sirs-LaporanWaktuTungguLab',
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
