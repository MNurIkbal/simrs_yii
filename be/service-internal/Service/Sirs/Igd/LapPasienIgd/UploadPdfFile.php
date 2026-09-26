<?php
namespace Integrasi\Service\Sirs\Igd\LapPasienIgd;

use Yii;

class UploadPdfFile extends Pdf
{
    public function execute()
    {
        $response = $this->uploadFile();
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-pdf:' . $this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Proses berhasil',
                'progress' => 100,
                'filename' => $this->unique_str
            ]),
        ]);
        $this->unlinkFile();
        return json_encode([
            'service' => 'Sirs-LapPasienIgd',
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
            $response = $client->post('lap-pasien-igd/drop-file', [
                'query' => [
                    'filePath' => $this->unique_str,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($pathContents, 'r'),
                        'filename' => 'LAPORAN PASIEN RAWAT DARURAT.pdf'
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

    private function unlinkFile()
    {
        $dir = dirname(dirname(dirname(dirname(__DIR__))));
        $rootPath = 'uploads';
        $path = $dir . '/' . $rootPath . '/' . $this->unique_str . '.pdf';
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
