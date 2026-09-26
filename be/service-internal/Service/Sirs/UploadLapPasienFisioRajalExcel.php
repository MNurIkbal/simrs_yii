<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;
use Integrasi\Contracts\DocoImplement;

class UploadLapPasienFisioRajalExcel extends DocoImplement
{
    public function execute()
    {
        $response = $this->uploadFile();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses berhasil',
                'progress' => 100,
                'filename' => $this->unique_str
            ]),
        ]);
        return json_encode([
            'service' => 'Sirs-UploadLapPasienFisioRajalExcel',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    private function uploadFile()
    {
        $client = $this->setUrl();
        try {
            $response = $client->post('laporan-pasien-fisioterapi-rajal/drop-file-excel-bgprocess', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('uploads/' . $this->unique_str . '.xlsx'),
                        'filename' => 'Laporan Pasien Fisioterapi Rawat Jalan.xlsx'
                    ],
                ]
            ]);
            return json_decode($response->getBody(), true);
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
