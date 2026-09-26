<?php
namespace app\modules\v1\actions;

use Yii;
use yii\base\Action;
use Doco\components\DocoHelpers;

class DownloadExcelAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }
}