<?php

namespace Integrasi\Service\Sirs\PenjaminAsuransi\MonitoringBpjs;

use Yii;
use GuzzleHttp\Client;

class UploadFile extends \Integrasi\Contracts\DocoImplement
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
         'service' => 'Sirs-UploadExcel',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
         'response' => $response
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
         'base_uri' => "http://localhost:8858/penjaminasuransi/v1/",
         'headers' => $header
      ]);

      return $client;
   }

   private function uploadFile()
   {
      $client = $this->setUrl();
      if($this->type == 1) {
         $multipart = [
            [
               'name' => 'file',
               'contents' => file_get_contents('uploads/' . $this->unique_str .'.pdf'),
               'filename' => $this->unique_str. '.pdf',
            ],
         ];
      }
      else {
         $multipart = [
            [
               'name' => 'file',
               'contents' => file_get_contents('uploads/' . $this->unique_str . '.xlsx'),
               'filename' => $this->unique_str. '.xlsx',
            ],
         ];
      }
      try {
         $response = $client->post('monitoring-pasien-bpjs/drop-file', [
            [
               'query' => [
                  'filePath' => $this->unique_str
               ]
            ],
            'multipart' => $multipart
         ]);
         return json_decode($response->getBody(),true);
      } catch (\GuzzleHttp\Exception\RequestException $e) {
         if($e->hasResponse()) {
            $response = $e->getResponse();
            return $response->getBody();
         }
      }
   }
}
