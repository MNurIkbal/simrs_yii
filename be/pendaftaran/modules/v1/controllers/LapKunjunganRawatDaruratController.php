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
use app\modules\v1\models\LapKunjunganRawatDarurat;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\PegawaiView;
use Doco\rabbitmq\RabbitBgProcess;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadForm;

class LapKunjunganRawatDaruratController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\lap-kunjungan-rawat-darurat';
    public static $look_exclude = [402,628];
    const STATUS_PERIKSA = 'status_periksa_id';

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
        $model = new LapKunjunganRawatDarurat;
        $request = Yii::$app->request;
        $query = $model::find();
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
            // $query->andWhere(['<=', 'tgl_pendaftaran', $tgl_akhir]);
            unset($_GET['advanced-filter']['tgl_pendaftaran_awal']);
            unset($_GET['advanced-filter']['tgl_pendaftaran_akhir']);
        }
        $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);


        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionExportPdf
    * @attribute #table# => table
    **/
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $model = new LapKunjunganRawatDarurat;
        $query = $model::find();
        $result = $header = $footer = $toggle = [];
        $advancedFilters = $request->get('advanced-filter', []);
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
        $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
        $periode = date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir));
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $key = [
            '4' => [
                'title' => 'Umur',
                'row' => 'umur'
            ],
            '5' => [
                'title' => 'Golongan Umur',
                'row' => 'golonganumur_nama'
            ],
            '6' => [
                'title' => 'Jenis Kelamin',
                'row' => 'jenis_kelamin'
            ],
            '7' => [
                'title' => 'Agama',
                'row' => 'agama'
            ],
            '8' => [
                'title' => 'Status Perkawinan',
                'row' => 'status_perkawinan'
            ],
            '9' => [
                'title' => 'Pekerjaan',
                'row' => 'pekerjaan_nama'
            ],
            '10' => [
                'title' => 'Kota/Kab',
                'row' => 'kabupaten_nama'
            ],
            '11' => [
                'title' => 'Status Kunjungan',
                'row' => 'kunjungan'
            ],
            '12' => [
                'title' => 'Jenis Kasus Penyakit',
                'row' => 'jeniskasuspenyakit_nama'
            ],
            '13' => [
                'title' => 'Ruangan',
                'row' => 'ruangan_nama'
            ],
            '16' => [
                'title' => 'Cara Bayar / Penjamin',
                'row' => 'carabayar_penjamin',
            ],
            '17' => [
                'title' => 'Dokter IGD',
                'row' => 'nama_pegawai'
            ],
            '18' => [
                'title' => 'Kelas Pelayanan',
                'row' => 'kelaspelayanan_nama'
            ],
            '19' => [
                'title' => 'Status Pulang / Kondisi',
                'row' => 'kondisipulang'
            ],
            '20' => [
                'title' => 'Status Pemeriksaan',
                'row' => 'status_periksa'
            ],
            '21' => [
                'title' => 'No SEP',
                'row' => 'nosep'
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
            '#table#' => $this->renderPartial('index', [
                'data' => $data,
                'header' => $keyheader
            ]),
            '#periode#' => $periode,
            '#tanggal#' => DocoHelpers::convDateTime(date('d M Y')),
            '#tanggal_cetak#' => DocoHelpers::convDateTime(date('d M Y H:i:s')),
            '#jenis#' => 'Rawat Darurat',
            '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
            '#kepala#' => $kepalaruangan['kepalaruangan'],
            '#kepalanip#' => $kepalaruangan['kepalaruangannip'],
        ];
        $print->Output();
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

    private function getDataExcel()
    {
        $model = new LapKunjunganRawatDarurat;
        $request = Yii::$app->request;
        $query = $model::find();
        $advancedFilters = $request->get('advanced-filter', []);
        if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
            unset($_GET['advanced-filter']['tgl_pendaftaran_awal']);
            unset($_GET['advanced-filter']['tgl_pendaftaran_akhir']);
        }
        $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionExportExcelBgproses()
    {
        $request = Yii::$app->request;
        $getData = $request->get();

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $countData = $this->getDataExcel($getData)->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $countData,
            'countData' => $countData,
            'sendToUrl' => 'lap-kunjungan-rawat-darurat/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('pendaftaran'),
        ], 'laporan_kunjungan_rawat_darurat',  'import_data_laporan_kunjungan_rawat_darurat');

        return [
            'totalPerPage' => $countData,
            'unique_str' => $randString,
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
        $fileName = $rootPath.'/' . $no_request . '.xlsx';

        DocoHelpers::downloadFileExcel($fileName);
    }
}