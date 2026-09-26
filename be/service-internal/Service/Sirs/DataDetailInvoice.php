<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class DataDetailInvoice extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $params = [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'jenis_invoice' => $this->jenis_invoice,
            'nama_pegawai' => $this->nama_pegawai,
            'penjamin_id' => $this->penjamin_id,
            'outputAttributes' => true,
        ];
        $data = $this->getDataAttibutes($params);
        $attributes = isset($data['attributes']) ? $data['attributes'] : null;
        $kode_doc = isset($data['kode_doc']) ? $data['kode_doc'] : null;
        if(empty($attributes) || empty($kode_doc)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'invoice:'.$this->unique_str,
                'message' => json_encode([
                    'status' => 'failed', 
					 'messageProcess' => 'Gagal mendapatkan data detail invoice!',
					 'progress' => 0
                ]),
            ]);
        }
        else {
            $result = [ 
                'attributes' => $attributes,
                'kode_doc' => $kode_doc,
            ];
            $cacheFiles->set($this->unique_str, $result);
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'invoice:'.$this->unique_str,
                'message' => json_encode(['unique_process' => $this->unique_str]),
            ]);
        }
        return json_encode([
            'service' => 'Sirs-DataDetailInvoice',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttibutes($params)
    {
        $client = $this->setUrlKasir();
        try {
			$response = $client->get('tagihan-pasien/detail-invoice', [
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
