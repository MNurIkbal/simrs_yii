<?php
/**
 * * @author Budi <budi@sirs.co.id>
 * * @copyright by Sirs
 * */

namespace Integrasi\Service\Sirs;

use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;

class UploadLaporanBatalPemeriksaanLab extends \Integrasi\Contracts\DocoImplement
{
   public function execute()
   {
      $this->uploadFile();
      $dir = dirname(dirname(__DIR__));
      $rootPath = 'uploads';
      $ext = ($this->tipe == 1) ? '.xlsx' : '.pdf';
      $path = $dir.'/'.$rootPath.'/'.$this->unique_str.$ext;
      if(file_exists($path)) {
         unlink($path);
      }
      
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
         'service' => 'Sirs-UploadLaporanBatalPemeriksaanLab',
         'payload' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
         // 'response' => $response
      ]);
   }	

   private function setEndPoint()
   {
      $params = $this->params;
      $baseUri = ArrayHelper::getValue($params, 'base_uri');
      $header = [
         'Authorization' => $this->token,
         'user-agent' => 'cli',
         'X-Owner' => $this->xOwner,
      ];
      $client =  new Client([
         'base_uri' => $baseUri,
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
      $params = $this->params;
      $sendToUrl = ArrayHelper::getValue($params, 'sendToUrl');
      if($this->tipe == 1) {
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
         $response = $client->post($sendToUrl, [
            [
               'query' => [
                  'filePath' => $path
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
