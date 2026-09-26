<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use yii\data\ArrayDataProvider;
use Doco\Services\InternalService;

// model
use app\modules\v1\models\InfoPasienRadView;
use app\modules\v1\models\InfoPasienRadDetailView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;
use app\modules\v1\models\PemeriksaanPasienRadiologiView;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\LaporanPasienRadiologiView;

use app\modules\v1\models\UploadForm;


use yii\helpers\ArrayHelper;


class LapPasienRadiologiController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienRadView';
    public $konfig_farmasi;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
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

    public function actionIndex() 
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoPasienRadView;
            $query = $model::find();
            $query->andWhere(['not',['status_penunjang' => DocoConstants::BTL_APPROVE]]);
            $query->orWhere(['status_penunjang' => null]);

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $startLahir = '';
            $endLahir = '';

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']);
                    $between = true;
                }

                if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_lahir']);
                    $between = true;
                }
            }

            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);

            if(!empty($startLahir) && !empty($endLahir) && $between){
                $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
            }
            /**
             * End Special Condition date range
             **/

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

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

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $model = new LaporanPasienRadiologiView;
            $query = $model::find();
            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $startLahir = '';
            $endLahir = '';
            $status = null;

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']);
                    $between = true;
                }

                if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_lahir']);
                    $between = true;
                }
            }
            $query->betweenTglMasuk($start, $end);

            if (!empty($startLahir) && !empty($endLahir) && $between) {
                $query->betweenTglLahir($startLahir, $endLahir);
            }
            /**
             * End Special Condition date range
             **/
            $query->andWhere(['status_batal' => false ]);
            $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
            $query->noInStatusPeriksa(DocoConstants::BTL_APPROVE);
            $query->noInStatusPeriksa(null);
            $query->andWhere(['tindakanpelayananasal_id' => null]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        }
    }

    public function actionGetOptions()
    {
        $caraBayar = CaraBayar::find()->where([
            'is_active' => true
        ])->all();

        $penjamin = Penjamin::find()->where([
            'is_active' => true
        ])->all();
        $rujukan = Ruangan::find()->where([
            'is_active' => true,
            'instalasi_id' => DocoConstants::$exceptPenunjang
        ])->all();
        $asalRujukan = AsalRujukan::find()->where([
            'is_active' => true,
            'is_rujukan' => true
        ])->orderBy('asalrujukan_id')->all();

        return  [
            'cara_bayar' => $caraBayar,
            'penjamin' => $penjamin,
            'rujukan' => $rujukan,
            'asalRujukan' => $asalRujukan,
        ];
    }

    protected $_title = "Laporan Pasien Radiologi";
    public function actionExportExcel()
    {
        $model = new InfoPasienRadView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        $startLahir = '';
        $endLahir = '';
        $header = [];

        $asalrujukan_id = '';
        // Directory Creation
        if(isset($_GET['advanced-filter'])) {
            $advancedFilter = $_GET['advanced-filter'];
            // return $advancedFilter;
            if(isset($advancedFilter['no_pendaftaran'])){
                $header['Nomor Pendaftaran'] = $advancedFilter['no_pendaftaran'];
            }
            if(isset($advancedFilter['no_rekam_medik'])){
                $header['Nomor Rekam Medis'] = $advancedFilter['no_rekam_medik'];
            }
            if(isset($advancedFilter['nama_pasien'])){
                $header['Nama Pasien'] = $advancedFilter['nama_pasien'];
            }
            if(isset($advancedFilter['no_masukpenunjang'])){
                $header['Nomor Radiologi'] = $advancedFilter['no_masukpenunjang'];
            }
            if(isset($advancedFilter['tipe_pasien'])){
                $header['Tipe Pasien'] = strtoupper($advancedFilter['tipe_pasien']);
                $_GET['advanced-filter']['tipe_pasien'] = DocoConstants::$asal_rujukan[$_GET['advanced-filter']['tipe_pasien']];
            }
            if(isset($advancedFilter['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    $header['Tanggal Pendaftaran'] = date('d-M-Y', strtotime($start)).' - '.date('d-M-Y', strtotime($end));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($advancedFilter['tanggal_lahir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                if (count($explode) == 2) {
                    $startLahir = date('Y-m-d', strtotime($explode[0]));
                    $endLahir = date('Y-m-d', strtotime($explode[1]));
                    $header['Tanggal Lahir'] = date('d-M-Y', strtotime($startLahir)).' - '.date('d-M-Y', strtotime($endLahir));
                }
                unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($advancedFilter['status_periksa'])) {
                $status_periksa = $advancedFilter['status_periksa'];
                $header['Status'] = isset(DocoConstants::$status_lab[$status_periksa]) 
                        ? DocoConstants::$status_lab[$status_periksa] : null;
                $query->andWhere(['status_periksa' => $status_periksa]);
            }
            if(isset($advancedFilter['dokter_penunjang'])) {
                $dokter_penunjang = $advancedFilter['dokter_penunjang'];
                $header['Dokter'] = $dokter_penunjang;
                $query->andWhere(['ILIKE', 'dokter_penunjang', $dokter_penunjang]);
            }
            if(isset($advancedFilter['carabayar_nama'])) {
                $carabayar_nama = $advancedFilter['carabayar_nama'];
                $header['Cara Bayar'] = $carabayar_nama;
                $query->andWhere(['ILIKE', 'carabayar_nama', $carabayar_nama]);
            }
            if(isset($advancedFilter['asalrujukan_id'])) {
                $asalrujukan_id = (int) $advancedFilter['asalrujukan_id'];
                $header['Asal Rujukan Nama / Instalasi'] = $asalrujukan_id;
                // $query->andWhere(['asalrujukan_id'=>$asalrujukan_id]);
                // unset($_GET['advanced-filter']['asalrujukan_id']);
            }
            if(isset($advancedFilter['ruanganasal_id'])) {
                $ruanganasal_id = (int) $advancedFilter['ruanganasal_id'];
                $header['Ruangan Asal'] = $ruanganasal_id;
                // $query->andWhere(['ruanganasal_id'=>$ruanganasal_id]);
                // unset($_GET['advanced-filter']['ruanganasal_id']);
            }
        }
        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        if(!empty($startLahir) && !empty($endLahir) && $between){
            $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $result = [];
        foreach ($dataProvider->getModels() as $key => $value) {
            // Data Selection
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pendaftaran')] = date('d M Y', strtotime($value['tglmasukpenunjang']));
            $newValue[\Yii::t('app', 'Nomor Pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'No Rekam Medis')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Tanggal Lahir')] = date('d M Y', strtotime($value['tanggal_lahir']));
            $newValue[\Yii::t('app', 'Dokter')] = $value['dokter_penunjang'];
            $newValue[\Yii::t("app", "Cara Bayar")] = $value['carabayar_nama'];
            $newValue[\Yii::t("app", "Penjamin")] = $value['penjamin_nama'];
            $newValue[\Yii::t("app", "No Radiologi")] = $value['no_masukpenunjang'];
            $newValue[\Yii::t("app", "Asal Rujukan")] = $value['asalrujukan_nama'];
            $newValue[\Yii::t("app", "Status")] = isset($value['status_periksa']) ? 
            DocoConstants::$status_lab[$value['status_periksa']] : '';
            if( isset($header['Asal Rujukan Nama / Instalasi']) ){
                $header['Asal Rujukan Nama / Instalasi'] = $value['

                +'];
            }
            if( isset($header['Ruangan Asal']) ){
                $header['Ruangan Asal'] = $value['ruangan_nama'];
            }
            $result[$key] = $newValue;
        }

        $footer = [];
        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;

        // $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
        //     "uploadPath" => "./uploads",
        // ));
        
        // return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    /**
    * @controller actionCetakPdf
    * @attribute #tabel_detail# => Untuk Menampilkan tabel detail pemesanan
    * @attribute #title# => Untuk Menampilkan title
    * @attribute #periode# => periode
    * @attribute #tanggal# => tanggal cetak
    **/
    public function actionCetakPdf()
    {
        try {
            $title = 'Laporan Pasien Radiologi';
            $query = InfoPasienRadView::find();
            $request = Yii::$app->request;
            // return $request->get();
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            $startLahir = '';
            $endLahir = '';

            if(isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if(isset($advancedFilter['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if(isset($advancedFilter['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if(isset($advancedFilter['status_periksa'])) {
                    $status_periksa = $advancedFilter['status_periksa'];
                    $query->andWhere(['status_periksa' => $status_periksa]);
                }
                if(isset($advancedFilter['dokter_penunjang'])) {
                    $dokter_penunjang = $advancedFilter['dokter_penunjang'];
                    $query->andWhere(['ILIKE', 'dokter_penunjang', $dokter_penunjang]);
                }
                if(isset($advancedFilter['carabayar_nama'])) {
                    $carabayar_nama = $advancedFilter['carabayar_nama'];
                    $query->andWhere(['ILIKE', 'carabayar_nama', $carabayar_nama]);
                }
                if(isset($advancedFilter['penjamin_nama'])) {
                    $penjamin_nama = $advancedFilter['penjamin_nama'];
                    $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
                }
                if(isset($advancedFilter['asalrujukan_nama'])) {
                    $asalrujukan_nama = $advancedFilter['asalrujukan_nama'];
                    $query->andWhere(['ILIKE', 'asalrujukan_nama', $asalrujukan_nama]);
                }
                // if(isset($advancedFilter['penjamin_nama'])) {
                //     $penjamin_nama = $advancedFilter['penjamin_nama'];
                //     $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
                // }
                // if(isset($advancedFilter['penjamin_nama'])) {
                //     $penjamin_nama = $advancedFilter['penjamin_nama'];
                //     $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
                // }
            }

            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            if(!empty($startLahir) && !empty($endLahir) && $between){
                $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
            }
            $query = DocoRestActiveFilter::advancedFilter(new InfoPasienRadView, $query);
            $model = $query->all();
            $periode = date('d-M-Y', strtotime($start)).' - '.date('d-M-Y', strtotime($end));
            if($model) {
                $print = new DocoPrint();
                $print->attributes = [
                    '#title#' => $title,
                    '#periode#' => $periode,
                    '#tanggal#' => date('d-M-Y'),
                    '#tabel_detail#' => $this->renderPartial('pdf', [
                        'query' => $model,
                        'periode' => $periode,
                        'tanggal' => date('d-M-Y')
                    ]),
                ];

                $print->Output();
            }     
        } catch (\Exception $e) {
             \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSyncPdf() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $filter = isset($getData['params']) ? $getData['params'] : [];
        
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        if (isset($filter['page'])) unset($filter['page']);
        if (isset($filter['per-page'])) unset($filter['per-page']);
        $fetchLimit = 20;
        $data = $this->getDataLaporan($getData)->asArray()->all();
        $countData = count($data);
        
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [ 
                'LaporanPasienRadiologi' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $filter
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakLaporanPasienRadiologi' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLaporanPasienRadiologi' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.'.pdf';
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($file);
            unlink($file);
            die();
        }
    }

    public function getDataLaporan()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $filter = isset($getData['params']) ? $getData['params'] : [];

        $model = new LaporanPasienRadiologiView;
        $query   = $model::find();
        $query->noInStatusPeriksa(DocoConstants::BTL_APPROVE);
        $query->noInStatusPeriksa(null);
        $query->andWhere([ 'status_batal' => false ]);
        $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $startLahir = '';
        $endLahir = '';
        if(isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($filter['advanced-filter']['tanggal_lahir'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tanggal_lahir']);
                if (count($explode) == 2) {
                    $startLahir = date('Y-m-d', strtotime($explode[0]));
                    $endLahir = date('Y-m-d', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($filter['advanced-filter']['status_periksa'])) {
                $status_periksa = $filter['advanced-filter']['status_periksa'];
                $query->andWhere(['status_periksa' => $status_periksa]);
            }
            if(isset($filter['advanced-filter']['dokter_penunjang'])) {
                $dokter_penunjang = $filter['advanced-filter']['dokter_penunjang'];
                $query->andWhere(['ILIKE', 'dokter_penunjang', $dokter_penunjang]);
            }
            if(isset($filter['advanced-filter']['carabayar_nama'])) {
                $carabayar_nama = $filter['advanced-filter']['carabayar_nama'];
                $query->andWhere(['ILIKE', 'carabayar_nama', $carabayar_nama]);
            }
            if(isset($filter['advanced-filter']['penjamin_nama'])) {
                $penjamin_nama = $filter['advanced-filter']['penjamin_nama'];
                $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
            }
            if(isset($filter['advanced-filter']['asalrujukan_nama'])) {
                $asalrujukan_nama = $filter['advanced-filter']['asalrujukan_nama'];
                $query->andWhere(['ILIKE', 'asalrujukan_nama', $asalrujukan_nama]);
            }
            if(isset($filter['advanced-filter']['no_rekam_medik'])) {
                $no_rekam_medik = $filter['advanced-filter']['no_rekam_medik'];
                $query->andWhere(['ILIKE', 'no_rekam_medik', $no_rekam_medik]);
            }
            if(isset($filter['advanced-filter']['nama_pasien'])) {
                $nama_pasien = $filter['advanced-filter']['nama_pasien'];
                $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien]);
            }
            
        }

        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        if(!empty($startLahir) && !empty($endLahir) && $between){
            $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
        }

        return DocoRestActiveFilter::advancedFilter($model, $query);    
    }

    public function actionSendFile()
    {
        $request = Yii::$app->request;
        $filePath = $request->get('filePath', null);
        $model = new UploadPayload;
        if($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            $path = 'uploads/'. $filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $nameFile = $path.'/'.$model->file;
            if($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'Upload File Berhasil'
                ];
            }
        }
    }

    public function actionDetailLaporan()
    {
        return Yii::$app->docoPlugin->execute('cetak_detail_invoice');
    }


    public function actionGetObjectData()
    {
        $request = Yii::$app->request;
        
        $model   = new LaporanPasienRadiologiView;
        $query   = $model::find();

        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        
        $startLahir = '';
        $endLahir = '';
        $between =  false;
        $title = 'Laporan Pasien Radiologi';
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['tanggal_lahir'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                if (count($explode) == 2) {
                    $startLahir = date('Y-m-d', strtotime($explode[0]));
                    $endLahir = date('Y-m-d', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(isset($_GET['advanced-filter']['status_periksa'])) {
                $status_periksa = $_GET['advanced-filter']['status_periksa'];
                $query->andWhere(['status_periksa' => $status_periksa]);
            }
            if(isset($_GET['advanced-filter']['dokter_penunjang'])) {
                $dokter_penunjang = $_GET['advanced-filter']['dokter_penunjang'];
                $query->andWhere(['ILIKE', 'dokter_penunjang', $dokter_penunjang]);
            }
            if(isset($_GET['advanced-filter']['carabayar_nama'])) {
                $carabayar_nama = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['ILIKE', 'carabayar_nama', $carabayar_nama]);
            }
            if(isset($_GET['advanced-filter']['penjamin_nama'])) {
                $penjamin_nama = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
            }
            if(isset($_GET['advanced-filter']['asalrujukan_nama'])) {
                $asalrujukan_nama = $_GET['advanced-filter']['asalrujukan_nama'];
                $query->andWhere(['ILIKE', 'asalrujukan_nama', $asalrujukan_nama]);
            }
            
        }
        
        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        if(!empty($startLahir) && !empty($endLahir) && $between){
            $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
        }

        $query->andWhere(['status_batal' => false ]);
        $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
        $query->noInStatusPeriksa(DocoConstants::BTL_APPROVE);
        $query->noInStatusPeriksa(null);
        $query->andWhere(['tindakanpelayananasal_id' => null]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $model = $query->all();
        $periode = date('d-M-Y', strtotime($start)).' - '.date('d-M-Y', strtotime($end));
        $attributes = [
            '#title#' => $title,
            '#periode#' => $periode,
            '#tanggal#' => date('d-M-Y'),
            '#tabel_detail#' => $this->renderPartial('pdf', [
                'query' => $model,
                'periode' => $periode,
                'tanggal' => date('d-M-Y')
            ]),
        ];
        return [
            'periode'=>$periode,
            'attributes'=>$attributes,

        ]; 
    }

    public function getDataLaporanExcel($params)
    {
        $request = Yii::$app->request;
        $model = new LaporanPasienRadiologiView;
        $query = $model::find();

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        $startLahir = '';
        $endLahir = '';

        if(isset($params['advanced-filter'])) {
            $advancedFilter = $params['advanced-filter'];
            if(!empty($advancedFilter['tglmasukpenunjang'])) {
                $explode = explode(" - ", $advancedFilter['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tglmasukpenunjang']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(!empty($advancedFilter['tanggal_lahir'])) {
                $explode = explode(" - ", $advancedFilter['tanggal_lahir']);
                if (count($explode) == 2) {
                    $startLahir = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $endLahir = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($advancedFilter['tanggal_lahir']); // Unset Advanced Filter  date range
                $between = true;
            }
            if(!empty($advancedFilter['no_pendaftaran'])) {
                $query->andWhere(['no_pendaftaran' => $advancedFilter['no_pendaftaran']]);
            }
            if(!empty($advancedFilter['no_rekam_medik'])) {
                $query->andWhere(['no_rekam_medik' => $advancedFilter['no_rekam_medik']]);
            }
        }

        $query->betweenTglMasuk($start, $end);

        if (!empty($startLahir) && !empty($endLahir) && $between) {
            $query->betweenTglLahir($startLahir, $endLahir);
        }

        $query->andWhere(['status_batal' => false ]);
        $query->andWhere(['not',['status_periksa' => (string) DocoConstants::BTL_PERIKSA_LAB]]);
        $query->noInStatusPeriksa(DocoConstants::BTL_APPROVE);
        $query->noInStatusPeriksa(null);
        $query->andWhere(['tindakanpelayananasal_id' => null]);

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $data = $this->getDataLaporanExcel($getData)->asArray()->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPasienRadiologiExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportPasienRadiologi' => [
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
                'UploadLaporanPasienRadiologiExcel' => [
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
        $fileName = $dir.'/Laporan Pasien Radiologi.xlsx';

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