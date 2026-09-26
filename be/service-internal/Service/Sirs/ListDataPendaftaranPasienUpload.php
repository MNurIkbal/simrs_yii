<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class ListDataPendaftaranPasienUpload extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $response = $this->uploadFile();
        $ext = $this->ext;
        $fileName = $this->filename;
        $uniqueStr = $this->unique_str;

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses export excel berhasil',
                'filename' => $uniqueStr.'/'.$fileName.$ext,
                'progress' => 100
            ]),
        ]);
        return json_encode([
            'service' => 'Sirs-ListDataPendaftaranPasienUpload',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    protected function uploadFile()
    {
        $client = $this->setUrl();
        $ext = $this->ext;
        $fileName = $this->filename;
        try {
            $response = $client->post('inf-pencarian-pasien/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('uploads/'. $this->unique_str . $ext),
                        'filename' => $fileName . $ext
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
            'base_uri' => "http://localhost:8858/pendaftaran/v1/",
            'headers' => $header
        ]);
        return $client;
    }
}