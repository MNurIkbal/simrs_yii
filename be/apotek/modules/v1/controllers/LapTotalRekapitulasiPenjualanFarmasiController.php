<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use GuzzleHttp\Exception\RequestException;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\models\LaporanRekapPenjualanFarmasiFn;

class LapTotalRekapitulasiPenjualanFarmasiController extends DocoActiveController {
    public $modelClass = '';

    public function actions() {
        return [
            'index'         => 'app\modules\v1\actions\LapTotalRekapitulasiPenjualanFarmasi\IndexAction',
            'export-excel'  => 'app\modules\v1\actions\LapTotalRekapitulasiPenjualanFarmasi\ExportExcelAction'
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
        
        $countData = $this->getDataLaporanExcel()->count();
        $fetchLimit = 50;
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanTotalRekapPenjualanFarmasi' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcelLaporanTotalRekapPenjualanFarmasi' => [
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
                'UploadExcelLaporanTotalRekapPenjualanFarmasi' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);
        
        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Total Rekapitulasi Penjualan Farmasi.xlsx';

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
            die();
        }
    }

    public function getDataLaporanExcel()
    {
        $request = Yii::$app->request;
        $advanced_filter = $request->get('advanced-filter');
        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:59');

        if(isset($advanced_filter) && isset($advanced_filter['tgl'])) {
            $explode = explode(" - ", $advanced_filter['tgl']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
            unset($_GET['advanced-filter']['tgl']);
        }

        $model = new LaporanRekapPenjualanFarmasiFn;
        $query = $model::getData($start, $end);

        if(isset($advanced_filter) && isset($advanced_filter['jenisobatalkes_nama'])){
            $jenisobatalkes_id = $advanced_filter['jenisobatalkes_nama'];
            $query->andWhere(['jenisobatalkes_id' => $jenisobatalkes_id]);
            unset($_GET['advanced-filter']['jenisobatalkes_nama']);
        }

        return DocoRestActiveFilter::advancedFilter($model, $query);
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
}
