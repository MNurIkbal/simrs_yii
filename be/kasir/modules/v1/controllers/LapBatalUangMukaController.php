<?php

namespace app\modules\v1\controllers;

/**
 * @Author: zn
 * @Date: 23 Dec 2021
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use app\modules\v1\models\InfoBatalUangMukaView;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;



class LapBatalUangMukaController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoBatalUangMukaView';

    public function verbs(){
        $verbs["export-excel-bgprocess"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    protected $_title = "Laporan Batal Uang Muka";

    public function actionIndex()
    {
        $query = $this->getData();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected function getData()
    {
        $request = Yii::$app->request;
        $model = new InfoBatalUangMukaView;
        $query = $model::find();
        $_GET = $request->get();

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        // $startPembayaran = date('Y-m-d 00:00:00');
        // $endPembayaran = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_batal'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_batal']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));                    
                }
                unset($_GET['advanced-filter']['tgl_batal']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['tgl_uangmuka'])) {
                $explodePembayaran = explode(" - ", $_GET['advanced-filter']['tgl_uangmuka']);
                if(count($explodePembayaran) == 2) {
                    $startPembayaran = date('Y-m-d 00:00:00', strtotime($explodePembayaran[0]));
                    $endPembayaran = date('Y-m-d 23:59:59', strtotime($explodePembayaran[1]));
                }
                unset($_GET['advanced-filter']['tgl_uangmuka']); // Unset Advanced Filter  date range
                $query->andWhere(['between', 'tgl_uangmuka', $startPembayaran, $endPembayaran]);
            }
            if(isset($_GET['advanced-filter']['nama_pasien'])) {
                $namaPasien = $_GET['advanced-filter']['nama_pasien'];
                $query->andWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($namaPasien)]);
                $query->orWhere(['ILIKE', 'no_rekam_medik', $namaPasien]);
                unset($_GET['advanced-filter']['nama_pasien']);
            }

        }
        $query->andWhere(['between', 'tgl_batal', $start, $end]); 
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    private function getheader($model, $includeId = false){
        $header = array_keys($model->attributes);
        $column = [];
        
        //Tukar field column untuk Tanggal Deposit Uang Muka
        $header[4] = isset($header[11]) ? $header[11]:'';
        $header[11] = isset($header[5]) ? $header[5]:'';
        $header[3] = isset($header[8]) ? $header[8]:'';
        $temp_column = isset($header[6]) ? $header[6]:'';
        $header[6] = isset($header[7]) ? $header[7]:'';
        $header[7] = $temp_column;
        unset($header[8]);
        
        foreach($header as $key =>$value){
            if($key == 0){
                $column[] = [
                    'title' => 'No',
                    'data' => 'no',
                    'searchable' => false,
                    'visible' => true,
                ];
            }
            else{
                $column_explode = (explode('_', $value) ? explode('_', $value) : $value);
                $visible = true;
                if(!$includeId){
                    if (isset($column_explode[1]) && $column_explode[1] == 'id'){
                        $visible = false;
                    }
                    if (isset($column_explode[0]) && isset($column_explode[1]) && ($column_explode[0] == 'no' && $column_explode[1] == 'pembayaran' )){
                        $visible = false;
                    }
                    if (isset($column_explode[0]) && isset($column_explode[1]) && ($column_explode[0] == 'tgl' && $column_explode[1] == 'pembayaran' )){
                        $visible = false;
                    }
                }
                $title = ucwords(str_replace('_',  ' ', $value));
                if(isset($title) && ($title == 'No Uangmuka' )){
                    $title = "No Pembayaran";
                }
                if(isset($title) && ($title == 'Jumlah Uangmuka' )){
                    $title = "Jumlah Uang Muka";
                }
                if(isset($title) && ($title == 'Tgl Batal' )){
                    $title = "Tanggal Batal";
                }
                if(isset($title) && ($title == 'Tgl Uangmuka' )){
                    $title = "Tanggal Pembayaran";
                }
                
    
                $column[] = [
                    'title' => $title,
                    'data' => $value,
                    'searchable' => false,
                    'visible' => $visible,
                ];
            } 
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
        $start_uangmuka = $end_uangmuka = '';
        $no_pembayaran = $no_pendaftaran = $nama_pasien_rm = '-';
        
        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tgl_batal'])) {
                $tgl_batal = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_batal']);
                $start = !empty($tgl_batal['startDate']) ?  date('d-M-Y', strtotime($tgl_batal['startDate'])) : '';
                $end = !empty($tgl_batal['endDate']) ?  date('d-M-Y', strtotime($tgl_batal['endDate'])) : '';
            }   
            if (isset($get['advanced-filter']['tgl_uangmuka'])) {
                $tgl_uangmuka = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_uangmuka']);
                $start_uangmuka = !empty($tgl_uangmuka['startDate']) ?  date('d-M-Y', strtotime($tgl_uangmuka['startDate'])) : '';
                $end_uangmuka = !empty($tgl_uangmuka['endDate']) ?  date('d-M-Y', strtotime($tgl_uangmuka['endDate'])) : '';
            }   
            if (isset($get['advanced-filter']['no_uangmuka'])) {
                $no_pembayaran = !empty($get['advanced-filter']['no_uangmuka']) ?  $get['advanced-filter']['no_uangmuka'] : '-';
            }   
            if (isset($get['advanced-filter']['no_pendaftaran'])) {
                $no_pendaftaran = !empty($get['advanced-filter']['no_pendaftaran']) ?  $get['advanced-filter']['no_pendaftaran'] : '-';
            }   
            if (isset($get['advanced-filter']['nama_pasien'])) {
                $nama_pasien_rm = !empty($get['advanced-filter']['nama_pasien']) ?  $get['advanced-filter']['nama_pasien'] : '-';
            }   
        }
        
        $headerExcel = [
            "Tanggal Batal" => $start . ' - ' . $end,
            "Tanggal Pembayaran" => $start_uangmuka . ' - ' . $end_uangmuka,
            "No Pembayaran" => $no_pembayaran,
            "No Pendaftaran" => $no_pendaftaran,
            "Nama Pasien / No RM" => $nama_pasien_rm,
        ];
        
        $model = new InfoBatalUangMukaView();
        $header = $this->getheader($model, false);
        $data = $this->getData();
        $countData = count($data->asArray()->all());
        $totalPerPage = count($data);
        $options = [
            "skipIncrement" => true,
            "customHeader" => [],
        ];
        
        $uri_kasir = Yii::$app->docoRest->getBaseUri('kasir');
        $params = [
            'sendToUrl' => 'lap-batal-uang-muka/drop-file',
            'getDataUrl' => 'lap-batal-uang-muka/get-data-laporan',
            'base_uri' => $uri_kasir,
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
                    'title' => 'Laporan Batal Uang Muka',
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

    public function actionGetDataLaporan()
    {
        try {
            $data = $this->getData()->AsArray()->All();
            $result = [];
            $counter = 1;
            foreach($data as $key => $value){
                $value['no'] = $counter;                
                $value['tgl_batal'] = isset($value['tgl_batal']) ? date("d-M-Y H:i:s", strtotime($value['tgl_batal'])):'-';
                $value['tgl_uangmuka'] = isset($value['tgl_uangmuka']) ? date("d-M-Y H:i:s", strtotime($value['tgl_uangmuka'])):'-';
                $result[$key] = $value;
                $counter++;
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
            
            $path = "uploads/";
            // if (!file_exists($path)) mkdir($path, 0755, true);
            // $path = $filePath;

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
        // $dir = $rootPath.'/'.$no_request;
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

}
