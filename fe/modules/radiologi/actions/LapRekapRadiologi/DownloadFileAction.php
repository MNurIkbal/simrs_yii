<?php

namespace Doco\radiologi\actions\LapRekapRadiologi;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\Traits\ControllerHelperTrait;

class DownloadFileAction extends Action
{
   use ControllerHelperTrait;
   public function run()
   {
      $request = Yii::$app->request;
      $date = date('dmY');
      $filename = $request->get('fileName', null);
      $tipe = $request->get('tipe', 1);
      $ext = ($tipe == 1) ? '.xlsx' : '.pdf';
      $fileDownloads = $this->controller->_title.' '.$date.$ext;
      $path = Yii::getAlias("@download").'/'.$fileDownloads;
      $endPoint = $this->controller->_endpoint;
      Yii::$app->docoRest->radiologi->get($endPoint . 'download-file', [
         'save_to' => $path,
         'query' => [
            'filename' => $filename,
            'tipe' => $tipe
         ],
      ]);
      return DocoHelpers::downloadFile($path,true);
   }
}
