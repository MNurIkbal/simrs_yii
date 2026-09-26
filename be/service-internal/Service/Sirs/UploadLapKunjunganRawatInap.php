<?php
/**
 * * @author Budi <budi@sirs.co.id>
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

class UploadLapKunjunganRawatInap extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      $response = $this->uploadFile();
      $dir = dirname(dirname(__DIR__));
      $rootPath = 'uploads';
      $ext = ($this->isExcel == 1) ? '.xlsx' : '.pdf';
      $path = $dir.'/'.$rootPath.'/'.$this->unique_str.$ext;
      if(file_exists($path)) {
         unlink($path);
      }

      Yii::$app->redis->executeCommand('PUBLISH', [
         'channel' => 'export-pdf:'.$this->unique_str,
         'message' => json_encode([
               'status' => 'finish',
               'messageProcess' => 'Proses berhasil',
               'progress' => 100,
               'filename' => $this->unique_str
         ]),
      ]);
      return json_encode([
         'service' => 'Sirs-UploadLapKunjunganRawatInap',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
         'response' => $response
      ]);
   }

   private function setEndPoint()
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

   private function uploadFile()
   {
      $client = $this->setEndPoint();
      $dir = dirname(dirname(__DIR__));
      $rootPath = 'uploads';
      $path = $dir.'/'.$rootPath.'/'.$this->unique_str;
      if($this->isExcel == 1) {
         $multipart = [
            [
               'name' => 'file',
               'contents' => file_get_contents($path .'.xlsx'),
               'filename' => $this->unique_str. '.xlsx',
            ],
         ];
      }
      else {
         $ext = '.pdf';
         $multipart = [
            [
               'name' => 'file',
               'contents' => fopen($path.$ext, 'r'),
            ],
         ];
      }

      try {
         $response = $client->post('lap-kunjungan-rawat-inap/send-file', [
            [
               'query' => [
                  'filePath' => $path
               ]
            ],
            'multipart' => $multipart
         ]);
         if($this->isExcel == 1) {
            return json_decode($response->getBody(),true);
         }
      } catch (\GuzzleHttp\Exception\RequestException $e) {
         if($e->hasResponse()) {
            $response = $e->getResponse();
            return $response->getBody();
         }
      }
   }
}
