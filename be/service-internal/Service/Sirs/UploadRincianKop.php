<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;
use Integrasi\Components\DocoHelpers;

class UploadRincianKop extends \Integrasi\Contracts\DocoImplement {
    public function execute()
    {
        $response = $this->uploadFile();
        if($response) {
            $dir = dirname(dirname(__DIR__));
            $rootPath = 'uploads';
            $pathFolder = $dir.'/'.$rootPath.'/'.$this->unique_str;
            $pathFile = $pathFolder.'.zip';
            if(file_exists($pathFile)) {
                unlink($pathFile);
            }
            Yii::$app->redis->executeCommand('PUBLISH',[
                'channel' => 'export-pdf:'.$this->unique_str,
                'message' => json_encode([
                    'status' => 'finish', 
                    'messageProcess' => 'Proses berhasil',
                    'progress' => 100,
                    'filename' => $this->unique_str
                ]),
            ]);
        } else {
            Yii::$app->redis->executeCommand('PUBLISH', [
                'channel' => 'export-pdf:'.$this->unique_str,
                'message' => json_encode([
                    'status' => 'failed', 
                    'messageProcess' => 'Gagal mengimport data, silahkan dicoba lagi.',
                ]),
            ]);
        }
        return json_encode([
            'service' => 'Sirs-UploadRincianKop',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response,
        ]);
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
    
    private function uploadFile()
    {
        $client = $this->setUrl();
        $dir = dirname(dirname(__DIR__));
        $rootPath = 'uploads';
        $path = $dir.'/'.$rootPath.'/'.$this->unique_str.'.zip';
        if(!file_exists($path)) {
            return false;
        }
        try {
            $response = $client->post('info-purchase-order/send-file-zip', [
                [
                    'query' => [
                        'filePath' => $path
                    ]
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($path, 'r'),
                    ],
                    ]
            ]);
            $body = json_decode($response->getBody(), true);
            return $body['response'];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if($e->hasResponse()) {
                $response = $e->getResponse();
                return $response->getBody();
            }
        }
    }
}
