<?php

namespace app\modules\v1\actions\LapKunjunganPenunjang;

use Yii;

class DownloadExcelBgprocessAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath . '/' . $no_request;
        $fileName = $dir . '/Laporan Kunjungan Penunjang.xlsx';
        if (file_exists($fileName)) {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            unlink($fileName);
            die();
        }
    }
}
