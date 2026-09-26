<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\Services\InternalService;
use app\modules\v1\models\InfoStokBarang;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

class InfStokBarangController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoStokBarang';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new InfoStokBarang;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = $end = date('Y-m-d');

        if(isset($_GET['advanced-filter']['periodestok_nama'])) {
            $explode = explode(" - ", $_GET['advanced-filter']['periodestok_nama']);
            if(count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));
            }
            unset($_GET['advanced-filter']['periodestok_nama']); // Unset Advanced Filter  date range
            $between = true;
        }

        /**
         * End Special Condition date range
        **/
        
        // return [$start, $end];

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetInstalasi()
    {
        return Instalasi::find()->all();
    }

    public function actionExportExcel()
    {
        $model = new InfoStokBarang;
        $query = $model::find(true);

        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::find()->where([
            'ruangan_id' => $ruangan_id
        ])->one();
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['periodestok_nama'])) {
                $date = $_GET['advanced-filter']['periodestok_nama'];
                unset($_GET['advanced-filter']['periodestok_nama']); // Unset Advanced Filter  date range
                $between = true;
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $data_baru = [];
        $namaInstalasi = $namaRuangan = null;
        foreach ($data as $key => $value) {
            $namaRuangan = $value['ruangan_nama'];
            $namaInstalasi = $value['instalasi_nama'];
            $data_baru[] = [
                'Nama ruangan' => $value['ruangan_nama'],
                'Nama barang' => $value['barang_nama'],
                'Stok dipesan' => $value['qty_dipesan'],
                'Stok tersedia' => $value['qty_tersedia'],
                'Stok' => $value['qty_stok'],
            ];
        }

        $header = [
            'Tanggal Unduh' => date('d-M-Y H:i:s'),
        ];

        $footer = [
            'title' => [
                0 => '',
                1 => '',
            ],
            'data' => [
                'Stok' => 'Diunduh Oleh :' . $ruangan->ruangan_nama,
            ]
        ];


        $filePath = DocoHelpers::exportExcel("Informasi Stok dan Ketersediaan Barang", $data_baru, $header, [],$footer,[],true);

        $filePath->save('php://output');
        die;
    }

    public function getDataLaporanExcel()
    {
        $model = new InfoStokBarang;
        $query = $model::find(true);

        $between = false;
        $start = $end = date('Y-m-d');

        if(isset($_GET['advanced-filter']['periodestok_nama'])) {
            $explode = explode(" - ", $_GET['advanced-filter']['periodestok_nama']);
            if(count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));
            }
            unset($_GET['advanced-filter']['periodestok_nama']); // Unset Advanced Filter  date range
            $between = true;
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $data = $this->getDataLaporanExcel();
        $limit = 20;
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$limit);

        (new InternalService)->sendTo([
            'Sirs' => [
                'InformasiStokBarangExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportInformasiStokBarang' => [
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
                'UploadInformasiStokBarang' => [
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

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Informasi Stok Barang.xlsx';

        DocoHelpers::downloadFileExcel($fileName);
    }
}
