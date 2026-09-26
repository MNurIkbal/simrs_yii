<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\Supplier;
use Doco\components\DocoActiveController;
use Doco\Services\InternalService;
use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\UploadForm;
use Doco\components\DocoHelpers;
use yii\web\UploadedFile;

class LapPenerimaanObatAlkesController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-data"] = ["GET"];
        $verbs["supplier"] = ["GET"];
        $verbs["export-excel"] = ["GET", "POST"];
        $verbs["sync-export-excel"] = ["GET"];
        $verbs["send-file"] = ["GET","POST"];
        $verbs["download-file"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        return [
            'get-data' => 'app\modules\v1\actions\LapPenerimaanObatAlkes\GetDataAction',
            'supplier' => 'app\modules\v1\actions\LapPenerimaanObatAlkes\SupplierAction',
            'export-excel' => 'app\modules\v1\actions\LapPenerimaanObatAlkes\ExportExcelAction'
        ];
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $fetchLimit = 50;
        $data = ArrayHelper::getValue($this->runAction('get-data'), 'response', []);
        $totalCount = ArrayHelper::getValue($data, '_meta', []);
        $countData = ArrayHelper::getValue($totalCount, 'totalCount', 0);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'LapPenerimaaanObatAlkesExport' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'totalPerPage' => $totalPerPage,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakLapPenerimaaanObatAlkesExport' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);
        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLapPenerimaaanObatAlkesExport' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                ]
            ]
        ], true);
        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionSendFile()
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

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('filename', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Penerimaan Obat Alkes.xlsx';

        DocoHelpers::downloadFileExcel($fileName, $dir);
    }
}
