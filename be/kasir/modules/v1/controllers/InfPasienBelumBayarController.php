<?php

namespace app\modules\v1\controllers;
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use app\modules\v1\models\PasienBelumBayar;
use app\modules\v1\cache\Cache;
use Doco\components\DocoConstansId;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\businessLogic\TagihanHelper;
use Doco\components\NoCountDataProvider;

class InfPasienBelumBayarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PasienBelumBayar';
    protected $_title = "Informasi Pasien Belum Bayar";

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-bulk-biaya-admin"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        // unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new PasienBelumBayar;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00', strtotime('-1 month'));
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['is_kelola_tagihan'])) {
                $konfig = $this->actionGetKonfigSystem();
                $nominal = $konfig['kelola_tagihan'];
                $is_kelola_tagihan = $_GET['advanced-filter']['is_kelola_tagihan'];
                if($is_kelola_tagihan) {
                    $query->andWhere(['sisa_tagihan' => $nominal]);
                    $query->orWhere(['>=', 'sisa_tagihan', $nominal]);
                }
            }
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }
        } else {
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        }

        $ruangan_id = $request->get('ruangan_id');
        if($ruangan_id) {
            $constansId = new DocoConstansId;
            if($ruangan_id == $constansId->actionGetId('ruangan_kasir_ri')) {
                $query->andWhere(['IS NOT', 'pasienadmisi_id', NULL]);
            }
            elseif($ruangan_id == $constansId->actionGetId('ruangan_kasir_rd')) {
                $query->andWhere(['instalasi_id' => DocoConstants::INST_ID_RD]);
            }
            elseif($ruangan_id == $constansId->actionGetId('ruangan_kasir_rj')) {
                $query->andWhere(['<>', 'instalasi_id', DocoConstants::INST_ID_RD]);
                $query->andWhere(['<>', 'instalasi_id', DocoConstants::INST_ID_RI]);
            }
        }

        
        //belum periksa di hide
        $query->andWhere(['<>', 'status_periksa_id', DocoConstants::BATAL_PERIKSA_INFO_PASIEN_BELUM_BAYAR]);

        $isPasienTitipan = $request->get('is_pasientitipan',null);
        if($isPasienTitipan == 'true'){
            $query->andWhere(['is_pasientitipan' => TRUE]);
        }
        
        $caraBayarId = $request->get('carabayar_id',null);
        if(!empty($caraBayarId)){
            $query->andWhere(['carabayar_id' => $caraBayarId]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query)->asArray();
        return new NoCountDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetKodeRuangan()
    {
        $request = Yii::$app->request;
        $kode = $request->get('kode');
        $result = ['kode_ruangan' => null];
        if($kode) {
            $constansId = new DocoConstansId;
            $result['kode_ruangan'] = $constansId->actionGetId($kode);
        }
        return $result;
    }

    public function actionExportExcel()
    {
        $model = new PasienBelumBayar;
        $query = $model::find(true);
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['is_kelola_tagihan'])) {
                $konfig = $this->actionGetKonfigSystem();
                $nominal = $konfig['kelola_tagihan'];
                $is_kelola_tagihan = $_GET['advanced-filter']['is_kelola_tagihan'];
                if($is_kelola_tagihan) {
                    $query->andWhere(['sisa_tagihan' => $nominal]);
                    $query->orWhere(['>=', 'sisa_tagihan', $nominal]);
                }
            }
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $value['tgl_pendaftaran'] = date("j M Y H:i:s", strtotime($value['tgl_pendaftaran']));
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pendaftaran')] = $value['tgl_pendaftaran'];
            $newValue[\Yii::t('app', 'No Pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t("app", "Nama Pasien")] = $value['nama_pasien'].' / '.$value['no_rekam_medik'].' / '.date("j M Y", strtotime($value['tanggal_lahir']));
            $newValue[\Yii::t("app", "Jenis Kelamin")] = $value['jenis_kelamin'];
            $newValue[\Yii::t('app', 'Penjamin')] = $value['carabayar_nama'].' / '.$value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Ruangan Akhir')] = $value['instalasi_nama'].' / '.$value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Dokter')] = $value['nama_dokter'];
            $newValue[\Yii::t("app", "Status")] = $value['status_periksa'];
            $newValue[\Yii::t("app", "Perkiraan Tagihan")] = $value['total_tagihan'];
            $newValue[\Yii::t("app", "Limit Penjamin")] = $value['limit_tagihan'];
            $newValue[\Yii::t("app", "Uang Muka")] = (!empty($value['uang_muka'])) ? $value['uang_muka'] : 0;
            $newValue[\Yii::t("app", "Pembayaran Tagihan")] = $value['uang_masuk'];
            $newValue[\Yii::t("app", "Sisa Tagihan")] = $value['sisa_tagihan'];
            $newValue[""] = null;
            $result[$key] = $newValue;
        }

        $header = array(
            Yii::t("app", "No Pendaftaran") => (@$_GET['advanced-filter']['no_pendaftaran']),
            Yii::t("app", "Nama Pasien") => (@$_GET['advanced-filter']['nama_pasien']),
            Yii::t("app", "No Rekam Medik") => (@$_GET['advanced-filter']['no_rekam_medik']),
        );

        $footer = [];
        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    public function actionGetKonfigSystem()
    {
        $konfig = [];
        try {
            $konfig = Cache::getKonfigSistem();
        } catch (\Exception $e) {
            return $konfig;
        }
        return $konfig;
    }

    private function getheader()
    {
        $column = [];
        $column = [
        [
            'title' => 'No',
            'data' => 'no',
            'searchable' => false,
            'visible' => true,
        ],
        [
           'title' => 'Tanggal Pendaftaran',
           'data' => 'tgl_pendaftaran',
           'searchable' => false,
           'visible' => true,
       ],
        [
            'title' => 'No Pendaftaran',
            'data' => 'no_pendaftaran',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Nama Pasien',
            'data' => 'nama_pasien',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Jenis Kelamin',
            'data' => 'jenis_kelamin',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Penjamin',
            'data' => 'penjamin_nama',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Ruangan Akhir',
            'data' => 'ruangan_nama',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Dokter',
            'data' => 'nama_dokter',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Status',
            'data' => 'status_periksa',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Perkiraan Tagihan',
            'data' => 'total_tagihan',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Limit Penjamin',
            'data' => 'limit_tagihan',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Uang Muka',
            'data' => 'uang_muka',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Pembayaran Tagihan',
            'data' => 'uang_masuk',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Sisa Tagihan',
            'data' => 'sisa_tagihan',
            'searchable' => false,
            'visible' => true,
        ],
        ];
  
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
        
        $start = $end = '';
        
        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $get['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    if(isset($explode[0])){
                        $start = !empty($explode[0]) ? $explode[0] : '';
                    }
                    if(isset($explode[1])){
                        $end = !empty($explode[1]) ? $explode[1] : '';
                    }
                }    
            }   
        }
        $no_pendaftaran = isset($get['advanced-filter']['no_pendaftaran']) ? $get['advanced-filter']['no_pendaftaran']:'-';
        $nama_pasien = isset($get['advanced-filter']['nama_pasien']) ? $get['advanced-filter']['nama_pasien']:'-';
        $ruangan_nama = isset($get['advanced-filter']['ruangan_nama']) ? $get['advanced-filter']['ruangan_nama']:'-';
        $no_rekam_medik = isset($get['advanced-filter']['no_rekam_medik']) ? $get['advanced-filter']['no_rekam_medik']:'-';
        $instalasi_nama = isset($get['advanced-filter']['instalasi_nama']) ? $get['advanced-filter']['instalasi_nama']:'-';
        $status_periksa = isset($get['advanced-filter']['status_periksa']) ? $get['advanced-filter']['status_periksa'] : '-';
        
        $headerExcel = [
            "Tanggal Pendaftaran" => $start . ' - ' . $end,
            "No Pendaftaran" => $no_pendaftaran,
            "Nama Pasien" => $nama_pasien,
            "No Rekam Medik" => $no_rekam_medik,
            "Instalasi" => $instalasi_nama,
            "Ruangan Akhir" => $ruangan_nama,
            "Status" => $status_periksa
        ];
        $header = $this->getheader();
        $data = $this->actionGetDataLaporan();
        $countData = count($data);
        $totalPerPage = count($data);
        $options = [
            "skipIncrement" => true,
            "customHeader" => [],
        ];
        $uri_kasir = Yii::$app->docoRest->getBaseUri('kasir');
        $params = [
            'sendToUrl' => 'inf-pasien-belum-bayar/drop-file',
            'getDataUrl' => 'inf-pasien-belum-bayar/get-data-laporan',
            'base_uri' => $uri_kasir,
        ];
        (new InternalService)->sendTo([
            'Sirs' => [
                'DataBelumBayar' => [
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
                    'title' => 'Informasi Pasien Belum Bayar',
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
        $result = [];
        $rowNum = 1;
        $request = Yii::$app->request;
        $_GET = $request->get();
        try {
            $model = new PasienBelumBayar;
            $query = $model::find(true);
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['is_kelola_tagihan'])) {
                    $konfig = $this->actionGetKonfigSystem();
                    $nominal = $konfig['kelola_tagihan'];
                    $is_kelola_tagihan = $_GET['advanced-filter']['is_kelola_tagihan'];
                    if($is_kelola_tagihan) {
                        $query->andWhere(['sisa_tagihan' => $nominal]);
                        $query->orWhere(['>=', 'sisa_tagihan', $nominal]);
                    }
                }
                if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    unset($_GET['advanced-filter']['tgl_pendaftaran']);
                    $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
                }
            }
            $query->andWhere(['<>', 'status_periksa_id', DocoConstants::BATAL_PERIKSA_INFO_PASIEN_BELUM_BAYAR]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            foreach ($query->asArray()->all() as $key => $value) {
                $value['no'] = $rowNum;
                $value['tgl_pendaftaran'] = !empty($value['tgl_pendaftaran']) && !is_null($value['tgl_pendaftaran']) ? date('d-M-Y H:i:s', strtotime($value['tgl_pendaftaran'])) : '-';
                $value['no_pendaftaran'] = !empty($value['no_pendaftaran']) && !is_null($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
                $value['data_nama_pasien'] = !empty($value['nama_pasien']) && !is_null($value['nama_pasien']) ? $value['nama_pasien'] : '';
                $value['no_rekam_medik'] = !empty($value['no_rekam_medik']) && !is_null($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '';
                $value['tgl_lahir'] = !empty($value['tanggal_lahir']) && !is_null($value['tanggal_lahir']) ? date('d-M-Y', strtotime($value['tanggal_lahir'])) : '-';
                $value['nama_pasien'] = $value['data_nama_pasien'].' / '.$value['no_rekam_medik'].' / '.$value['tgl_lahir'];
                $value['jenis_kelamin'] = !empty($value['jenis_kelamin']) && !is_null($value['jenis_kelamin']) ? $value['jenis_kelamin'] : '';
                $value['data_penjamin_nama'] = !empty($value['penjamin_nama']) && !is_null($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
                $value['carabayar_nama'] = !empty($value['carabayar_nama']) && !is_null($value['carabayar_nama']) ? $value['carabayar_nama'] : '';
                $value['penjamin_nama'] = $value['carabayar_nama'].' / '.$value['data_penjamin_nama'];
                $value['data_ruangan_nama'] = !empty($value['ruangan_nama']) && !is_null($value['ruangan_nama']) ? $value['ruangan_nama'] : '';
                $value['instalasi_nama'] = !empty($value['instalasi_nama']) && !is_null($value['instalasi_nama']) ? $value['instalasi_nama'] : '';
                $value['ruangan_nama'] = $value['instalasi_nama'].' / '.$value['data_ruangan_nama'];
                $value['nama_dokter'] = !empty($value['nama_dokter']) && !is_null($value['nama_dokter']) ? $value['nama_dokter'] : '';
                $value['status_periksa'] = !empty($value['status_periksa']) && !is_null($value['status_periksa']) ? $value['status_periksa'] : '';
                $value['total_tagihan'] = !empty($value['total_tagihan']) && !is_null($value['total_tagihan']) ? $value['total_tagihan'] : '';
                $value['limit_tagihan'] = !empty($value['limit_tagihan']) && !is_null($value['limit_tagihan']) ? $value['limit_tagihan'] : '';
                $value['uang_muka'] = !empty($value['uang_muka']) && !is_null($value['uang_muka']) ? $value['uang_muka'] : '';
                $value['uang_masuk'] = !empty($value['uang_masuk']) && !is_null($value['uang_masuk']) ? $value['uang_masuk'] : '';
                $value['sisa_tagihan'] = !empty($value['sisa_tagihan']) && !is_null($value['sisa_tagihan']) ? $value['sisa_tagihan'] : 0;
                $value['pendaftaran_id'] = !empty($value['pendaftaran_id']) && !is_null($value['pendaftaran_id']) ? $value['pendaftaran_id'] : 0;
                $value['kelaspelayanan_id'] = !empty($value['kelaspelayanan_id']) && !is_null($value['kelaspelayanan_id']) ? $value['kelaspelayanan_id'] : 0;
                $value['penjamin_id'] = !empty($value['penjamin_id']) && !is_null($value['penjamin_id']) ? $value['penjamin_id'] : 0;
                $value['pasienadmisi_id'] = !empty($value['pasienadmisi_id']) && !is_null($value['pasienadmisi_id']) ? $value['pasienadmisi_id'] : 0;

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
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

    public function actionGetBulkBiayaAdmin(){
        $request = Yii::$app->request;
        $data = $request->post();
        $dataAdmin = [];
        if(is_array($data)){
            foreach($data as $val){
                $primary = isset($val['primary']) ? $val['primary'] : null;
                $pendaftaranId = isset($val['pendaftaranId']) ? $val['pendaftaranId'] : null;
                $penjaminId = isset($val['penjaminId']) ? $val['penjaminId'] : null;
                $kelasPelayananId = isset($val['kelasPelayananId']) ? $val['kelasPelayananId'] : null;
                $totalTagihan = isset($val['totalTagihan']) ? $val['totalTagihan'] : null;
                $pasienAdmisiId = isset($val['pasienAdmisiId']) ? $val['pasienAdmisiId'] : null;

                $total_admin =  TagihanHelper::getBiayaAdmin($pendaftaranId, $penjaminId, $kelasPelayananId, $totalTagihan, $pasienAdmisiId);

                $dataAdmin[$primary] = $total_admin;
            }
        }
        return $dataAdmin;
    }

    public function actionGetPenjamin(){
        $request = Yii::$app->request;
        $data = $request->get();
        $regisId = $data['pendaftaran_id'];
        $groupcarabayarumum_id =  DocoConstants::GROUP_UMUM;
        $queryPenjamin = Yii::$app->db->createCommand("
        SELECT id_payer, nama_payer FROM (
            SELECT
    pendaftaran_multipayer_t.penjamin_id as id_payer,
    penjamin_m.penjamin_nama as nama_payer
    FROM pendaftaran_t
    LEFT JOIN pendaftaran_multipayer_t ON pendaftaran_t.pendaftaran_id = pendaftaran_multipayer_t.pendaftaran_id
    LEFT JOIN penjamin_m ON pendaftaran_multipayer_t.penjamin_id = penjamin_m.penjamin_id
    LEFT JOIN carabayar_m ON pendaftaran_multipayer_t.carabayar_id = carabayar_m.carabayar_id
    LEFT JOIN pendaftaranpenjamin_t ON pendaftaran_t.pendaftaran_id = pendaftaranpenjamin_t.pendaftaran_id
    WHERE pendaftaran_t.pendaftaran_id = {$regisId} AND groupcarabayar_id is not NULL AND groupcarabayar_id != {$groupcarabayarumum_id}
            UNION
            SELECT
            COALESCE(pasienadmisi_t.penjamin_id,pendaftaran_t.penjamin_id) as id_payer,
    penjamin_m.penjamin_nama as nama_payer
            FROM pendaftaran_t
            LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
            LEFT JOIN penjamin_m ON penjamin_m.penjamin_id = COALESCE(pasienadmisi_t.penjamin_id,pendaftaran_t.penjamin_id)
            WHERE pendaftaran_t.pendaftaran_id = {$regisId}
            ) nama_payer")
        ->queryAll();
        return $queryPenjamin;
    }

    public function actionExportExcelDetailBgprocess() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = $request->get('randString', null);
        $params = $request->get('params', []);
        $pendaftaranId = !empty($params['pendaftaran_id']) ? $params['pendaftaran_id'] : null;
        $headerExcel = [];
        $header = $this->getHeaderDetailExcel();
        if(!empty($pendaftaranId)){
            $countData = Yii::$app->db->createCommand("
			                SELECT count(tindakan_obat_id) FROM infotagihanpasien_v WHERE pendaftaran_id = {$pendaftaranId}")
                        ->queryOne();
            $totalPerPage = $countData;
            $options = [
                "skipIncrement" => true,
                "customHeader" => [],
            ];

            (new InternalService)->sendTo([
                'Sirs' => [
                    'DataDetailBelumBayar' => [
                        'unique_str' => $randString,
                        'filter' => $get,
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
                        'title' => 'Informasi Pasien Belum Bayar',
                        'headerExcel' => $headerExcel,
                        'footer' => [],
                        'options' => $options,
                        'header' => $header,
                    ]
                ]
            ], true);
            /** untuk balikkan dari service internal */
            $uri_kasir = Yii::$app->docoRest->getBaseUri('kasir');

            $params = [
                'sendToUrl' => 'inf-pasien-belum-bayar/drop-file',
                'getDataUrl' => 'inf-pasien-belum-bayar/get-data-laporan',
                'base_uri' => $uri_kasir,
            ];

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
        
    }

    private function getHeaderDetailExcel()
    {
        $column = [
            [
                'title' => 'No',
                'data' => 'no',
            ],
            [
                'title' => 'Tanggal Masuk',
                'data' => 'tgl_masuk',
            ],
            [
                'title' => 'Tanggal Keluar',
                'data' => 'tgl_keluar',
            ],
            [
                'title' => 'Instalasi',
                'data' => 'instalasi_nama',
            ],
            [
                'title' => 'Ruangan Akhir',
                'data' => 'ruangan_akhir',
            ],
            [
                'title' => 'No Pendaftaran',
                'data' => 'no_pendaftaran',
            ],
            [
                'title' => 'Nama Pasien',
                'data' => 'nama_pasien',
            ],
            [
                'title' => 'Nomor Rekam Medik',
                'data' => 'no_rekam_medik',
            ],
            [
                'title' => 'DPJP',
                'data' => 'dokterpenanggungjawab_nama',
            ],
            [
                'title' => 'Tindakan',
                'data' => 'daftartindakan_nama',
            ],
            [
                'title' => 'Obat',
                'data' => 'obatalkes_nama',
            ],
            [
                'title' => 'Tarif',
                'data' => 'tarif_satuan',
            ],
            [
                'title' => 'Cara Bayar',
                'data' => 'cara_bayar',
            ],
            [
                'title' => 'Penjamin',
                'data' => 'penjamin_nama',
            ],
            [
                'title' => 'Subtotal',
                'data' => 'subtotal',
            ],
            [
                'title' => 'Diskon',
                'data' => 'diskon',
            ],
            [
                'title' => 'Jumlah Dibayar Penjamin',
                'data' => 'dijamin',
            ],
            [
                'title' => 'Jumlah Dibayar Pasien',
                'data' => 'ditagihkan',
            ],
        ];
  
        return $column;
    }

}
