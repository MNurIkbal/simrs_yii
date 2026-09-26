<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Client;

class UploadLaporanPemeriksaanLabExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $response = $this->uploadFile();
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses berhasil',
                'progress' => 100,
                'filename' => $this->unique_str
            ]),
        ]);
        return json_encode([
            'service' => 'Sirs-UploadLaporanPemeriksaanLabExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    private function uploadFile()
    {
        $client = $this->setUrl();
        try {
            $response = $client->post('lap-pemeriksaan/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('uploads/'. $this->unique_str .'.xlsx'),
                        'filename' => 'Laporan Pemeriksaan Laboratorium.xlsx'
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
            'base_uri' => "http://localhost:8858/laboratorium/v1/",
            'headers' => $header
        ]);

        return $client;
    }


}