<?php 

/**
 * * @author Dede <dede.herdiana@sirs.co.id>
 * reusable getdata pdf into be
 * * @copyright by Sirs
 * */
namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class DataPdf extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;

		Yii::$app->redis->executeCommand('PUBLISH', [
			'channel' => 'export-pdf:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Proses menyiapkan data.',
				 'progress' => 10,
			]),
		]);
        $attributes = $this->getDataAttibutes();
        $cacheFiles->set($this->unique_str, $attributes);
        return json_encode([
            'service' => 'Sirs-DataPdf',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttibutes()
    {
        $client = $this->setUrl();
        try {
			$response = $client->get($this->data_uri, [
				'query' => $this->params
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

    private function setUrl()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => $this->base_uri,
			'headers' => $header
		]);

		return $client;
	}
}