<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPendapatanRuanganView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\RuanganView;
use yii\db\Query;
use Doco\components\DocoPrint;
use Doco\Services\InternalService;
use Doco\components\DocoConstants;
use app\modules\v1\models\UploadForm;
use Doco\rabbitmq\RabbitBgProcess;
use yii\web\UploadedFile;


class LaporanPendapatanRuanganController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPendapatanRuanganView';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    // public function actions()
    // {
    //     $actions = parent::actions();

    //     unset($actions['index']);
    //     unset($actions['view']);
        

    //     return $actions;
    // }

    /**
    *
    * @see Fungsi override action index
    * @return array, activeQueryRecords data pendapatan ruangan
    *
    */

    public function actionIndex()
    {
        try{
            $request = Yii::$app->request;
            $model = new LaporanPendapatanRuanganView;
            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $instalasi_id = $request->get('instalasi_id', DocoConstants::INSTALASI_KASIR);
            $ruangan_id = $request->get('ruangan_id', null);
            if($instalasi_id == DocoConstants::INSTALASI_KASIR) {
                $query->andWhere(['ruangan_id' => $ruangan_id]);
            } else {
                $query->andWhere(['instalasi_id' => $instalasi_id]);
            }

            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:59');
            if($request->get('advanced-filter')) {
                $advancedFilters = $request->get('advanced-filter');
                if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                    && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                    $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                    $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
                    
                }
                if($instalasi_id == DocoConstants::INSTALASI_KASIR) {
                    if(isset($advancedFilters['instalasi_ruangan'])) {
                        $instalasi_ruangan = $advancedFilters['instalasi_ruangan'];
                        $query->andWhere(['ruangan_id' => $instalasi_ruangan]);
                    }else {
                        $query->andWhere(['instalasi_id' => $instalasi_id]);
                    }
                }
            }
            
            $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
            $additionalData = [
                'summary' => [
                    'total' => $query->sum('total')
                ]
            ];
            return  $this->activeDataProvider($query, $additionalData);
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

    public function actionView($id)
    {
        return $this->getData($id)->asArray()->one();
    }

    private function getData($id = null)
    {
        $model = LaporanPendapatanRuanganView::find();
        if ($id) {
            $model->where(['pendaftaran_id' => $id]);
        }

        return $model;
    }

    public function actionGetRequest()
    {
        $request = Yii::$app->request;
        $is_admin = $request->get('is_admin');

        $modelCaraBayar = CaraBayar::find();
        $queryCaraBayar = $modelCaraBayar->all();

        $modelPenjamin = Penjamin::find();
        $queryPenjamin = $modelPenjamin->all();

        $modelPegawai = Pegawai::find();
        $queryPegawai = $modelPegawai->all();

        $modelKelasPelayanan = KelasPelayanan::find();
        $queryKelasPelayanan = $modelKelasPelayanan->all();

        $arrRuangan = [];
        if(empty($is_admin)) {
            $pegawai_id = Yii::$app->jwt->user->pegawai_id;
            $modelRuanganPegawai = PegawaiView::find()
                ->where(['pegawai_id' => $pegawai_id])
                ->all();

            if($modelRuanganPegawai) {
                foreach ($modelRuanganPegawai as $value) {
                    $arrRuangan[] = $value['ruangan_id'];
                }
            }
        }

        $modelRuangan = Ruangan::find()->where(['IN', 'ruangan_id', $arrRuangan]);
        $queryRuangan = $modelRuangan->all();

        return [
            'penjamin' => ArrayHelper::map($queryPenjamin, 'penjamin_id', 'penjamin_nama'),
            'ruangan' => ArrayHelper::map($queryRuangan, 'ruangan_nama', 'ruangan_nama'),
            'cara_bayar' => ArrayHelper::map($queryCaraBayar, 'carabayar_id', 'carabayar_nama'),
            'pegawai' => ArrayHelper::map($queryPegawai, 'nama_pegawai', 'nama_pegawai'),
            'kelas_pelayanan' => ArrayHelper::map($queryKelasPelayanan, 'kelaspelayanan_nama', 
                'kelaspelayanan_nama')
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new LaporanPendapatanRuanganView;
        $query = $model::find();
        $instalasi_id = $request->get('instalasi_id', DocoConstants::INSTALASI_KASIR);
        $ruangan_id = $request->get('ruangan_id', null);
        if($instalasi_id != DocoConstants::INSTALASI_KASIR) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
            $ruangan = Ruangan::findOne($ruangan_id);
            $namaRuangan = $ruangan['ruangan_nama'];
        }
        else {
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['instalasi_ruangan'])) {
                    $ruangan_id = $_GET['advanced-filter']['instalasi_ruangan'];
                    $ruangan = RuanganView::find()->where(['ruangan_id' => $ruangan_id])->one();
                    $namaRuangan = $ruangan['instalasi_nama'].' - '.$ruangan['ruangan_nama'];
                } else {
                    $ruangan = RuanganView::find()->where(['ruangan_id' => $ruangan_id])->one();
                    $namaRuangan = $ruangan['instalasi_nama'].' - '.$ruangan['ruangan_nama'];
                }
            }
        }
        
        $title = 'Laporan Pendapatan Ruangan '.$namaRuangan;
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $header = [];
        $header[Yii::t('app', "Periode")] = ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))));
        if(isset($_GET['advanced-filter'])) {
            $advancedFilters = $_GET['advanced-filter'];
            if(isset($advancedFilters['tgl_pendaftaran_awal']) && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = date('Y-m-d H:i:s', strtotime($advancedFilters['tgl_pendaftaran_awal']. ' 00:00:00'));
                $tgl_akhir   = date('Y-m-d H:i:s', strtotime($advancedFilters['tgl_pendaftaran_akhir'] . ' 23:59:59'));
                $header[Yii::t('app', "Periode")] = ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))));
                unset($_GET['advanced-filter']['tgl_pendaftaran_awal']);
                unset($_GET['advanced-filter']['tgl_pendaftaran_akhir']);
            }

            if(isset($advancedFilters['no_pendaftaran'])) {
                $header[Yii::t('app', "No. Pendaftaran")] = $_GET['advanced-filter']['no_pendaftaran'];
            } else {
                $header[Yii::t('app', "No. Pendaftaran")] = '-';
            }

            if(isset($advancedFilters['no_rekam_medik'])) {
                $header[Yii::t('app', "No Rekam Medik")] = $_GET['advanced-filter']['no_rekam_medik'];
            } else {
                $header[Yii::t('app', "No Rekam Medik")] = '-';
            }

            if(isset($advancedFilters['nama_pasien'])) {
                $header[Yii::t('app', "Nama Pasien")] = $_GET['advanced-filter']['nama_pasien'];
            } else {
                $header[Yii::t('app', "Nama Pasien")] = '-';
            }

            if(isset($advancedFilters['carabayar_id'])) {
                $cara_bayar = CaraBayar::findOne($advancedFilters['carabayar_id']);
                $carabayar_nama = $cara_bayar->carabayar_nama;
                $header[Yii::t('app', "Cara Bayar")] = $carabayar_nama;
            } else {
                $header[Yii::t('app', "Cara Bayar")] = '-';
            }

            if(isset($advancedFilters['penjamin_id'])) {
                $penjamin = Penjamin::findOne($advancedFilters['penjamin_id']);
                $penjamin_nama = $penjamin->penjamin_nama;
                $header[Yii::t('app', "Penjamin")] = $penjamin_nama;
            } else {
                $header[Yii::t('app', "Penjamin")] = '-';
            }

            if(isset($advancedFilters['nama_pegawai'])) {
                $header[Yii::t('app', "Dokter")] = $_GET['advanced-filter']['nama_pegawai'];
            } else {
                $header[Yii::t('app', "Dokter")] = '-';
            }

            if(isset($advancedFilters['kelaspelayanan_nama'])) {
                $header[Yii::t('app', "Kelas Pelayanan")] = $_GET['advanced-filter']['kelaspelayanan_nama'];
            } else {
                $header[Yii::t('app', "Kelas Pelayanan")] = '-';
            }

            if($instalasi_id == DocoConstants::INSTALASI_KASIR) {
                if(isset($advancedFilters['instalasi_ruangan'])) {
                    $instalasi_ruangan = $_GET['advanced-filter']['instalasi_ruangan'];
                    $header[Yii::t('app', "Instalasi/Ruangan")] = $namaRuangan;
                    $query->andWhere(['ruangan_id' => $instalasi_ruangan]);
                }
            }
            else {
                $header[Yii::t('app', "Ruangan")] = $namaRuangan;
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        
        $result = [];
        $totalRs = $totalJp = 0;
        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal Pendaftaran')] = date('d M Y', strtotime($value['tgl_pendaftaran']));
            $newValue[\Yii::t('app', 'No Pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'No Rekam Medik')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'Cara Bayar')] = $value['carabayar_nama'];
            $newValue[\Yii::t('app', 'Nama Penjamin')] = $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Dokter')] = $value['nama_pegawai'];
            $newValue[\Yii::t('app', 'Ruangan')] = $value['ruangan_nama'];
            $newValue[\Yii::t('app', 'Kelas Pelayanan')] = $value['kelaspelayanan_nama'];
            $newValue[\Yii::t('app', 'Rumah Sakit (Rp.)')] = $value['jasa_rumahsakit'];
            $newValue[\Yii::t('app', 'Jasa Pelayanan (Rp.)')] = $value['jasa_layanan'];
            $newValue[\Yii::t('app', 'Total (Rp.)')] = $value['total'];
            $totalRs += $value['jasa_rumahsakit'];
            $totalJp += $value['jasa_layanan'];
            $result[$key] = $newValue;
        }

        $footer = [
            'title' => ['TOTAL', 9],
            'data' => [
                'Rumah Sakit (Rp.)' => $totalRs,
                'Jasa Pelayanan (Rp.)' => $totalJp,
                'Total (Rp.)' => $totalRs + $totalJp,
            ]
        ];

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataLaporanExcel()->asArray()->all();
        $limit = 100;
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData /$limit);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPendapatanRuanganExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportLaporanPendapatanRuangan' => [
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
                'UploadLaporanPendapatanRuanganExcel' => [
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

    public function actionSyncExportExcelRabbitmq() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $data = $this->getDataLaporanExcel()->asArray()->all();
        $limit = 100;
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData /$limit);

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $countData,
            'countData' => $countData,
            'sendToUrl' => 'laporan-pendapatan-ruangan/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('apotek'),
        ], 'laporan_pendapatan_ruangan',  'import_data_pendapatan_ruangan');

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function getDataLaporanExcel()
    {
        $request = Yii::$app->request;
        $model = new LaporanPendapatanRuanganView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        $instalasi_id = $request->get('instalasi_id', DocoConstants::INSTALASI_KASIR);
        $ruangan_id = $request->get('ruangan_id', null);
        
        if ($instalasi_id == DocoConstants::INSTALASI_KASIR) {
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }else {
            $query->andWhere(['instalasi_id' => $instalasi_id]);
        }

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }

            if ($instalasi_id == DocoConstants::INSTALASI_KASIR) {
                if (isset($_GET['advanced-filter']['instalasi_ruangan'])) {
                    $instalasi_ruangan = $_GET['advanced-filter']['instalasi_ruangan'];
                    $query->andWhere(['ruangan_id' => $instalasi_ruangan]);
                }
            }else {
                $query->andWhere(['instalasi_id' => $instalasi_id]);
            }
        }
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName . '.' . $ext;

            $path = "uploads/" . $filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path . '/' . $model->file;
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
        $dir = $rootPath . '/' . $no_request;
        $fileName = $dir . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);

    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => periode tanggal
    * @attribute #tanggal# => tanggal sekarang
    * @attribute #jabatan# => jabatan
    * @attribute #title# => title
    * @attribute #pegawai# => pegawai mengetahui
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->jwt->ruangan_id;
        $ruangan = Ruangan::findOne($ruangan_id);
        $title = 'Laporan Pendapatan Ruangan '.$ruangan['ruangan_nama'];
        $get = $request->get();
        $model = new LaporanPendapatanRuanganView;
        $query = $model::find();
        $query->andWhere(['ruangan_id' => $ruangan_id]);
        
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        $carabayar_nama = '';
        $penjamin_nama = '';
        if($request->get('advanced-filter')) {
            $advancedFilters = $request->get('advanced-filter');
            if(isset($advancedFilters['tgl_pendaftaran'])) {
                $exp = explode(' - ', $advancedFilters['tgl_pendaftaran']);
                $tgl_awal = $exp[0];
                $tgl_akhir = $exp[1];
                $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
                $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
                $advancedFilters['tgl_pendaftaran_awal'] = $tgl_awal_format;
                $advancedFilters['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            }
            if(isset($advancedFilters['carabayar_id'])) {
                $cara_bayar = CaraBayar::findOne($advancedFilters['carabayar_id']);
                $carabayar_nama = $cara_bayar->carabayar_nama;
            }
            if(isset($advancedFilters['penjamin_id'])) {
                $penjamin = Penjamin::findOne($advancedFilters['penjamin_id']);
                $penjamin_nama = $penjamin->penjamin_nama;
            }
        }
        
        
        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $pegawai = PegawaiView::find()
            ->where(['ruangan_id' => $ruangan_id, 'jabatan_id' => 3])
            ->one();
        
        $jabatan = ($pegawai) ? $pegawai->jabatan_nama : '';
        $mengetahui = ($pegawai) ? $pegawai->nama_pegawai : '';
        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir)),
            '#tanggal#' => date('d M Y'),
            '#jabatan#' => $jabatan,
            '#pegawai#' => $mengetahui,
            '#title#' => $title,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $dataProvider->getModels(),
            ]),
        ];

        $print->Output();
    }

    public function actions()
    {
        /**
         * @Author: [Budi][budi@docotel.com]
         * 
         * DATA ATTRIBUTE YANG BISA DIGUNAKAN
         * 
         * ---------------------------------------------------------------------
         * selected : kolom yg akan ditampilkan
         * contoh penggunaan : 
         * selected => ['nama_kolom'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * field_search : filter kolom berdasarkan pencarian / term equals 1 char
         * contoh penggunaan : 
         * field_search => ['nama_kolom'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * is_where : filter kolom berdasarkan pencarian / term equals 1 word
         * contoh penggunaan : 
         * is_where => ['nama_kolom'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * default_where : filter kolom berdasarkan 2 parameter (nama_kolom, value)
         * contoh penggunaan : 
         * default_where => ['nama_kolom', 'value'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * other_where : filter kolom berdasarkan 3 parameter (query filter, nama_kolom, value)
         * contoh penggunaan :
         * other_where => ['ILIKE/WHERE/LIKE/ETC', 'nama_kolom', 'value'] (bisa lebih dari satu kolom)
         * ---------------------------------------------------------------------
         * 
         * ---------------------------------------------------------------------
         * orderby : sorting berdasarkan 2 parameter (nama kolom, ASC/DESC)
         * contoh penggunaan :
         * orderby => ['nama_kolom', ASC/DESC]
         * ---------------------------------------------------------------------
         * 
         */
        return [
            'get-data-ruangan' => [
                'class' => 'Doco\actions\GetDataAction',
                'model' => new RuanganView,
                'selected' => [
                    'ruangan_id AS id',
                    'ruangan_nama as text',
                    'instalasi_nama',
                    'ruangan_id',
                    'ruangan_nama',
                ],
                'field_search' => [
                    'instalasi_nama',
                    'ruangan_nama',
                ],
            ],
        ];
    }
}