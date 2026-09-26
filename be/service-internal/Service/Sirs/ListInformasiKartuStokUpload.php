<?php

namespace Integrasi\Service\Sirs;

use Yii;

use Integrasi\Contracts\DocoImplement;

use GuzzleHttp\Client;

class ListInformasiKartuStokUpload extends DocoImplement
{
    public function execute()
    {
        $this->uploadFile();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses export excel berhasil',
                'progress' => 100,
                'filename' => $this->unique_str
            ]),
        ]);
        return json_encode([
            'service' => 'Sirs-ListInformasiKartuStokUpload',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    private function uploadFile()
    {
        $client = $this->setUrl();
        try {
            $response = $client->post('inf-kartu-stok/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('uploads/'. $this->unique_str .'.xlsx'),
                        'filename' => $this->unique_str . $this->ext
                    ],
                ]
            ]);
            return json_decode($response->getBody(),true);
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
            'base_uri' => "http://localhost:8858/apotek/v1/",
            'headers' => $header
        ]);
        return $client;
    }
}