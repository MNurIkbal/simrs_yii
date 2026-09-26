<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class UploadLapPenerimaaanObatAlkesExport extends \Integrasi\Contracts\DocoImplement
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
            'service' => 'Sirs-UploadLapPenerimaaanObatAlkesExport',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    protected function uploadFile()
    {
        try {
            $response = $this->setUrl()->post('lap-penerimaan-obat-alkes/send-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('uploads/'. $this->unique_str .'.xlsx'),
                        'filename' => 'Laporan Penerimaan Obat Alkes.xlsx'
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
