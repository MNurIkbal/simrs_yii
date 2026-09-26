<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class UploadLaporanCaraPulangPasien extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $response = $this->uploadFile();
		$dir = dirname(dirname(__DIR__));
		$rootPath = 'uploads';
		$path = $dir.'/'.$rootPath.'/'.$this->unique_str. '.pdf';
		if(file_exists($path)) {
			unlink($path);
		}
		
		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-pdf:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Proses berhasil',
				 'progress' => 100,
				 'filename' => $this->unique_str
			]),
		]);
		return json_encode([
			'service' => 'Sirs-UploadLaporanCaraPulangPasien',
			'payload' => $this->attributes,
			'timestamp' => date('Y-m-d H:i:s'),
			'response' => $response
		]);
    }	

	private function setUrlRm()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => "http://localhost:8858/rm/v1/",
			'headers' => $header
		]);

		return $client;
	}

	private function uploadFile()
	{
		$client = $this->setUrlRm();
		$dir = dirname(dirname(__DIR__));
		$rootPath = 'uploads';
		$path = $dir.'/'.$rootPath.'/'.$this->unique_str;
		$ext = '.pdf';
		$pathContents = $path.$ext;
		try {
			$response = $client->post('lap-cara-pulang-pasien/send-file', [
				[
					'query' => [
						'filePath' => $path
					]
				],
				'multipart' => [
					[
						'name' => 'file',
						'contents' => fopen($pathContents, 'r'),
					],
				]
			]);
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
	}
}
