<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Dede herdiana
 * @Date: 19 November 2021
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
use app\modules\v1\models\InfoBayarUangMukaDetailView;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;


class LapUangMukaPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoBayarUangMukaDetailView';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    protected $_title = "Laporan Pembayaran Uang Muka";

    public function actionIndex()
    {
        $query = $this->getData();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected function getData()
    {
        $model = new InfoBayarUangMukaDetailView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_uangmuka'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_uangmuka']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_uangmuka']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        $query->andWhere(['between', 'tgl_uangmuka', $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    private function getheader($model, $includeId = false){
        $header = array_keys($model->attributes);
        $column = [];
        //Set Header Custom => Override dari urutan header karena permintaan custom dari Product Owner
        $header[0] = 'no';
        $header[1] = 'tgl_uangmuka';
        $header[2] = 'no_uangmuka';
        $header[3] = 'no_pendaftaran';
        $header[4] = 'no_rekam_medik';
        $header[5] = 'nama_pasien';
        $header[6] = 'jumlah_uangmuka';
        $header[7] = 'jenis_transaksi';
        $header[8] = 'nama_bank';

        foreach($header as $value){
            $column_explode = (explode('_', $value) ? explode('_', $value) : $value);
            $visible = true;
            if(!$includeId){
                if (isset($column_explode[1]) && $column_explode[1] == 'id'){
                    $visible = false;
                }
                if (isset($column_explode[0]) && isset($column_explode[1]) && ($column_explode[0] == 'carabayar' && $column_explode[1] == 'nama' )){
                    $visible = false;
                }
                if (isset($column_explode[0]) && isset($column_explode[1]) && ($column_explode[0] == 'penjamin' && $column_explode[1] == 'nama' )){
                    $visible = false;
                }
                if (isset($column_explode[0]) && isset($column_explode[1]) && ($column_explode[0] == 'kelaspelayanan' && $column_explode[1] == 'nama' )){
                    $visible = false;
                }
            }
            $title = ucwords(str_replace('_',  ' ', $value));
            if(isset($title) && ($title == 'No Uangmuka' )){
                $title = "No Kwitansi";
            }
            if(isset($title) && ($title == 'Tgl Uangmuka' )){
                $title = "Tanggal Pembayaran";
            }
            if(isset($title) && ($title == 'Jumlah Uangmuka' )){
                $title = "Jumlah Uang Muka (Rp.)";
            }

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
            if (isset($get['advanced-filter']['tgl_uangmuka'])) {
                $tgl_uangmuka = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_uangmuka']);
                $start = !empty($tgl_uangmuka['startDate']) ?  date('d-M-Y', strtotime($tgl_uangmuka['startDate'])) : '';
                $end = !empty($tgl_uangmuka['endDate']) ?  date('d-M-Y', strtotime($tgl_uangmuka['endDate'])) : '';
            }   
        }
        $no_pendaftaran = isset($get['advanced-filter']['no_pendaftaran']) ? $get['advanced-filter']['no_pendaftaran']:'-';
        $no_rekam_medik = isset($get['advanced-filter']['no_rekam_medik']) ? $get['advanced-filter']['no_rekam_medik']:'-';
        $nama_pasien = isset($get['advanced-filter']['nama_pasien']) ? $get['advanced-filter']['nama_pasien']:'-';
        
        $headerExcel = [
            "Tanggal Batal" => $start . ' - ' . $end,
            "No Pendaftaran" => $no_pendaftaran,
            "No Rekam Medik" => $no_rekam_medik,
            "Nama Pasien" => $nama_pasien,
        ];
        
        $model = new InfoBayarUangMukaDetailView();
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
            'sendToUrl' => 'lap-uang-muka-pasien/drop-file',
            'getDataUrl' => 'lap-uang-muka-pasien/get-data-laporan',
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
                    'title' => 'Laporan Pembayaran Uang Muka',
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
            $rowNum = 1;
            foreach($data as $key => $value){
                $value['no'] = $rowNum;
                $value['tgl_uangmuka'] = !empty($value['tgl_uangmuka']) && !is_null($value['tgl_uangmuka']) ? date('d-M-Y', strtotime($value['tgl_uangmuka'])) : '-';
                $value['no_uangmuka'] = !empty($value['no_uangmuka']) && !is_null($value['no_uangmuka']) ? $value['no_uangmuka'] : '';
                $value['no_pendaftaran'] = !empty($value['no_pendaftaran']) && !is_null($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
                $value['no_rekam_medik'] = !empty($value['no_rekam_medik']) && !is_null($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
                $value['nama_pasien'] = !empty($value['nama_pasien']) && !is_null($value['nama_pasien']) ? $value['nama_pasien'] : '';
                $value['jumlah_uangmuka'] = !empty($value['jumlah_uangmuka']) && !is_null($value['jumlah_uangmuka']) ? $value['jumlah_uangmuka'] : 0;
                $value['nama_bank'] = !empty($value['nama_bank']) && !is_null($value['nama_bank']) ? $value['nama_bank'] : '';
                $value['jenis_transaksi'] = !empty($value['jenis_transaksi']) && !is_null($value['jenis_transaksi']) ? $value['jenis_transaksi'] : '';

                $result[$key] = $value;
                $rowNum++;
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
