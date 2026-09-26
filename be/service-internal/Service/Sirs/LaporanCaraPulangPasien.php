<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;

class LaporanCaraPulangPasien extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $attributes = $this->getDataAttibutes($this->filter);
        $cacheFiles = Yii::$app->cacheFiles;
        
        $cacheFiles->set($this->unique_str, $attributes);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:'.$this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);
        
        return json_encode([
            'service' => 'Sirs-LaporanCaraPulangPasien',
            // 'payload' => $this->result,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);

        
    }

    private function getDataAttibutes($params)
    {
        $client = $this->setUrlRm();
        try {
			$response = $client->get('lap-cara-pulang-pasien/get-object-data', [
				'query' => $params
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


}