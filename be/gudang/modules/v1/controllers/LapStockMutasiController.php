<?php


namespace app\modules\v1\controllers;

use app\modules\v1\models\UploadForm;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\Services\InternalService;
use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\LaporanStockMutasiFn;
use app\modules\v1\models\Ruangan;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoSpout;
use yii\web\UploadedFile;

class LapStockMutasiController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\LaporanStockMutasiFn';
    protected $_title = "Laporan Summary Mutasi Stok";

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-data-laporan"] = ["GET"];
        $verbs["get-data"] = ["GET"];
        $verbs["sync-export-excel"] = ["GET"];
        $verbs["download-file"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        return [
            'filters' => 'app\modules\v1\actions\LapStockMutasi\FiltersAction',
            'get-list-data' => 'app\modules\v1\actions\LapStockMutasi\GetListDataAction',
            'get-list-ruangan' => 'app\modules\v1\actions\LapStockMutasi\GetListRuanganAction',
            'get-list-jenis-obat' => 'app\modules\v1\actions\LapStockMutasi\GetListJenisObatAction',
        ];
    }

     public function getData() {
        $request = Yii::$app->request;
        $get = $request->get();
        $range_tanggal = ArrayHelper::getValue($get,'advanced-filter.tanggal_inventory');
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        if(!is_null($range_tanggal)){
            $range_explode = explode(' - ', $range_tanggal);
            if (count($range_explode) == 2) {
                $start_date = date('Y-m-d',strtotime($range_explode[0]));
                $end_date = date('Y-m-d',strtotime($range_explode[1]));
            }
        } 
        $model = new LaporanStockMutasiFn(['extParam'=>[$start_date,$end_date]]);

        return $model;
    }

    public function getDataLaporan($params) {
        $model = self::getData();
        $query = $model::find();
        if (isset($params['advanced-filter'])) {
            $advancedFilter = $params['advanced-filter'];
            if(!empty($advancedFilter['tgl_adjusmen'])) {
                $explode = explode(" - ", $advancedFilter['tgl_adjusmen']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_adjusmen']);
                $query->andWhere(['between', 'tgl_adjusmen', $start, $end]);
            }
        }

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function dateFilter($query, $request)
    {
        $advancedFilter = $request->get('advanced-filter');
        $dateKey = ['tgl_adjusmen'];
        foreach ($advancedFilter as $key => $value) {
            if (in_array($key, $dateKey)) {
                $explode = explode(" - ", $advancedFilter[$key]);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                $query->andWhere(['between', $key, $start, $end]);
            }
        }
    }

    public function actionSyncExportExcel() {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $data = $this->getDataLaporan($getData)->asArray()->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanStockMutasiExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportStockMutasi' => [
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
                'UploadLaporanStockMutasiExcel' => [
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
        $fileName = $dir.'/laporan-stock-mutasi.xlsx';

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