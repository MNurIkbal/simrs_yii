<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\rabbitmq\RabbitBgProcess;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadForm;
use app\modules\v1\models\LaporanRekapitulasiPenjualanView;

use GuzzleHttp\Exception\RequestException;

class LapRekapitulasiPenjualanController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\LapRekapitulasiPenjualanView';

    public function actions() {
        return [
            'index'         => 'app\modules\v1\actions\LapRekapitulasiPenjualan\IndexAction',
            'export-excel'  => 'app\modules\v1\actions\LapRekapitulasiPenjualan\ExportExcelAction'
        ];
    }

    public function dateFilter($query, $request) {
        $advanced_filter = $request->get('advanced-filter');
        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:59');
        if (isset($advanced_filter) && isset($advanced_filter['tgl_pelayanan'])) {
            $explode = explode(" - ", $advanced_filter['tgl_pelayanan']);
            if (count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
        }
        $query->andWhere(['between', 'tgl_pelayanan', $start, $end]);
        $query->orderBy($request->get('order', ''));
    }

    public function actionExportExcelBgprocess() 
    {
        $request  = Yii::$app->request;
        $get  = $request->get();

        $randString = ArrayHelper::getValue($get, 'randString');

        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);

        $data = $this->actionGetObjectData();
        $countData = $totalPerPage = count($data);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $kodeObat = $namaObat = '-';

        if (isset($_GET['advanced-filter'])) {
            $filter = $request->get('advanced-filter');
            if (isset($_GET['advanced-filter']['tgl_pelayanan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pelayanan']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pelayanan']);
            }

            if (isset($_GET['advanced-filter']['kode_obat'])) {
                $kodeObat = $filter['kode_obat'];
            }

            if (isset($_GET['advanced-filter']['nama_obat'])) {
                $namaObat = $filter['nama_obat'];
            }
        }

        $periode = '' . date('j M Y', strtotime($start)) . ' - ' . date('j M Y', strtotime($end));

        $headerFilter = [
            'Tanggal' => $periode,
            'Kode Obat' => $kodeObat,
            'Nama Obat' => $namaObat
        ];
        
        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $get,
            'countData' => $countData, 
            'title' => 'Laporan Rekapitulasi Penjualan',
            'manual_excel' => false,
            'sendToUrl' => 'lap-rekapitulasi-penjualan/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('apotek'),
            'getDataUrl' => 'lap-rekapitulasi-penjualan/get-object-data',
            'headerFilter' => $headerFilter,
        ], 'laporan_rekapitulasi_penjualan');

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionGetObjectData()
    {
        $request = Yii::$app->request;
        $model = new LaporanRekapitulasiPenjualanView;
        $query = $model::find(true);
        $this->dateFilter($query, $request);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query->asArray()->all();
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
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }
}