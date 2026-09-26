<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-24 10:00
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LapKunjunganRawatInap;
use app\modules\v1\models\LapKunjunganRawatInapFn;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\PegawaiView;
use yii\helpers\ArrayHelper;

use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;
use Doco\rabbitmq\RabbitBgProcess;

class LapKunjunganRawatInapController extends DocoActiveController
{
    public $messageBroker = [
        'generate-data-serconn' => [
            'services' => [
                'Sirs' => [
                    'LaporanKunjunganRawatInap' => [
                        'query_params' => ['advance_filter', 'unique_str'],
                    ]
                ],
            ]
        ],
    ];

    public $modelClass = 'app\modules\v1\models\lap-kunjungan-rawat-inap';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:00');

        if (isset($advancedFilters['tgl_pendaftaran'])) {
            $explode = explode(" - ", $advancedFilters['tgl_pendaftaran']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
            unset($advancedFilters['tgl_pendaftaran']);
        }
        
        $model = new LapKunjunganRawatInapFn;
        $query = $model::getData($start, $end)->select([
            'tgl_pendaftaran',
            'no_pendaftaran',
            'no_rekam_medik',
            'nama_pasien',
            'jenis_kelamin',
            'umur',
            'golonganumur_id',
            'golonganumur_nama',
            'agama',
            'statusperkawinan',
            'pekerjaan_nama',
            'alamat_pasien',
            'kabupaten_nama',
            'kunjungan',
            'jeniskasuspenyakit_nama',
            'carabayar_id',
            'carabayar_nama',
            'penjamin_id',
            'penjamin_nama',
            'nama_perujuk',
            'ruangan_nama',
            'kamarruangan_id',
            'kamarruangan_nokamar',
            'no_tempattidur',
            'nama_pegawai',
            'kelaspelayanan_nama',
            'kelas_ditagihkan_nama',
            'status_ranap_nama',
            'carakeluar_nama',
            'diagnosa',
            'tgl_keluar',
            'is_pasientitipan',
            'is_pasientitipan_pk',
            'nosep',
            'status_pasien',
            'kamartempattidur_id'
        ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);


    }

    public function actionGenerateDataSerconn()
    {
        $request = Yii::$app->request->get();
        $data['status'] = 'update';
        $randString = isset($request['unique_str']) ? $request['unique_str'] : null;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'kunjungan-rawat-inap:'.$randString,
            'message' => json_encode($data),
        ]);
        return [
            'randString' => $randString
        ];       
    }

    public function actionIndexBgProcess()
    {
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);

      $query=  (new RabbitBgProcess())->send([
            'filter' => $advancedFilters,
            'base_uri' => Yii::$app->docoRest->getBaseUri('pendaftaran'),
        ], 'index_kunjungan_rawat_inap','sync_kunjungan_rawat_inap');

        return $query;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode# => untuk menampilkan periode data
    * @attribute #tanggal# => untuk menampilkan tanggal sekarang
    * @attribute #tanggal_cetak# => untuk menampilkan tanggal cetak
    * @attribute #jenis# => untuk menampilkan title
    * @attribute #cetak_oleh# => untuk menampilkan pencetak
    * @attribute #kepala# => untuk menampilkan nama kepala ruangan
    * @attribute #kepalanip# => untuk menampilkan nip kepala ruangan
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        try {
            $model = new LapKunjunganRawatInap;
            $query = $model::find();
            $result = $header = $footer = $toggle = [];
            $advancedFilters = $request->get('advanced-filter', []);
            $tgl_awal = $tgl_akhir = date('d-M-Y');
            if (isset($advancedFilters['tgl_pendaftaran_awal'])
                    && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
                $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
            }
            if(isset($advancedFilters['toggle'])){
                $toggle = $advancedFilters['toggle'];
                unset($_GET['advanced-filter']['toggle']);
            }
            $periode = date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir));
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['tgl_pendaftaran' => SORT_DESC]);
            $key = [
                '3' => [
                    'title' => 'Jenis Kelamin',
                    'row' => 'jenis_kelamin'
                ],
                '4' => [
                    'title' => 'Umur',
                    'row' => 'umur'
                ],
                '5' => [
                    'title' => 'Golongan Umur',
                    'row' => 'golonganumur_nama'
                ],
                '6' => [
                    'title' => 'Agama',
                    'row' => 'agama'
                ],
                '7' => [
                    'title' => 'Status Perkawinan',
                    'row' => 'statusperkawinan'
                ],
                '8' => [
                    'title' => 'Pekerjaan',
                    'row' => 'pekerjaan_nama'
                ],
                '9' => [
                    'title' => 'Kota/Kab',
                    'row' => 'kabupaten_nama'
                ],
                '10' => [
                    'title' => 'Status Kunjungan',
                    'row' => 'kunjungan'
                ],
                '11' => [
                    'title' => 'Jenis Kasus Penyakit',
                    'row' => 'jeniskasuspenyakit_nama'
                ],
                '12' => [
                    'title' => 'Cara Bayar / Penjamin',
                    'row' => 'carabayar_penjamin',
                ],
                '15' => [
                    'title' => 'Rujukan',
                    'row' => 'nama_perujuk'
                ],
                '16' => [
                    'title' => 'Ruangan',
                    'row' => 'ruangan_nama'
                ],
                '17' => [
                    'title' => 'Kamar - Bed',
                    'row' => 'kamar_bed'
                ],
                '20' => [
                    'title' => 'Dokter',
                    'row' => 'nama_pegawai'
                ],
                '21' => [
                    'title' => 'Kelas Pelayanan / Kelas Tagihan',
                    'row' => 'kelaspelayanan_nama'
                ],
                '22' => [
                    'title' => 'Status Periksa',
                    'row' => 'status_ranap_nama'
                ],
                '23' => [
                    'title' => 'Status Pulang',
                    'row' => 'carakeluar_nama'
                ],
                '24' => [
                    'title' => 'Diagnosa',
                    'row' => 'diagnosa'
                ],
                '25' => [
                    'title' => 'Tanggal Keluar',
                    'row' => 'tgl_keluar'
                ],
            ];
            $data = $query->all();
            if($toggle){
                foreach ($toggle as $k => $v) {
                    $keyheader[$v] = $key[$v];
                }
            }else{
                $keyheader = $key;
            }
            $kepalaruangan = $this->getKepalaRuangan();
            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('index', [
                    'data' => $data,
                    'header' => $keyheader
                ]),
                '#periode#' => $periode,
                '#tanggal#' => DocoHelpers::convDateTime(date('d M Y')),
                '#tanggal_cetak#' => DocoHelpers::convDateTime(date('d M Y H:i:s')),
                '#jenis#' => 'Rawat Inap',
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#kepala#' => $kepalaruangan['kepalaruangan'],
                '#kepalanip#' => $kepalaruangan['kepalaruangannip'],
            ];
            $print->Output();
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \Exception("Terjadi Kesalahan", 1);
        } catch (\yii\db\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \Exception("Terjadi Kesalahan", 1);
        }
    }

    private function getKepalaRuangan()
    {
        try {
            $getKepalaRuangan = PegawaiView::find()->where([
                'ruangan_id'=> Yii::$app->jwt->ruangan_id,
                'jabatan_id'=>DocoConstants::VAR_J_K_R])->one();
            return [
                'kepalaruangan' => $getKepalaRuangan->nama_pegawai,
                'kepalaruangannip' => $getKepalaRuangan->nomorindukpegawai
            ];
        } catch (\Exception $e) {
            return [
                'kepalaruangan' => '',
                'kepalaruangannip' => '',
            ];
        } catch (\yii\db\Exception $e){
            return [
                'kepalaruangan' => '',
                'kepalaruangannip' => '',
            ];
        }
    }

    public function actionExportExcelBgproses()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        $getData = $request->get();
        $params = isset($getData['params']) ? $getData['params'] : [];
        $countData = $this->actionGetObjectData()->count();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $advancedFilters = [];

        if(isset($getData['advanced-filter'])) {
            $advancedFilters = $getData['advanced-filter'];
        }

        if(isset($params['advanced-filter'])) {
            $advancedFilters = $params['advanced-filter'];
        }

        if (isset($advancedFilters['tgl_pendaftaran'])) {
            $explode = explode(" - ", $advancedFilters['tgl_pendaftaran']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
        }

        $headerExcel = [
            'Tanggal Pendaftaran' =>date('d M Y', strtotime($start)).' - '.date('d M Y', strtotime($end)),
            'Cara Bayar' => ArrayHelper::getValue($getData, 'filters_name.carabayar_id', '-'),
            'Penjamin' => ArrayHelper::getValue($getData, 'filters_name.penjamin_id', '-'),
            'Ruangan' => ArrayHelper::getValue($getData, 'filters_name.ruangan_id', '-'),
            'Kamar' => ArrayHelper::getValue($getData, 'filters_name.kamar_id', '-'),
            'No. Tempat Tidur' => ArrayHelper::getValue($getData, 'filters_name.tempattidur_id', '-'),
            'Dokter' => ArrayHelper::getValue($getData, 'filters_name.pegawai_id', '-'),
            'No SEP' => ArrayHelper::getValue($advancedFilters, 'nosep', '-'),
        ];

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $countData,
            'headerExcel' => $headerExcel,
            'countData' => $countData, 
            'title' => 'Laporan Kunjungan Rawat Inap',
            'sendToUrl' => 'lap-kunjungan-rawat-inap/download-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('pendaftaran'),
        ], 'laporan_kunjungan_rawat_inap','export_csv');

        return [
            'totalPerPage' => $countData,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionGetObjectData()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $params = isset($getData['params']) ? $getData['params'] : [];
        $advancedFilters = [];

        if(isset($getData['advanced-filter'])) {
            $advancedFilters = $getData['advanced-filter'];
        }

        if(isset($params['advanced-filter'])) {
            $advancedFilters = $params['advanced-filter'];
        }

        if (isset($advancedFilters['tgl_pendaftaran'])) {
            $explode = explode(" - ", $advancedFilters['tgl_pendaftaran']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
            unset($advancedFilters['tgl_pendaftaran']);
        }

        $model = new LapKunjunganRawatInapFn;
        $query = $model::getData($start, $end);

        $query->orderBy(['pendaftaran_id' => SORT_DESC]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $tipe = $request->get('tipe', null);
        $ext = ($tipe == 'excel') ? '.xlsx' : '.pdf';
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.$ext;
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            if($tipe == 'excel') {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            }
            else {
                header('Content-Type: application/pdf');
            }
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
}
