<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JenisObatAlkes;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;

class LapPemakaianBmhpRuanganController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\LaporanPemakaianObatRuanganView';

    public function actions() {
        return [
            'index' => 'app\modules\v1\actions\LapPemakaianBmhpRuangan\IndexAction',
            'export-excel' => 'app\modules\v1\actions\LapPemakaianBmhpRuangan\ExportExcelAction',
        ];
    }

    public function actionListDataFilter()
    {
        $ruangan = ArrayHelper::map(Ruangan::find()->select(['ruangan_id', 'ruangan_nama'])->orderBy(["ruangan_nama" => SORT_ASC])->all(), 'ruangan_nama', 'ruangan_nama');
        $jenisObat = ArrayHelper::map(JenisObatAlkes::find()->orderBy(['jenisobatalkes_nama' => SORT_ASC])->select(['jenisobatalkes_id','jenisobatalkes_nama'])->all(),'jenisobatalkes_nama','jenisobatalkes_nama');
        $data = ['ruangan' => $ruangan, 'jenisObat' => $jenisObat];

        return $this->responseJson(200, 'success', $data);
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $listData = $this->runAction('index');
        $response = ArrayHelper::getValue($listData, 'response');
        
        $meta = ArrayHelper::getValue($response, '_meta');
        $countData = ArrayHelper::getValue($meta, 'totalCount');
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ArrayHelper::getValue($meta, 'perPage');
        $pageCount = ArrayHelper::getValue($meta, 'pageCount');

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPemakaianBmhpRuangan' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'pageCount' => $pageCount,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportPemakaianBmhp' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'pageCount' => $pageCount,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadLaporanPemakaianBmhpRuangan' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'pageCount' => $pageCount,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
            'pageCount' => $pageCount,
        ];
    }

    public function actionDropFile()
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
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Pemakaian Bmhp Ruangan.xlsx';
        
        if (file_exists($fileName)) 
        {
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
