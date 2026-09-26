<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\UploadForm;
use Doco\components\DocoHelpers;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\LaporanStockInventoryFn;
use app\components\rabbitmq\LapStockInventoryBgProcess; 

class LapStockInventoryController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanStockInventoryFn';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-data-excel"] = ["GET"];
        $verbs["sync-export-excel"] = ["GET"];
        $verbs["drop-file"] = ["GET","POST"];
        $verbs["download-file"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        return [
            'get-list-data' => 'app\modules\v1\actions\LapStockInventory\GetListDataAction',
            'get-list-ruangan' => 'app\modules\v1\actions\LapStockInventory\GetListRuanganAction',
            'get-list-jenis-obat' => 'app\modules\v1\actions\LapStockInventory\GetListJenisObatAction',
        ];
    }

    public function filterQuery($advancedFilter, $query)
    {
        if(isset($advancedFilter['ruangan_nama'])){
            $query->where('ruangan_nama', $advancedFilter['ruangan_nama']);
        }

        return $query;
    }

    protected $_title = 'LAPORAN STOCK INVENTORY';
    
    public function actionExportExcel()
    {
        $date = date('Y-m-d');
        $model = new LaporanStockInventoryFn;
        $query = LaporanStockInventoryFn::getData($date);

        $ruangan_nama = $jenis = '';
        if (isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tanggal_inventory'])){
                $date = date('Y-m-d', strtotime($_GET['advanced-filter']['tanggal_inventory']));
                $query = $model::getData($date);
            }
            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $ruangan_nama = $_GET['advanced-filter']['ruangan_nama'];
            }

            if(isset($_GET['advanced-filter']['jenis_obat'])){
                $jenis = $_GET['advanced-filter']['jenis_obat'];
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model,$query);
        $result = $model->toExcel($query);

        $header = array(
            'Tanggal Inventory' => (@$date),
            Yii::t('app', $model->attributeLabels()['ruangan_nama']) => $ruangan_nama,
            'Jenis Obat Alkes' => $jenis
        );

        $options = [
            "titleStyle" => [
                "fontSize" => 11,
                "alignment" => "left"
            ],
            "customFormatCode" => [
                ['selectColumn' => 'J', 'formatCode' => 'number'],
                ['selectColumn' => 'K', 'formatCode' => 'number'],
                ['selectColumn' => 'L', 'formatCode' => 'number'],
                ['selectColumn' => 'M', 'formatCode' => 'number'],
                ['selectColumn' => 'N', 'formatCode' => 'number'],
                ['selectColumn' => 'O', 'formatCode' => 'number'],
                ['selectColumn' => 'P', 'formatCode' => 'number']
            ],
        ];

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, $options, [], [], true);
        $filePath->save('php://output');
        die;
    }

    public function getDataExcel($param)
    {
        $date = date('Y-m-d');
        $model = new LaporanStockInventoryFn;
        $query = LaporanStockInventoryFn::getData($date);

        if (isset($param['advanced-filter'])) {
            if(isset($param['advanced-filter']['tanggal_inventory'])){
                $date = date('Y-m-d', strtotime($param['advanced-filter']['tanggal_inventory']));
                $query = $model::getData($date);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model,$query);
        return $query;
    }
    
    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner  = $request->getHeaders()->get('X-Owner');
        $auth    = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $randString   = isset($getData['randString']) ? $getData['randString'] : null;

        (new LapStockInventoryBgProcess())->send([
            'unique_str'    => $randString,
            'filter'        => $getData,
            'sendToUrl'     => 'lap-stock-inventory/drop-file',
            'base_uri'      => Yii::$app->docoRest->getBaseUri('gudang'),
        ], 'excel_lap_stock_inventory', 'import_excel_lap_stock_inventory');

        return [
            'totalPerPage'   => 5,
            'messageProcess' => 'Memulai proses export laporan stock inventory',
            'unique_str'     => $randString,
            'countData'      => 0,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model   = new UploadForm;
        
        try {
            $filePath = $request->get('filePath', null);
            if ($request->isPost)
            {
                $files = UploadedFile::getInstanceByName('file');
                if ($files === null) {
                    return [
                        'status' => 400,
                        'message' => 'File tidak ditemukan pada request. Pastikan field upload bernama "file".'
                    ];
                }
                $fileName    = $files->getBaseName();
                $ext         = $files->getExtension();
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
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
            ];
        }
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }
}

?>