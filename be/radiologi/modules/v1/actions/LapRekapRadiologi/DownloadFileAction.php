<?php

namespace app\modules\v1\actions\LapRekapRadiologi;

use Yii;
use yii\base\Action;

class DownloadFileAction extends Action 
{
   public function run() 
   {
      $request = Yii::$app->request;
      $filename = $request->get('filename', null);
      $tipe = $request->get('tipe', 1);
      $ext = ($tipe == 1) ? '.xlsx' : '.pdf';
      $rootPath = './uploads';
      $files = $rootPath.'/'.$filename . $ext;
      if(file_exists($files)) {
         header('Content-Description: File Transfer');
         if($tipe == 1) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         }
         else {
            header('Content-Type: application/pdf');
         }
         header("Content-Disposition: inline; filename=$files");
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate');
         header('Pragma: public');
         ob_clean();
         flush();
         readfile($files);
         unlink($files);
         die();
      }
   }
}
