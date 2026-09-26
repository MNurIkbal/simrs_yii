<?php
/**
 * * @author Budi <budi@sirs.co.id>
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs\PenjaminAsuransi\PenerimaanPembayaran;

use Yii;
use GuzzleHttp\Client;

class UploadDetail extends \Integrasi\Contracts\DocoImplement
{
	public function execute()
	{
		$cacheFiles = Yii::$app->cacheFiles;
		$noPembayaran = $cacheFiles->get('no-pembayaran-'.$this->unique_str);
		$response = $this->uploadFile();
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'invoice:'.$this->unique_str,
			'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Proses berhasil',
            'progress' => 100,
            'filename' => $this->unique_str,
				'no_pembayaran' => $noPembayaran
			]),
		]);

		return json_encode([
			'service' => 'Sirs-UploadDetail',
			'payload' => $this->attributes,
			'timestamp' => date('Y-m-d H:i:s'),
			'response' => $response
		]);
    }	

	private function endPoint()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => "http://localhost:8858/penjaminasuransi/v1/",
			'headers' => $header
		]);

		return $client;
	}

	private function uploadFile()
	{
		$client = $this->endPoint();
      try {
         $response = $client->post('inf-penerimaan-pembayaran/send-file', [
            'query' => [
               'filePath' => $this->unique_str,
            ],
            'multipart' => [
               [
                  'name' => 'file',
                  'contents' => file_get_contents('uploads/' . $this->unique_str . '.xlsx'),
                  'filename' => $this->unique_str.'.xlsx'
               ],
            ]
         ]);
         return json_decode($response->getBody(), true);
      } catch (\GuzzleHttp\Exception\RequestException $e) {
         if ($e->hasResponse()) {
            $response = $e->getResponse();
            return $response->getBody();
         }
      }
	}
}