<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class DetailInvoiceGabung extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $attributes = $this->getDataAttibutes();
        $cacheFiles->set($this->unique_str, $attributes);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'invoice:'.$this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);
        return json_encode([
            'service' => 'Sirs-DetailInvoiceGabung',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttibutes()
    {
        $client = $this->setUrlKasir();
        try {
			$response = $client->get('inf-gabung-invoice/data-detail-invoice-gabung', [
				'query' => [
                    'invoicegabung_id' => $this->invoicegabung_id,
                    'nama_pegawai' => $this->nama_pegawai,
                ]
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

    private function setUrlKasir()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => "http://localhost:8858/kasir/v1/",
			'headers' => $header
		]);

		return $client;
	}
}