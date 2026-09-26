<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\Services\InternalService;
use yii\web\UploadedFile;

// model

use app\modules\v1\models\LaporanPasienOperasi;
use app\modules\v1\models\LaporanOperasiView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\UploadForm;

class LaporanPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPasienOperasi';
    public $konfig_farmasi;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-display-antrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        $verbs["export-excel"] = ["POST", "GET"];
        $verbs["export-pdf"] = ["POST", "GET"];
        $verbs["export-excel-bgprocess"] = ["POST", "GET"];
        $verbs["get-data-laporan"] = ["POST", "GET"];
        $verbs["drop-file"] = ["POST", "GET"];
        $verbs["download-file"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionGetAttributes()
    {
        $ruangan = Ruangan::find()->select([
            'ruangan_id',
            'ruangan_nama'
        ])->where([
            'instalasi_id' => [1,2,3]
        ])->all();

        $penjamin = Penjamin::find()->select([
            'penjamin_id',
            'penjamin_nama'
        ])->all();
        
        return [
            'ruangan' => $ruangan,
            'penjamin' => $penjamin,
        ];
    }

    public function actionIndex()
    {
        try {
            $query = $this->getData();
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionExportExcel()
    {
        try {
            $query = $this->getData()->asArray()->all();
            if (!empty($query)) {
                $counter = 0;
                foreach ($query as $index => $value) {
                    if (isset($_GET['advanced-filter']['ruanganasal_id'])) {
                        $ruanganPerujuk = $_GET['advanced-filter']['ruanganasal_id'];
                        if ($ruanganPerujuk == $value->ruanganasal_id) {
                            $header['Ruangan Perujuk'] = $value->ruangan_nama;
                        }
                    }
                    if (isset($_GET['advanced-filter']['penjamin_id'])) {
                        $penjamin = $_GET['advanced-filter']['penjamin_id'];
                        if ($penjamin == $value->penjamin_id) {
                            $header['Nama Penjamin'] = $value->penjamin_nama;
                        }
                    }
                    $data[$counter]['Tanggal Operasi'] = $value->tgl_operasi;
                    $data[$counter]['No Pendaftaran'] = $value->no_pendaftaran;
                    $data[$counter]['Nama Pasien'] = $value->nama_pasien;
                    $data[$counter]['Ruangan Perujuk'] = $value->ruangan_nama;
                    $data[$counter]['Penjamin'] = $value->penjamin_nama;
                    $counter++;
                }
            }

            $filePath = DocoHelpers::exportExcel('Laporan Pasien Bedah Sentral', $data, $header, [],[],[],true);

            $filePath->save('php://output');
            die;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @controller actionExportPdf
     * @attribute #table_laporan# => Menampilkan Laporan Pasien Bedah Sentral 
     * @attribute #periode# => Periode laporan
     **/

    public function actionExportPdf()
    {
       
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $model = new LaporanOperasiView;
            $query = $model::find();
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $startString = $endString= date('d F Y');
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tanggal_operasi'])) {
                    $tanggal_operasi = DocoHelpers::parsingRangeDate($get['advanced-filter']['tanggal_operasi']);
                    $start = !empty($tanggal_operasi['startDate']) ?  $tanggal_operasi['startDate'] : '';
                    $end = !empty($tanggal_operasi['endDate']) ?  $tanggal_operasi['endDate'] : '';
                    $startString = date('d F Y', strtotime($start));
                    $endString = date('d F Y', strtotime($end));
                    $query->andWhere(['between', 'tanggal_operasi', $start, $end]);
                    unset($get['advanced-filter']['tanggal_operasi']);
                }   

            }


            $periode = $startString.' - '.$endString;
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $data = $query->asArray()->all();
            $print = new DocoPrint();
            $print->attributes = [
                '#table_laporan#' => $this->renderPartial('index',['data'=>$data]),
                '#periode#' => $periode
            ];
            $print->Output();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetHeaderLaporanOperasi(){
        $model = new LaporanOperasiView();
        return $data['header'] = $this->getheader($model, false);
    }

    private function getheader($model, $includeId = false){
        $header = array_keys($model->attributes);
        $column = [];
        
        foreach($header as $value){
            $column_explode = (explode('_', $value) ? explode('_', $value) : $value);
            $visible = true;
            if(!$includeId){
                $visible = (isset($column_explode[1]) && $column_explode[1] == 'id') ? false : true;
            }
            $title = ucwords(str_replace('_',  ' ', $value));

            $column[] = [
                'title' => $title,
                'data' => $value,
                'searchable' => false,
                'visible' => $visible,
            ];
        }
        return $column;
    }

    public function actionExportExcelBgprocess() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $headerExcel = [];
        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);
        /** set header excel */

        
        $start = date('d-M-Y');
        $end = date('d-M-Y');
        
        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tanggal_operasi'])) {
                $tanggal_operasi = DocoHelpers::parsingRangeDate($get['advanced-filter']['tanggal_operasi']);
                $start = !empty($tanggal_operasi['startDate']) ?  date('d-M-Y', strtotime($tanggal_operasi['startDate'])) : '';
                $end = !empty($tanggal_operasi['endDate']) ?  date('d-M-Y', strtotime($tanggal_operasi['endDate'])) : '';
            }   
        }
        
        $headerExcel = [
            "Tanggal Operasi" => $start . ' - ' . $end
        ];
        
        $model = new LaporanOperasiView();
        $header = $this->getheader($model, false);
        $data = $this->getData();
        $countData = count($data->asArray()->all());
        $totalPerPage = count($data);
        $options = [
            "skipIncrement" => true,
            "customHeader" => [],
        ];
        
        $uri_bedah = Yii::$app->docoRest->getBaseUri('bedahsentral');
        $params = [
            'sendToUrl' => 'laporan-pasien/drop-file',
            'getDataUrl' => 'laporan-pasien/get-data-laporan',
            'base_uri' => $uri_bedah,
        ];

        (new InternalService)->sendTo([
            'Sirs' => [
                'DataExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $get,
                    'params' => $params
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $get,
                    'title' => 'Laporan Pasien Bedah Sentral',
                    'headerExcel' => $headerExcel,
                    'footer' => [],
                    'options' => $options,
                    'header' => $header,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'params' => $params
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    private function getData(){
        $request = Yii::$app->request;
        $get = $request->get();
        $model = new LaporanOperasiView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tanggal_operasi'])) {
                $tanggal_operasi = DocoHelpers::parsingRangeDate($get['advanced-filter']['tanggal_operasi']);
                $start = !empty($tanggal_operasi['startDate']) ?  $tanggal_operasi['startDate'] : '';
                $end = !empty($tanggal_operasi['endDate']) ?  $tanggal_operasi['endDate'] : '';
                
                unset($get['advanced-filter']['tanggal_operasi']);
            }   

        }
        $query->andWhere(['between', 'tanggal_operasi', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionGetDataLaporan()
    {
        try {
            $data = $this->getData()->AsArray()->All();
            $result = [];
            foreach($data as $key => $value){
                foreach($value as $k => $r){
                    if (gettype($value[$k]) == 'boolean'){
                        $value[$k] = ($value[$k] == true ? 'Y' : '');
                    }
                }
                
                $value['tanggal_operasi'] = !empty($value['tanggal_operasi']) && !is_null($value['tanggal_operasi']) ? date('d-M-Y H:i:s', strtotime($value['tanggal_operasi'])) : '-';
                $value['tanggal_pendaftaran'] = !empty($value['tanggal_pendaftaran']) && !is_null($value['tanggal_pendaftaran']) ? date('d-M-Y', strtotime($value['tanggal_pendaftaran'])) : '-';
                $value['subtotal'] = !empty($value['subtotal']) ?  DocoHelpers::rupiahDisplay($value['subtotal']) : 0;
                $result[$key] = $value;
            }
            return $result;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
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
        $fileName = $dir.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

}