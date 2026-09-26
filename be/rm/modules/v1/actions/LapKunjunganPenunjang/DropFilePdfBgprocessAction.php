<?php

namespace app\modules\v1\actions\LapKunjunganPenunjang;

use Yii;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadFormPdf;

class DropFilePdfBgprocessAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $model = new UploadFormPdf;
        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;
            $path = "uploads/" . $filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);
            $nameFile = $path . '/' . $model->file;
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
