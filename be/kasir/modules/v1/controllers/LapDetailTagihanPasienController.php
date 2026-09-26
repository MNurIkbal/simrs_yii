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
use app\modules\v1\models\LaporanDetailKunjunganRsView;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;




class LapDetailTagihanPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanDetailKunjunganRsView';

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

    protected $_title = "Laporan Detail Tagihan Pasien";

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
        $model = new LaporanDetailKunjunganRsView;
        $query = $model::find();
        $_GET = $request->get();

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pembayaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pembayaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));                    
                }
                unset($_GET['advanced-filter']['tgl_pembayaran']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['tgl_keluar'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_keluar']);
                if(count($explode) == 2) {
                    $outStart = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $outEnd = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                $query->andWhere(['between', 'tgl_keluar', $outStart, $outEnd]);
                unset($_GET['advanced-filter']['tgl_keluar']);
            }
        }
        $query->andWhere(['between', 'tgl_pembayaran', $start, $end]); 
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    private function getheader($model, $includeId = false){
        $header = array_keys($model->attributes);
        $column = [];
        
        //Set Header Custom => Override dari urutan header karena permintaan custom dari Product Owner
        $header[1] = 'tgl_pembayaran';
        $header[2] = 'tgl_masuk';
        $header[3] = 'no_pembayaran';
        $header[4] = 'instalasi';
        $header[5] = 'no_pendaftaran';
        $header[6] = 'nama_pasien';
        $header[7] = 'cara_bayar';
        $header[8] = 'total_tagihan';
        $header[9] = 'total_dijamin';
        $header[10] = 'total_dibayar';

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
                }
                $title = ucwords(str_replace('_',  ' ', $value));
                if(isset($title) && ($title == 'Tgl Pembayaran' )){
                    $title = "Tanggal Pembayaran";
                }
                if(isset($title) && ($title == 'Tgl Masuk' )){
                    $title = "Tanggal Masuk - Keluar";
                }
                if(isset($title) && ($title == 'Instalasi' )){
                    $title = "Instalasi - Ruangan Akhir";
                }
                if(isset($title) && ($title == 'Cara Bayar' )){
                    $title = "Cara Bayar - Penjamin";
                }
                if(isset($title) && ($title == 'Total Tagihan' )){
                    $title = "Jumlah Tagihan";
                }
                if(isset($title) && ($title == 'Total Dijamin' )){
                    $title = "Jumlah Dibayar Penjamin";
                }
                if(isset($title) && ($title == 'Total Dibayar' )){
                    $title = "Jumlah Dibayar Pasien";
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
        $no_pembayaran = $no_pendaftaran = $nama_pasien_rm = $outStart = $outEnd = '-';
        
        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tgl_pembayaran'])) {
                $tgl_pembayaran = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_pembayaran']);
                $start = !empty($tgl_pembayaran['startDate']) ?  date('d-M-Y', strtotime($tgl_pembayaran['startDate'])) : '';
                $end = !empty($tgl_pembayaran['endDate']) ?  date('d-M-Y', strtotime($tgl_pembayaran['endDate'])) : '';
            }   
        }
        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tgl_keluar'])) {
                $tgl_keluar = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_keluar']);
                $outStart = !empty($tgl_keluar['startDate']) ?  date('d-M-Y', strtotime($tgl_keluar['startDate'])) : '';
                $outEnd = !empty($tgl_keluar['endDate']) ?  date('d-M-Y', strtotime($tgl_keluar['endDate'])) : '';
            }   
        }
        $no_pendaftaran = isset($get['advanced-filter']['no_pendaftaran']) ? $get['advanced-filter']['no_pendaftaran'] :'-';
        $instalasi = isset($get['advanced-filter']['instalasi']) ? $get['advanced-filter']['instalasi'] :'-';
        $poliklinik = isset($get['advanced-filter']['ruangan']) ? $get['advanced-filter']['ruangan'] :'-';
        $cara_bayar = isset($get['advanced-filter']['cara_bayar']) ? $get['advanced-filter']['cara_bayar'] :'-';
        $penjamin = isset($get['advanced-filter']['penjamin']) ? $get['advanced-filter']['penjamin'] :'-';
        
        $headerExcel = [
            "Tanggal Pembayaran" => $start . ' - ' . $end,
            "Tanggal Pulang" => $outStart . ' - ' . $outEnd,
            "No Pendaftaran" => $no_pendaftaran,
            "Instalasi" => $instalasi,
            "Poliklinik" => $poliklinik,
            "Cara Bayar" => $cara_bayar,
            "Penjamin" => $penjamin,
        ];
        
        $model = new LaporanDetailKunjunganRsView();
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
            'sendToUrl' => 'lap-detail-tagihan-pasien/drop-file',
            'getDataUrl' => 'lap-detail-tagihan-pasien/get-data-laporan',
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
                    'title' => 'Laporan Detail Tagihan Pasien',
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
                $tanggal_masuk = isset($value['tgl_masuk']) ? date("d-M-Y", strtotime($value['tgl_masuk'])):'';
                $tanggal_keluar = isset($value['tgl_keluar']) ? date("d-M-Y", strtotime($value['tgl_keluar'])):'';
                $value['tgl_masuk'] = $tanggal_masuk. ' - ' .$tanggal_keluar;
                $instalasi_val = isset($value['instalasi']) ? $value['instalasi'] : '';
                $ruangan_val = isset($value['ruangan']) ? $value['ruangan'] : '';
                $value['instalasi'] = $instalasi_val. ' - ' .$ruangan_val;
                $nama_pasien = isset($value['nama_pasien']) ? $value['nama_pasien'] : '';
                $no_rekam_medik = isset($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
                $value['nama_pasien'] = $nama_pasien . ' - ' . $no_rekam_medik;
                $cara_bayar_val =  isset($value['cara_bayar']) ? $value['cara_bayar'] : '';
                $penjamin_val =  isset($value['penjamin']) ? $value['penjamin'] : '';
                $value['cara_bayar'] = $cara_bayar_val. ' - ' .$penjamin_val;

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
    
    public function actionFilters()
    {
        $type = Yii::$app->request->get('type', null);
        $payload = Yii::$app->request->get('payload', []);
        $page = isset($payload['page']) ? $payload['page'] : 1;
        $limit = isset($payload['limit']) ? $payload['limit'] : DocoConstants::LIMIT_INFINITY_SCROLL;
        $term = isset($payload['term']) ? $payload['term'] : null;
        $carabayarId = isset($payload['carabayar_id']) ? $payload['carabayar_id'] : null;
        $result = [];
        if($type == 'carabayar') {
            $result = CaraBayar::find()
                ->select(['carabayar_nama AS id', 'carabayar_nama AS text'])
                ->where(['is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
            }
        }
        elseif($type == 'penjamin') {
            $result = Penjamin::find()
                    ->select(['penjamin_nama AS id', 'penjamin_nama AS text'])
                    ->where(['is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
            }
        }
        elseif($type == 'ruangan') {
            $result = Ruangan::find()
                    ->select(['ruangan_nama AS id', 'ruangan_nama AS text'])
                    ->where(['is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
            }
        }
        else {
            $result = Instalasi::find()
                ->select(['instalasi_nama AS id', 'instalasi_nama AS text'])
                ->where(['is_active' => true]);

            if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(instalasi_nama)', strtolower($term)]);
            }
        }
        
        if(!empty($result)) {
            $result = $result->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
        }

        return $result;
    }



}
