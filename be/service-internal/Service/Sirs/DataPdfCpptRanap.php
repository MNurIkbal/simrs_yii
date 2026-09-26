<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class DataPdfCpptRanap extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $attributes = $this->getDataAttributes();
        $cacheFiles->set($this->unique_str, $attributes);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:'.$this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);
        return json_encode([
            'service' => 'Sirs-DataPdfCpptRanap',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttributes()
    {
        $client = $this->setUrlRanap();
        try {
			$response = $client->get('cppt/data-pdf-cppt-ranap', [
				'query' => [
                    'params' => $this->params,
                ]
                ]);
            $response = json_decode($response->getBody(), true);
			return $response;
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
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
}