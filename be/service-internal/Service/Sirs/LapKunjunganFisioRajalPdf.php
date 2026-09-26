<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;

class LapKunjunganFisioRajalPdf extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $params = $this->filter;
        $responseData = $this->getDatas($params);
        $attributes = ArrayHelper::getValue($responseData, 'attributes');
        $kodeDoc = ArrayHelper::getValue($responseData, 'kode_doc');
        if (empty($attributes) || empty($kodeDoc)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'failed',
                    'messageProcess' => 'Gagal mendapatkan data laporan kunjungan fisio rajal!',
                    'progress' => 0
                ]),
            ]);
        } else {
            $result = [
                'attributes' => $attributes,
                'kode_doc' => $kodeDoc,
            ];
            $cacheFiles->set($this->unique_str, $result);
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode(['unique_process' => $this->unique_str]),
            ]);
        }
        return json_encode([
            'service' => 'Sirs-LapKunjunganFisioRajalPdf',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    private function getDatas($params)
    {
        $client = $this->setUrl();
        try {
            $response = $client->get('laporan-kunjungan-fisioterapi-rajal/populate-data-pdf-bgprocess', [
                'query' => $params
            ]);
            $response = json_decode($response->getBody(), true);
            $response = $response['response'];
            return $response;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
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
            'base_uri' => "http://localhost:8858/fisioterapi/v1/",
            'headers' => $header
        ]);
        return $client;
    }
}
