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
use app\modules\v1\models\LapRekapTagihanPasienView;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Ruangan;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;



class LapRekapTagihanPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LapRekapTagihanPasienView';

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

    protected $_title = "Laporan Rekapitulasi Tagihan Pasien";

    public function actionIndex()
    {
        $query = $this->getData();
        return $query;
    }

    protected function getData()
    {
        $request = Yii::$app->request;
        $model = new LapRekapTagihanPasienView;
        $query = $model::find();
        $_GET = $request->get();
        
        $between = false;
        $year = date('Y');
        $komponentQuery = $tempQuery = $result = [];
        $start_month = $end_month = null;

        if(!empty($_GET['tanggal_mulai'])) {
            $explode = explode("-", $_GET['tanggal_mulai']);
            if(count($explode) == 2) {
                $year = isset($explode[0]) ? (int)$explode[0]: $year;
                $start_month = isset($explode[1]) ? (int)$explode[1]: null;                    
            }
            unset($_GET['tanggal_mulai']); 
        }
        if(!empty($_GET['tanggal_akhir'])) {
            $explode = explode("-", $_GET['tanggal_akhir']);
            if(count($explode) == 2) {
                $year = isset($explode[0]) ? (int)$explode[0]: $year;
                $end_month = isset($explode[1]) ? (int)$explode[1]: null;                   
            }
            unset($_GET['tanggal_akhir']); 
        }
        $filters = "tahun = '".$year."'";
        
        if(!empty($start_month) && !empty($end_month)){
            $filters = $filters ." AND ( ";
            for ($m=$start_month; $m<=$end_month; $m++) {
                $month_filter = date('F', mktime(0,0,0,$m, 1, date('Y')));
                $filters .= " bulan ILIKE '".$month_filter."%' ";
                if($m != $end_month){
                    $filters .= " OR ";
                }
            }
            $filters = $filters . " ) ";
        };
        if(!empty($_GET['penjamin'])) {
            $penjamin = $_GET['penjamin'];
            $filters .= " AND penjamin = '". $penjamin ."'";
            unset($_GET['penjamin']); 
        };
        if(!empty($_GET['ruangan'])) {
            $ruangan = $_GET['ruangan'];
            $filters .= " AND ruangan = '". $ruangan ."'";
            unset($_GET['ruangan']); 
        };
        if(!empty($_GET['unit'])) {
            $unit = $_GET['unit'];
            $filters .= " AND unit_pelayanan = '". $unit ."'";
            unset($_GET['unit']); 
        };

        $komponentQuery = Yii::$app->db->createCommand("
        SELECT
          x.tahun, 
          x.bulan,
          x.penjamin,
          x.ruangan,
        	x.unit_pelayanan,
          sum(x.kunjungan_baru) as kunjungan_baru,
          sum(x.kunjungan_lama) as kunjungan_lama,
          sum(x.total) as total
        FROM
        (SELECT
        tahun,
        bulan,
        penjamin,
        ruangan,
        unit_pelayanan,
        CASE
          when kunjungan='Kunjungan Baru' Then count(kunjungan)
          else 0
          end as kunjungan_baru,
        CASE
          when kunjungan='Kunjungan Lama' Then count(kunjungan)
          else 0
          end as kunjungan_lama,  
        sum(total) as total
         from laporanrekapkunjuganrs_v
        WHERE {$filters}
        GROUP BY tahun,
        bulan,
        penjamin,
        ruangan,
        unit_pelayanan,
        kunjungan) x
        GROUP BY x.tahun, x.bulan, x.penjamin, x.ruangan, x.unit_pelayanan
        ORDER BY unit_pelayanan, penjamin
    ")->queryAll();

    //Set Nama Bulan
    for ($m=1; $m<=12; $m++) {
        $month[] = date('F', mktime(0,0,0,$m, 1, date('Y')));
    }

    foreach ($komponentQuery as $key => $val){
    $dataQ = [];
        if (empty($tempQuery)){
                $dataQ['penjamin'] = $val['penjamin'];
                $dataQ['ruangan'] = $val['ruangan'];
                $dataQ['unit_pelayanan'] = $val['unit_pelayanan'];
                $dataQ['total'] = $val['total'];
                $dataQ['bulan'] = $val['bulan'];
                $dataQ['tahun'] = $val['tahun'];
                
                //Set Bulan untuk diisi dengan data kunjungan
                $bulan = isset($val['bulan']) ? str_replace(' ', '', strtolower($val['bulan'])) : '';
                foreach ($month as $val_month) {
                    if ($val_month == ucwords($bulan))
                    {
                        $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                        $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                        $dataQ[$kunjungan_lama] = isset($val['kunjungan_lama']) ? $val['kunjungan_lama'] :'';
                        $dataQ[$kunjungan_baru] = isset($val['kunjungan_baru']) ? $val['kunjungan_baru'] :'';
                    }
                }
        } else 
        {
            foreach($tempQuery as $keyQ => $valQ)
            {
                if ( isset($valQ['penjamin']) && isset($valQ['ruangan']) && isset($valQ['unit_pelayanan']) && ($valQ['penjamin'] == $val['penjamin']) && ($valQ['ruangan'] == $val['ruangan']) && ($valQ['unit_pelayanan'] == $val['unit_pelayanan']) )
                {
                    //Set Bulan untuk diisi dengan data kunjungan
                    $bulan = isset($val['bulan']) ? str_replace(' ', '', strtolower($val['bulan'])) : '';
                    foreach ($month as $val_month) {
                        if ($val_month == ucwords($bulan))
                        {
                            $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                            $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                            $tempQuery[$keyQ][$kunjungan_lama] = isset($val['kunjungan_lama']) ? $val['kunjungan_lama'] :'';
                            $tempQuery[$keyQ][$kunjungan_baru] = isset($val['kunjungan_baru']) ? $val['kunjungan_baru'] :'';
                            $tempQuery[$keyQ]['total'] += $val['total'];
                            $tempQuery[$keyQ]['total'] += $val['total'];
                        }
                    }
                    unset($dataQ);
                    break;
                }
                    
                    if ( ($valQ['penjamin'] != $val['penjamin']) || ($valQ['ruangan'] != $val['ruangan']) || ($valQ['unit_pelayanan'] != $val['unit_pelayanan']) ){
                        $dataQ['penjamin'] = $val['penjamin'];
                        $dataQ['ruangan'] = $val['ruangan'];
                        $dataQ['unit_pelayanan'] = $val['unit_pelayanan'];
                        $dataQ['total'] = $val['total'];
                        $dataQ['bulan'] = $val['bulan'];
                        $dataQ['tahun'] = $val['tahun'];
                        
                        //Set Bulan untuk diisi dengan data kunjungan
                        $bulan = isset($val['bulan']) ? str_replace(' ', '', strtolower($val['bulan'])) : '';
                        foreach ($month as $val_month) {
                            if ($val_month == ucwords($bulan))
                            {
                                $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                                $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                                $dataQ[$kunjungan_lama] = isset($val['kunjungan_lama']) ? $val['kunjungan_lama'] :'';
                                $dataQ[$kunjungan_baru] = isset($val['kunjungan_baru']) ? $val['kunjungan_baru'] :'';
                            }
                        }                        
                    }
            }
        }
        if (!empty($dataQ)){
            $tempQuery[]= $dataQ;
        }
    }
        return $tempQuery;
    }

    private function getheader(){
        $column = [];

        $column = [
        [
            'title' => 'No',
            'data' => 'no',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Unit Pelayanan',
            'data' => 'unit_pelayanan',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Penjamin',
            'data' => 'penjamin',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Ruangan',
            'data' => 'ruangan',
            'searchable' => false,
            'visible' => true,
        ],
        ];

        for ($m=1; $m<=12; $m++) {
            $jd=gregoriantojd($m,1,date('Y'));
            $_month = jdmonthname($jd,0);
            $val_month = date('F', mktime(0,0,0,$m, 1, date('Y')));
            $column_months_lama = 
            [
                'title' => $_month.' LAMA',
                'data' => 'kunjungan_lama-'.$val_month,
                'searchable' => false,
                'visible' => true,
            ];
            array_push($column,$column_months_lama);
            $column_months_baru = 
            [
                'title' => $_month.' BARU',
                'data' => 'kunjungan_baru-'.$val_month,
                'searchable' => false,
                'visible' => true,
            ];
            array_push($column,$column_months_baru);
        }
        $column_total = 
        [
            'title' => 'Total Biaya',
            'data' => 'total',
            'searchable' => false,
            'visible' => true,
        ];
        array_push($column,$column_total);
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
        $year = date('Y');
        $start_month = date('F', mktime(0,0,0,1, 1, $year));
        $end_month = date('F', mktime(0,0,0,(int)date('m'), 1, $year));
        $penjamin = $ruangan = $unit = '';
        
        if(isset($get['tanggal_mulai'])) {
            $explode = explode("-", $get['tanggal_mulai']);
            if(count($explode) == 2) {
                if(isset($explode[0])){
                    $year = !empty((int)$explode[0]) ? (int)$explode[0] : '';
                }
                if(isset($explode[1])){
                    $start_month = !empty(date('F', mktime(0,0,0,(int)$explode[1], 1, date('Y')))) ? date('F', mktime(0,0,0,(int)$explode[1], 1, date('Y'))) : '';                  
                }
            }
        }
        if(isset($get['tanggal_akhir'])) {
            $explode = explode("-", $get['tanggal_akhir']);
            if(count($explode) == 2) {
                if(isset($explode[0])){
                    $year = !empty((int)$explode[0]) ? (int)$explode[0] : '';
                }
                if(isset($explode[1])){
                    $end_month = !empty(date('F', mktime(0,0,0,(int)$explode[1], 1, date('Y')))) ? date('F', mktime(0,0,0,(int)$explode[1], 1, date('Y'))) : '';                    
                }   
            }
        }
        if(isset($get['penjamin'])) {
            $penjamin = $get['penjamin'];
        };
        if(isset($get['ruangan'])) {
            $ruangan = $get['ruangan'];
        };
        if(isset($get['unit'])) {
            $unit = $get['unit'];
        };
        
        $headerExcel = [
            "Periode" => $start_month . ' - ' . $end_month .' '.$year,
            "Penjamin" => $penjamin,
            "Ruangan" => $ruangan,
            "Unit Pelayanan" => $unit,
        ];

        
        $model = new LapRekapTagihanPasienView();
        $header = $this->getheader();
        $data = $this->getData();
        $countData = count($data);
        $totalPerPage = count($data);
        $options = [
            "skipIncrement" => true,
            "customHeader" => [],
        ];
        
        $uri_kasir = Yii::$app->docoRest->getBaseUri('kasir');
        $params = [
            'sendToUrl' => 'lap-rekap-tagihan-pasien/drop-file',
            'getDataUrl' => 'lap-rekap-tagihan-pasien/get-data-laporan',
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
                    'title' => 'Laporan Rekapitulasi Tagihan Pasien',
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
            $response = $this->getData();
            // return $response;
            $result = [];
            $no = 0;
            $data = [];

            for ($m=1; $m<=12; $m++) {
                $month[] = date('F', mktime(0,0,0,$m, 1, date('Y')));
            }
            $tmpPenjamin = ''; $valKunjungan = []; $valTotal = 0;
            $tmpPelayanan = ''; $valPelayanan = []; $valPelayananTotal = 0;
            $valGrandMonths = []; $valGrandTotal = 0;

            foreach ($response as $key => $value) {
                $no++;
                $value['no'] = $no;
                $value['unit_pelayanan'] = isset($value['unit_pelayanan']) ? $value['unit_pelayanan'] : '-';
                $value['code_pelayanan'] = isset($value['unit_pelayanan']) ? $value['unit_pelayanan'] : '-';
                $value['penjamin'] = isset($value['penjamin']) ? $value['penjamin'] : '-';
                $value['ruangan'] = isset($value['ruangan']) ? $value['ruangan'] : '-';
                $value['bulan'] = isset($value['bulan']) ? $value['bulan'] : '-';
                $value['tahun'] = isset($value['tahun']) ? $value['tahun'] : '-';
                $value ['is_penjamin'] = FALSE;
                $value ['is_unit'] = FALSE;
                $value ['is_total'] = FALSE;
                $total = isset($value['total']) ? (float)$value['total'] : 0;
                $value['total'] = $total;
                $valTotal += (float)$total;
                $valPelayananTotal += (float)$total;

                //Set Kolom Bulan
                foreach ($month as $val_month) {
                    
                    $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                    $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                    $value[$kunjungan_lama] = isset($value[$kunjungan_lama]) ? $value[$kunjungan_lama]:'';
                    $value[$kunjungan_baru] = isset($value[$kunjungan_baru]) ? $value[$kunjungan_baru]:'';

                    //Set Perhitungan Per Bulan untuk Penjamin
                    $valKunjungan[$kunjungan_lama] =isset($valKunjungan[$kunjungan_lama]) ? $valKunjungan[$kunjungan_lama] : 0;
                    $valKunjungan[$kunjungan_baru] =isset($valKunjungan[$kunjungan_baru]) ? $valKunjungan[$kunjungan_baru] : 0;
                    $valKunjungan[$kunjungan_lama] += ($value[$kunjungan_lama] == '') ? 0:$value[$kunjungan_lama];
                    $valKunjungan[$kunjungan_baru] += ($value[$kunjungan_baru] == '') ? 0:$value[$kunjungan_baru];

                     //Set Perhitungan Per Bulan untuk Unit Pelayanan
                     $valPelayanan[$kunjungan_lama] =isset($valPelayanan[$kunjungan_lama]) ? $valPelayanan[$kunjungan_lama] : 0;
                     $valPelayanan[$kunjungan_baru] =isset($valPelayanan[$kunjungan_baru]) ? $valPelayanan[$kunjungan_baru] : 0;
                     $valPelayanan[$kunjungan_lama] += ($value[$kunjungan_lama] == '') ? 0:$value[$kunjungan_lama];
                     $valPelayanan[$kunjungan_baru] += ($value[$kunjungan_baru] == '') ? 0:$value[$kunjungan_baru];
                }
                //Grouping By Penjamin
                if ($tmpPenjamin == ''){
                    $tmpPenjamin =  $value['penjamin'];
                }
                $nextPenjamin = isset($response[$key+1]['penjamin']) ? $response[$key+1]['penjamin'] : null;
                $nextPelayanan = isset($response[$key+1]['unit_pelayanan']) ? $response[$key+1]['unit_pelayanan'] : '';
                if ( $no == count($response) || $nextPenjamin != $tmpPenjamin ){

                    $data[] = $value;
                    //Create New Row
                    $valuePenjamin ['no'] = '';
                    $valuePenjamin ['code_pelayanan'] = $value['unit_pelayanan'];
                    $valuePenjamin ['unit_pelayanan'] = 'SUB TOTAL '.$tmpPenjamin;
                    $valuePenjamin ['penjamin'] = '';
                    $valuePenjamin ['ruangan'] = '';
                    $valuePenjamin ['bulan'] = '';
                    $valuePenjamin ['tahun'] = '';
                    $valuePenjamin ['is_penjamin'] = TRUE;
                    $valuePenjamin ['is_unit'] = FALSE;
                    $valuePenjamin ['is_total'] = FALSE;
                    $valuePenjamin ['total'] = $valTotal;
                    
                    foreach ($month as $val_month) {
                        $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                        $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                        $valuePenjamin[$kunjungan_lama] = isset($valKunjungan[$kunjungan_lama]) ?  $valKunjungan[$kunjungan_lama] : 0;
                        $valuePenjamin[$kunjungan_baru] = isset($valKunjungan[$kunjungan_baru]) ? $valKunjungan[$kunjungan_baru] : 0;
                    }

                    // Kosongkan row akumulasi kunjungan
                    $tmpPenjamin = '';
                    $valKunjungan = [];
                    $valTotal = 0;

                    $data[]= $valuePenjamin;
                    if($no != count($response) && $nextPelayanan == $tmpPelayanan ){
                        continue;
                    }
                    
                }

                //Grouping By Unit Pelayanan
                if ($tmpPelayanan == ''){
                    $tmpPelayanan =  $value['unit_pelayanan'];
                }
                
                 if ($no == count($response) ||  $nextPelayanan != $tmpPelayanan ){

                        //Create New Row
                        $valuePelayanan ['no'] = '';
                        $valuePelayanan ['unit_pelayanan'] = 'TOTAL '.$tmpPelayanan;
                        $valuePelayanan ['penjamin'] = '';
                        $valuePelayanan ['ruangan'] = '';
                        $valuePelayanan ['bulan'] = '';
                        $valuePelayanan ['tahun'] = '';
                        $valuePelayanan ['total'] = $valPelayananTotal;
                        $valuePelayanan ['is_penjamin'] = FALSE;
                        $valuePelayanan ['is_unit'] = TRUE;
                        $valuePelayanan ['is_total'] = FALSE;
                        $valGrandTotal += $valPelayananTotal;
                        foreach ($month as $val_month) {
                            $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                            $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                            $valuePelayanan[$kunjungan_lama] = isset($valPelayanan[$kunjungan_lama]) ? $valPelayanan[$kunjungan_lama] : '';
                            $valuePelayanan[$kunjungan_baru] = isset($valPelayanan[$kunjungan_baru]) ? $valPelayanan[$kunjungan_baru] : '';

                            //Set Perhitungan Per Bulan untuk Grand Total
                            $valGrandMonths[$kunjungan_lama] =isset($valGrandMonths[$kunjungan_lama]) ? $valGrandMonths[$kunjungan_lama] : 0;
                            $valGrandMonths[$kunjungan_baru] =isset($valGrandMonths[$kunjungan_baru]) ? $valGrandMonths[$kunjungan_baru] : 0;
                            $valGrandMonths[$kunjungan_lama] += isset($valPelayanan[$kunjungan_lama]) ? $valPelayanan[$kunjungan_lama] :0;
                            $valGrandMonths[$kunjungan_baru] += isset($valPelayanan[$kunjungan_baru]) ? $valPelayanan[$kunjungan_baru] :0;
                        }

                        //Kosongkan row akumulasi kunjungan
                        $tmpPelayanan = '';
                        $valPelayanan = [];
                        $valPelayananTotal = 0;

                        $data[]= $valuePelayanan;
                        if($no != count($response) && $nextPelayanan == $tmpPelayanan){
                            $data[]= $value;
                        }
                        
                        continue;
                }
                
                $data[]= $value;
            }

            // Final Grand Total
            $value ['no'] = '';
            $value ['unit_pelayanan'] = 'GRAND TOTAL';
            $value ['penjamin'] = '';
            $value ['ruangan'] = '';
            $value ['bulan'] = '';
            $value ['tahun'] = '';
            $value ['is_penjamin'] = FALSE;
            $value ['is_unit'] = FALSE;
            $value ['is_total'] = TRUE;
            $value ['total'] = $valGrandTotal;
            foreach ($month as $val_month) {
                $kunjungan_lama = 'kunjungan_lama-'.$val_month;
                $kunjungan_baru = 'kunjungan_baru-'.$val_month;
                $value[$kunjungan_lama] = isset($valGrandMonths[$kunjungan_lama]) ? $valGrandMonths[$kunjungan_lama] : 0;
                $value[$kunjungan_baru] = isset($valGrandMonths[$kunjungan_baru]) ? $valGrandMonths[$kunjungan_baru] : 0;
            }
            $data[]= $value;

            return $data;
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

    public function actionGetPenjamin(){
        $result = Penjamin::find()
        ->select(['penjamin_id AS id', 'penjamin_nama AS text'])
        ->where(['is_active' => true])->asArray()
        ->all();

        return $result;
    }
    public function actionGetRuangan(){
        $result = Ruangan::find()
        ->select(['ruangan_id AS id', 'ruangan_nama AS text'])
        ->where(['is_active' => true])->asArray()
        ->all();

        return $result;
    }

}
