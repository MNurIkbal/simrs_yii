<?php 
/**
* @author: [Budi][budi@sirs.co.id]
* Powered by Sirs
*/

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;

class LaporanBatalPemeriksaanLab extends \Integrasi\Contracts\DocoImplement
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
            $tanggalLahir = ArrayHelper::getValue($value, 'tanggal_lahir');
            $tglBatal = ArrayHelper::getValue($value, 'tgl_batal');
            $tglRujukan = ArrayHelper::getValue($value, 'tgl_rujukan');
   
            $tmp[1] = $no;
            $tmp[2] = !empty($tglBatal) ? date('d-M-Y H:i', strtotime($tglBatal)) : '-';
            $tmp[3] = !empty($tglRujukan) ? date('d-M-Y H:i', strtotime($tglRujukan)) : '-';
            $tmp[4] = ArrayHelper::getValue($value, 'no_pendaftaran');
            $tmp[5] = ArrayHelper::getValue($value, 'no_rujukan');
            $tmp[6] = ArrayHelper::getValue($value, 'nama_pasien').' ('.ArrayHelper::getValue($value, 'jenis_kelamin').') / '.ArrayHelper::getValue($value, 'no_rekam_medik').' / '.(!empty($tanggalLahir) ? date('d F Y', strtotime($tanggalLahir)) : '');
            $tmp[10] = ArrayHelper::getValue($value, 'pemeriksaan');
            $tmp[11] = ArrayHelper::getValue($value, 'asalrujukan_nama');
            $tmp[12] = ArrayHelper::getValue($value, 'nama_pegawai');
            $tmp[13] = ArrayHelper::getValue($value, 'carabayar_nama').' / '.ArrayHelper::getValue($value, 'penjamin_nama');
            $tmp[15] = ArrayHelper::getValue($value, 'alasan_batal');
            $tmp[16] = ArrayHelper::getValue($value, 'disetujui_oleh');
            $tmp[17] = ArrayHelper::getValue($value, 'dibatalkan_oleh');

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
         'service' => 'Sirs-LaporanBatalPemeriksaanLab',
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