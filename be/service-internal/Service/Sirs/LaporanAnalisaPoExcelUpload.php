<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class LaporanAnalisaPoExcelUpload extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $response = $this->uploadFile();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses export excel berhasil',
                'filename' => $this->unique_str . '/' . $this->filename . $this->ext,
                'progress' => 100
            ]),
        ]);
        return json_encode([
            'service' => 'Sirs-LaporanAnalisaPoExcelUpload',
            'payload' => $this->attributes,
            'response' => $this->unique_str,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    protected function uploadFile()
    {
        try {
            $response = $this->setUrl()->post('info-purchase-order/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('uploads/'. $this->unique_str . $this->ext),
                        'filename' => $this->filename . $this->ext
                    ],
                ]
            ]);
            return json_decode($response->getBody(), true);
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
            'base_uri' => "http://localhost:8858/pengadaan/v1/",
            'headers' => $header
        ]);
        return $client;
    }
}
