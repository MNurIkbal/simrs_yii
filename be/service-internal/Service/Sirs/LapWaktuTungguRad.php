<?php 
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use Integrasi\Components\DocoHelpers;

class LapWaktuTungguRad extends \Integrasi\Contracts\DocoImplement
{
   const RUJUKAN_RS = 1;
   const RUJUKAN_MASUK = 2;
   const RUJUKAN_KELUAR = 4;

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
            $tanggalLahir = ArrayHelper::getValue($value, 'tanggal_lahir');
            $tglMasukPenunjang = ArrayHelper::getValue($value, 'tglmasukpenunjang');
            $tglPersetujuan = ArrayHelper::getValue($value, 'tglpersetujuan');
            $tglAmbilFoto = ArrayHelper::getValue($value, 'tgl_ambilfoto');
            $tglHasil = ArrayHelper::getValue($value, 'tgl_hasilrad');
            $instalasiAsalNama = ArrayHelper::getValue($value, 'instalasiasal_nama');
            $rujukanDariNama = ArrayHelper::getValue($value, 'rujukandari_nama');
            $asalRujukanNama = ArrayHelper::getValue($value, 'asalrujukan_nama');
            $jenisRujukanId =  ArrayHelper::getValue($value, 'jenis_rujukan_id');

            $rsRujukan = '';
            if ($jenisRujukanId == self::RUJUKAN_MASUK || $jenisRujukanId == self::RUJUKAN_KELUAR) {
               $rsRujukan = $rujukanDariNama ;
            }

            $tmp[1] = $no;
            $tmp[2] = !empty($tglMasukPenunjang) ? date('d-M-Y H:i:s', strtotime($tglMasukPenunjang)) : '-';
            $tmp[3] = ArrayHelper::getValue($value, 'no_pendaftaran');
            $tmp[4] = ArrayHelper::getValue($value, 'nama_pasien');
            $tmp[5] = ArrayHelper::getValue($value, 'no_rekam_medik');
            $tmp[6] = ArrayHelper::getValue($value, 'jeniskelamin');
            $tmp[7] = !empty($tanggalLahir) ? date('d M Y', strtotime($tanggalLahir)) : '';
            $tmp[8] = ArrayHelper::getValue($value, 'dokter');
            $tmp[9] = ArrayHelper::getValue($value, 'jenispemeriksaanrad_nama');
            $tmp[10] = ArrayHelper::getValue($value, 'daftartindakan_nama');
            $tmp[11] = ArrayHelper::getValue($value, 'jenis_rujukan');
            $tmp[12] = $asalRujukanNama;
            $tmp[13] = $rsRujukan;
            $tmp[14] = !empty($tglMasukPenunjang) ? date('d-M-Y H:i:s', strtotime($tglMasukPenunjang)) : '-';
            $tmp[15] = !empty($tglPersetujuan) ? date('d-M-Y H:i:s', strtotime($tglPersetujuan)) : date('d-M-Y H:i:s', strtotime($tglMasukPenunjang));
            $tmp[16] = !empty($tglAmbilFoto) ? date('d-M-Y H:i:s', strtotime($tglAmbilFoto)) : '-';
            $tmp[17] = !empty($tglHasil) ? date('d-M-Y H:i:s', strtotime($tglHasil)) : '-';
            $tmp[18] = !empty($tglHasil) ? (new DocoHelpers)->getLamaTunggu($tglMasukPenunjang, $tglHasil) : '-';
            $tmp[19] = !empty($tglAmbilFoto) && !empty($tglHasil) ? (new DocoHelpers)->getLamaTunggu($tglAmbilFoto, $tglHasil) : '-';
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
         'service' => 'Sirs-LapWaktuTungguRad',
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
