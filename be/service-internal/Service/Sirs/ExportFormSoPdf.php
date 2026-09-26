<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class ExportFormSoPdf extends \Integrasi\Contracts\DocoImplement {
    public function execute() {
        $attributes = $this->getDataAttibutes($this->filter);
        $cacheFiles = Yii::$app->cacheFiles;

        $cacheFiles->set($this->unique_str, $attributes);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode(['unique_process' => $this->unique_str]),
        ]);

        return json_encode([
            'service' => 'Sirs-ExportFormSoPdf',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function getDataAttibutes($params) {
        $client = $this->setUrl();
        try {
            $response = $client->get('formulir-stok-opname/get-object-data', [
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

    private function setUrl() {
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
}
