<?php

namespace Integrasi\Service\Sirs\Rm\RekapPasienPerDiagnosa;

use Yii;
use GuzzleHttp\Client;

class UploadExcelFile extends \Integrasi\Contracts\DocoImplement
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
        $this->unlinkFile();
        return json_encode([
            'service' => 'Sirs-UploadFileLaporan',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    
    private function uploadFile()
    {
        try {
            $client = $this->setUrlRm();
            $response = $client->post('rekap-pasien-per-diagnosa/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => file_get_contents('uploads/' . $this->unique_str . '.xlsx'),
                        'filename' => 'Laporan Rekap Pasien per Diagnosa.xlsx'
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

    private function setUrlRm()
    {
        $header = [
            'Authorization' => $this->token,
            'user-agent' => 'cli',
            'X-Owner' => $this->xOwner,
        ];

        $client =  new Client([
            'base_uri' => "http://localhost:8858/rm/v1/",
            'headers' => $header
        ]);

        return $client;
    }
    
    private function unlinkFile()
    {
        $dir = dirname(dirname(dirname(dirname(__DIR__))));
        $rootPath = 'uploads';
        $path = $dir . '/' . $rootPath . '/' . $this->unique_str . '.xlsx';
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
