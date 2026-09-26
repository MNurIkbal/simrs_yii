<?php

namespace app\modules\v1\actions\InfoPurchaseOrder;

use Yii;
use yii\base\Action;

class DownloadZipAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.'.zip';
        if(headers_sent()) {
            return [
                'status' => 422,
                'message' => 'HTTP header already sent'
            ];
        }
        else {
            if(!is_file($file)) {
                header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
                return [
                    'status' => 404,
                    'message' => 'File not found'
                ];
            }
            elseif(!is_readable($file)) {
                header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
                return [
                    'status' => 403,
                    'message' => 'File not readable'
                ];
            }
            else {
                header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: Binary");
                header("Content-Length: ".filesize($file));
                header("Content-Disposition: attachment; filename=\"".basename($file)."\"");
                readfile($file);
                unlink($file);
                exit;
            }
        }
    }
}
