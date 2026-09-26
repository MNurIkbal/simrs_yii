<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class UploadFormSoPdf extends \Integrasi\Contracts\DocoImplement {
    public function execute() {
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
			'service' => 'Sirs-UploadFormSoPdf',
			'payload' => $this->attributes,
			'timestamp' => date('Y-m-d H:i:s'),
			'response' => $response
		]);
    }	

	private function setUrl()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => "http://localhost:8858/apotek/v1/",
			'headers' => $header
		]);

		return $client;
	}

	private function uploadFile() {
		$client = $this->setUrl();
		$dir = dirname(dirname(__DIR__));
		$rootPath = 'uploads';
		$path = $dir.'/'.$rootPath.'/'.$this->unique_str;
		$ext = '.pdf';
		$pathContents = $path.$ext;
		try {
			$response = $client->post('formulir-stok-opname/send-file', [
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
