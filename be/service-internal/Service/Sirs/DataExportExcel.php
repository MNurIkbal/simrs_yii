<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class DataExportExcel extends \Integrasi\Contracts\DocoImplement
{
    
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $attributes = $this->getDataAttibutes($this->filter);
        $cacheFiles->set($this->unique_str, $attributes);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Berhasil menyiapkan data.',
				 'progress' => 70
			 ]),
        ]);
        return json_encode([
            'service' => 'Sirs-DataExportExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttibutes($params)
    {
        $client = $this->setUrl();
        try {
			$response = $client->get((isset($this->params['getDataUrl']) ? $this->params['getDataUrl'] : ''), [
				'query' => $params,
			]);
            $response = json_decode($response->getBody(), true);
			$response = !empty($response['response']) ? $response['response'] : [];
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
			'base_uri' => isset($this->params['base_uri']) ? $this->params['base_uri'] : '',
			'headers' => $header
		]);

		return $client;
	}
}