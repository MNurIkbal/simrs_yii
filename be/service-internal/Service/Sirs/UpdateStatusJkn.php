<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class UpdateStatusJkn extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $pendaftaran_id = isset($this->result['pendaftaran_id']) ? $this->result['pendaftaran_id'] : null;
        $status_antrian_jkn = isset($this->result['status_antrian_jkn']) ? $this->result['status_antrian_jkn'] : null;
        if (!empty($pendaftaran_id)){
            $response = $this->getDataAttibutes($pendaftaran_id, $status_antrian_jkn);
        } else {
            $response = [
                "message" => "pendaftaran_id tidak ditemukan"
            ];
        }
        return $this->setResponse($response);
    }

    private function getDataAttibutes($pendaftaran_id, $status_antrian_jkn)
    {
        $client = $this->setUrlPendaftaran();
        try {
			$response = $client->post('api/update-antrian-jkn', [
				'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'status_antrian_jkn' => $status_antrian_jkn,
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

    private function setUrlPendaftaran()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => "http://localhost:8858/pendaftaran/v1/",
			'headers' => $header
		]);

		return $client;
	}

    public function setResponse($response)
    {
        return json_encode([
            'service' => 'Sirs-UpdateStatusJkn',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

}