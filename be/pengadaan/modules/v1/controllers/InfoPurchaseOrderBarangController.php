<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\UploadForm;
use app\modules\v1\models\LaporanPurchaseOrderOutstandingBarangView;
use Doco\Services\InternalService;

class InfoPurchaseOrderBarangController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfoPoView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-data-excel"] = ["GET"];
        $verbs["sync-export-excel"] = ["GET"];
        $verbs["drop-file"] = ["GET","POST"];
        $verbs["download-file"] = ["GET"];
        return $verbs;
    }

    public function getDataExcel($param)
    {       
        $model = new LaporanPurchaseOrderOutstandingBarangView;
        $dateFilter = 'tanggal_po';
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if (isset($param['advanced-filter'])) {
            $advancedFilter = $param['advanced-filter'];
            if(!empty($advancedFilter['tanggal_po'])) {
                $explode = explode(" - ", $advancedFilter['tanggal_po']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
        }
        $query->andWhere(['between', $dateFilter, $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataExcel($getData)->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPurchaseOrderBarangExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportPurchaseOrderBarang' => [
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
                'UploadLaporanPurchaseOrderBarangExcel' => [
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
        $fileName = $dir.'/laporan_purchase_order_outstanding_barang.xlsx';

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
            unlink($filename);
            die();
        }
    }

    public function actionValidasi($id, $type_po)
    {
        return $this->runAction('save', ['id' => $id, 'type_po' => $type_po]);
    }
}
