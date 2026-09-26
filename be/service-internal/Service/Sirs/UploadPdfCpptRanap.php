<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class UploadPdfCpptRanap extends \Integrasi\Contracts\DocoImplement
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
				 'filename' => $this->unique_str,
			]),
		]);
		return json_encode([
			'service' => 'Sirs-UploadPdfCpptRanap',
			'payload' => $this->attributes,
			'timestamp' => date('Y-m-d H:i:s'),
			'response' => $response,
			'path' => $path
		]);
    }	

	private function setUrlRanap()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => "http://localhost:8858/ranap/v1/",
			'headers' => $header
		]);

		return $client;
	}

	private function setUrlPenjamin()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
			'Content-Type' => 'application/json'
		];
		$client =  new Client([
			'base_uri' => "http://localhost:8858/penjaminasuransi/v1/",
			'headers' => $header
		]);

		return $client;
	}

	private function uploadFile()
	{
		$is_ftp =  filter_var($this->is_ftp, FILTER_VALIDATE_BOOLEAN);
		if($is_ftp) {
			$client = $this->setUrlPenjamin();
		} else {
			$client = $this->setUrlRanap();
		}
		$dir = dirname(dirname(__DIR__));
		$rootPath = 'uploads';
		$dokumenName = $this->unique_str;
		if($this->fileName != null || $this->fileName != '') {
			$dokumenName = $this->fileName;
		}  
		$path = $dir.'/'.$rootPath.'/'.$dokumenName;
		$ext = '.pdf';
		$pathContents = $path.$ext;

		try {
			// Kondisi upload ftp disini.
			if($is_ftp) {
				$params = [
					'filePath' => $pathContents,
					'pendaftaran_id' => $this->pendaftaran_id
				];
				Yii::error($params);
				$response = $client->get('inf-pasien-ranap-bpjs/ftp-send-file?'.http_build_query($params));
			} else {
				$response = $client->post('cppt/send-file', [
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
			}
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
	}
}