<?php

namespace app\modules\v1\actions\LapRekapRadiologi;

use Yii;
use yii\base\Action;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

class DropFileAction extends Action 
{
   public function run() 
   {
      $request = Yii::$app->request;
      $model = new UploadForm;
      $filePath = $request->get('filePath', null);
      if ($request->isPost) 
      {
         $files = UploadedFile::getInstanceByName('file');
         $fileName = $files->getBaseName();
         $ext = $files->getExtension();
         $model->file = $fileName.'.'.$ext;
         
         $path = "uploads/".$filePath;
         if (!file_exists($path)) mkdir($path, 0755, true);

         $nameFile = $path .'/'. $model->file;
         if ($files->saveAs($nameFile)) {
            return [
               'path' => $path,
               'message' => 'upload file berhasil!'
            ];
         }
      }
      return [
         'status' => 422,
         'message' => 'upload file gagal!'
      ];
   }
}
