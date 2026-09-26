<?php


namespace app\modules\v1\controllers;

use app\modules\v1\models\UploadForm;
use Doco\components\DocoHelpers;
use Doco\Services\InternalService;
use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\LaporanStockInventoryBarangFn;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoSpout;
use yii\web\UploadedFile;

class LapStockInventoryBarangController extends DocoActiveController
{
    public $modelClass = '';
    public function actions()
    {
        return [
            'get-list-data' => 'app\modules\v1\actions\LapStockInventoryBarang\GetListDataAction',
            'get-list-jenis-barang' => 'app\modules\v1\actions\LapStockInventoryBarang\GetListJenisBarangAction',
            'get-list-instalasi' => 'app\modules\v1\actions\LapStockInventoryBarang\GetListInstalasiAction',
        ];
    }

    public function filterQuery($advancedFilter, $query)
    {
        if(isset($advancedFilter['ruanganid'])){
            $query->where('ruanganid', $advancedFilter['ruanganid']);
        }

        return $query;
    }

    protected $_title = 'LAPORAN STOCK INVENTORY BARANG';
    
    public function actionExportExcel()
    {
        $date = date('Y-m-d');
        $model = new LaporanStockInventoryBarangFn;
        $query = LaporanStockInventoryBarangFn::getData($date);

        $ruangan_nama = $jenis = '';
        if (isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tanggal_inventory'])){
                $date = date('Y-m-d', strtotime($_GET['advanced-filter']['tanggal_inventory']));
                $query = $model::getData($date);
            }
            if(isset($_GET['advanced-filter']['ruangan_nama'])){
                $ruangan_nama = $_GET['advanced-filter']['ruangan_nama'];
            }

            if(isset($_GET['advanced-filter']['kelompok_barang'])){
                $jenis = $_GET['advanced-filter']['kelompok_barang'];
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model,$query);
        $result = $model->toExcel($query);

        $header = array(
            'Tanggal Inventory' => (@$date),
            Yii::t('app', $model->attributeLabels()['ruangan_nama']) => $ruangan_nama,
            'Kelompok Barang' => $jenis

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
        $model = new LaporanStockInventoryBarangFn;
        $query = LaporanStockInventoryBarangFn::getData($date);

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
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataExcel($getData)->asArray()->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanStockInventoryBarangExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportStockInventoryBarang' => [
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
                'UploadLaporanStockInventoryBarangExcel' => [
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
        $fileName = $dir.'/laporan-stock-inventory.xlsx';

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
}

?>