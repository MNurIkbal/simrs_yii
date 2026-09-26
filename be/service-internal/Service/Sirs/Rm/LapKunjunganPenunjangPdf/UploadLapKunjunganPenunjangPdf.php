<?php

/**
 * * @author Budi <budi@sirs.co.id>
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs\Rm\LapKunjunganPenunjangPdf;

use Yii;
use GuzzleHttp\Client;

class UploadLapKunjunganPenunjangPdf extends \Integrasi\Contracts\DocoImplement
{
    private function unlinkFile()
    {
        $dir = dirname(dirname(dirname(dirname(__DIR__))));
        $rootPath = 'uploads';
        $path = $dir . '/' . $rootPath . '/' . $this->unique_str . '.pdf';
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function execute()
    {
        $response = $this->uploadFile();
        $this->unlinkFile();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses berhasil',
                'progress' => 100,
                'filename' => $this->unique_str
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-UploadLapKunjunganPenunjangPdf',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    private function uploadFile()
    {
        $client = $this->setUrl();
        $dir = dirname(dirname(dirname(dirname(__DIR__))));
        $rootPath = 'uploads';
        $path = $dir . '/' . $rootPath . '/' . $this->unique_str;
        $ext = '.pdf';
        $pathContents = $path . $ext;
        try {
            $response = $client->post('lap-kunjungan-penunjang/drop-file-pdf-bgprocess', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($pathContents, 'r'),
                        'filename' => 'Laporan Kunjungan Penunjang.pdf'
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
            'base_uri' => "http://localhost:8858/rm/v1/",
            'headers' => $header
        ]);

        return $client;
    }
}
