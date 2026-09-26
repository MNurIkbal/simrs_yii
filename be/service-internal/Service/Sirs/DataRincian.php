<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class DataRincian extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $no_transaksi = $this->no_transaksi;
        $type_po = $this->type_po;
        $temp = $this->getParams($no_transaksi, $type_po);
        if (empty($temp)) {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode([
                    'status' => 'failed',
                    'messageProcess' => 'Gagal mengextract data',
                ]),
            ]);
        } else {
            $cacheFiles->set($this->unique_str, $temp);
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:' . $this->unique_str,
                'message' => json_encode(['unique_process' => $this->unique_str]),
            ]);
        }
        $result = json_encode([
            'service' => 'Sirs-DataRincian',
            'timestamp' => date('Y-m-d H:i:s'),
            'countData' => count($temp),
        ]);
        return $result;
    }

    private function getParams($no_transaksi, $type_po) {
        $result = [];
        foreach ($no_transaksi as $index => $transaksi) {
            $result[] = [
                'no_transaksi' => $transaksi,
                'type_po' => $type_po[$index],
            ];
        }
        return $result;
    }

    private function getDataAttibutes($no_transaksi, $type_po)
    {
        $client = $this->setUrl();
        try {
            $response = $client->get('info-purchase-order/cetak-rincian', [
                'query' => [
                    'no_transaksi' => $no_transaksi,
                    'type_po' => $type_po,
                    'is_bgprocess' => true
                ]
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
            'base_uri' => "http://localhost:8858/pengadaan/v1/",
            'headers' => $header
        ]);

        return $client;
    }
}
