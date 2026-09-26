<?php

/**
 * @Author: rizal
 * @Date:   2018-01-24 10:39:52
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\LaporanKunjunganRawatJalanView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\UploadForm;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\rabbitmq\RabbitBgProcess;

class LapKunjunganRawatJalanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanKunjunganRawatJalanView';
    public static $look_exclude = [402,628];
    const STATUS_PERIKSA = 'status_periksa_id';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
    * @author Rizal
    * @since 2018-01-24 10:41:50
    * @param 
    * @Update Iqbal 
    * @date 2018-08-09 
    * @desc 
    * @return json list data
    */
    private function Model(){
        $model = new LaporanKunjunganRawatJalanView;
        return $model::find();
    }

    public function actionIndex()
    {
        $model = new LaporanKunjunganRawatJalanView;
        $query = $this->model();

        // modify advanced filters
        $request = Yii::$app->request;
        $advancedFilters = $request->get('advanced-filter', []);
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($advancedFilters)) {
            if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $start = $advancedFilters['tgl_pendaftaran_awal'];
                $end = $advancedFilters['tgl_pendaftaran_akhir'];
            }

            if(isset($advancedFilters['no_induk_kependudukan'])) {
                $no_induk_kependudukan = $advancedFilters['no_induk_kependudukan'];
                $query->andWhere(['no_induk_kependudukan' => $no_induk_kependudukan]);
                unset($_GET['advanced-filter']['no_induk_kependudukan']);
            }

            if(isset($advancedFilters['nosep'])) {
                $nosep = $advancedFilters['nosep'];
                $query->andWhere(['nosep' => $nosep]);
                unset($_GET['advanced-filter']['nosep']);
            }

            if(isset($advancedFilters['status_periksa'])) {
                $status_periksa_id = $advancedFilters['status_periksa'];
                $query->andWhere(['status_periksa_id' => $status_periksa_id]);
                unset($_GET['advanced-filter']['status_periksa']);
            }

            if(isset($advancedFilters['carakeluar_nama'])) {
                $carakeluar_id = $advancedFilters['carakeluar_nama'];
                $query->andWhere(['carakeluar_id' => $carakeluar_id]);
                unset($_GET['advanced-filter']['carakeluar_nama']);
            }

            if(isset($advancedFilters['status_skrining'])) {
                $status_skrining = $advancedFilters['status_skrining'];
                $query->andWhere(['status_skrining' => $status_skrining]);
                unset($_GET['advanced-filter']['status_skrining']);
            }
        }
        $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['tgl_pendaftaran'=> SORT_DESC]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
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
        ini_set('memory_limit', '-1'); 
        ini_set('max_execution_time', '300'); 
        ini_set("pcre.backtrack_limit", 5000000);
        $request = Yii::$app->request;
        try {
            $model = new LaporanKunjunganRawatJalanView;
            $query = $model::find();
            $result = $header = $footer = $toggle = [];
            $advancedFilters = $request->get('advanced-filter', []);
            $tgl_awal = date('Y-m-d 00:00:00');
            $tgl_akhir = date('Y-m-d 23:59:00');
            if(isset($advancedFilters)) {
                if (isset($advancedFilters['tgl_pendaftaran_awal'])
                    && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                    $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                    $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
                }

                if(isset($advancedFilters['status_periksa'])) {
                    $status_periksa_id = $advancedFilters['status_periksa'];
                    $query->andWhere(['status_periksa_id' => $status_periksa_id]);
                    unset($_GET['advanced-filter']['status_periksa']);
                }
                if(isset($advancedFilters['carakeluar_nama'])) {
                    $carakeluar_id = $advancedFilters['carakeluar_nama'];
                    $query->andWhere(['carakeluar_id' => $carakeluar_id]);
                    unset($_GET['advanced-filter']['carakeluar_nama']);
                }
                if(isset($advancedFilters['toggle'])){
                    $toggle = $advancedFilters['toggle'];
                    unset($_GET['advanced-filter']['toggle']);
                }
            }
            $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
            $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
            $periode = date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir));
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['tgl_pendaftaran'=> SORT_DESC]);
          
            $key = [
                '3' => [
                    'title' => 'Status Pasien',
                    'row' => 'status_pasien'
                ],
                '4' => [
                    'title' => 'NIK',
                    'row' => 'no_induk_kependudukan'
                ],
                '5' => [
                    'title' => 'Jenis Kelamin',
                    'row' => 'jenis_kelamin'
                ],
                '6' => [
                    'title' => 'Umur',
                    'row' => 'umur'
                ],
                '7' => [
                    'title' => 'Golongan Umur',
                    'row' => 'golonganumur_nama'
                ],
                '8' => [
                    'title' => 'Agama',
                    'row' => 'agama'
                ],
                '9' => [
                    'title' => 'Status Perkawinan',
                    'row' => 'statusperkawinan'
                ],
                '10' => [
                    'title' => 'Pekerjaan',
                    'row' => 'pekerjaan_nama'
                ],
                '11' => [
                    'title' => 'Kota/Kab',
                    'row' => 'kabupaten_nama'
                ],
                '12' => [
                    'title' => 'Status Kunjungan',
                    'row' => 'kunjungan'
                ],
                '13' => [
                    'title' => 'Jenis Kasus Penyakit',
                    'row' => 'jeniskasuspenyakit_nama'
                ],
                '14' => [
                    'title' => 'Cara Bayar / Penjamin',
                    'row' => 'carabayar_penjamin',
                ],
                '17' => [
                    'title' => 'Rujukan',
                    'row' => 'nama_perujuk'
                ],
                '18' => [
                    'title' => 'Ruangan',
                    'row' => 'ruangan_nama'
                ],
                '19' => [
                    'title' => 'Dokter',
                    'row' => 'nama_pegawai'
                ],
                '20' => [
                    'title' => 'Kelas Pelayanan',
                    'row' => 'kelaspelayanan_nama'
                ],
                '21' => [
                    'title' => 'Status Pulang / Kondisi',
                    'row' => 'status_pulang'
                ],
                '22' => [
                    'title' => 'Status Pemeriksaan',
                    'row' => 'status_periksa'
                ],
                '23' => [
                    'title' => 'Status Skrining',
                    'row' => 'status_skrining'
                ],
                '24' => [
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
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $data,
                    'header' => $keyheader
                ]),
                '#periode#' => $periode,
                '#tanggal#' => DocoHelpers::convDateTime(date('d M Y')),
                '#tanggal_cetak#' => DocoHelpers::convDateTime(date('d M Y H:i:s')),
                '#jenis#' => 'Rawat Jalan',
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#kepala#' => $kepalaruangan['kepalaruangan'],
                '#kepalanip#' => $kepalaruangan['kepalaruangannip'],
            ];
            $print->Output();
        } catch (\Exception $e) {
            throw new \Exception("Terjadi Kesalahan", 1);
        } catch (\yii\db\Exception $e) {
            throw new \Exception("Terjadi Kesalahan", 1);
        }
    }

    public function actionExportExcel()
    {
        ini_set('memory_limit', '-1'); 
        ini_set('max_execution_time', '300'); 
        ini_set("pcre.backtrack_limit", 5000000);
        $request = Yii::$app->request;
        $model = new LaporanKunjunganRawatJalanView;
        $query = $model::find();
        $result = $header = $footer = $toggle = [];
        $advancedFilters = $request->get('advanced-filter', []);
        $tgl_awal = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:00');
        if(isset($advancedFilters)) {
            if (isset($advancedFilters['tgl_pendaftaran_awal']) 
                && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
                $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
                $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            }

            if(isset($advancedFilters['status_periksa'])) {
                $status_periksa_id = $advancedFilters['status_periksa'];
                $query->andWhere(['status_periksa_id' => $status_periksa_id]);
                unset($_GET['advanced-filter']['status_periksa']);
            }

            if(isset($advancedFilters['carakeluar_nama'])) {
                $carakeluar_id = $advancedFilters['carakeluar_nama'];
                $query->andWhere(['carakeluar_id' => $carakeluar_id]);
                unset($_GET['advanced-filter']['carakeluar_nama']);
            }
            if(isset($advancedFilters['toggle'])){
                $toggle = $advancedFilters['toggle'];
                unset($_GET['advanced-filter']['toggle']);
            }
        }
        $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
        $query->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
        $periode = date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir));
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['tgl_pendaftaran'=> SORT_DESC]);
        $key = [
                '3' => [
                    'title' => 'NIK',
                    'row' => 'no_identitas_pasien'
                ],
                '4' => [
                    'title' => 'Jenis Kelamin',
                    'row' => 'jenis_kelamin'
                ],
                '5' => [
                    'title' => 'Umur',
                    'row' => 'umur'
                ],
                '6' => [
                    'title' => 'Golongan Umur',
                    'row' => 'golonganumur_nama'
                ],
                '7' => [
                    'title' => 'Agama',
                    'row' => 'agama'
                ],
                '8' => [
                    'title' => 'Status Perkawinan',
                    'row' => 'statusperkawinan'
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
                    'title' => 'Cara Bayar / Penjamin',
                    'row' => 'carabayar_penjamin',
                ],
                '16' => [
                    'title' => 'Rujukan',
                    'row' => 'nama_perujuk'
                ],
                '17' => [
                    'title' => 'Ruangan',
                    'row' => 'ruangan_nama'
                ],
                '18' => [
                    'title' => 'Dokter',
                    'row' => 'nama_pegawai'
                ],
                '19' => [
                    'title' => 'Kelas Pelayanan',
                    'row' => 'kelaspelayanan_nama'
                ],
                '20' => [
                    'title' => 'Status Pulang / Kondisi',
                    'row' => 'status_pulang'
                ],
                '21' => [
                    'title' => 'Status Pemeriksaan',
                    'row' => 'status_periksa'
                ],
                '22' => [
                    'title' => 'Status Skrining',
                    'row' => 'status_skrining'
                ],
                '22' => [
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
        foreach ($data as $index => $value) {
            $newdata = [];
            $newdata['Tanggal Pendaftaran'] = DocoHelpers::convDateTime($value->tgl_pendaftaran);
            $newdata['Info Kunjungan'] = $value->no_pendaftaran. ' - '.$value->no_rekam_medik. ' - ' . (($value['namadepan']) ? $value['namadepan'] . " " : '') . $value->nama_pasien;
            if(isset($advancedFilters['carabayar_id']) && !isset($header['Cara Bayar'])){
                $header['Cara Bayar'] = $value->carabayar_nama;
            }
            if(isset($advancedFilters['penjamin_id']) && !isset($header['Penjamin'])){
                $header['Penjamin'] = $value->penjamin_nama;
            }
            if(isset($advancedFilters['ruangan_id']) && !isset($header['Ruangan'])){
                $header['Ruangan'] = $value->ruangan_nama;
            }
            if(isset($advancedFilters['pegawai_id']) && !isset($header['Dokter'])){
                $header['Dokter'] = $value->nama_pegawai;
            }
            if(isset($advancedFilters['status_skrining']) && !isset($header['status_skrining'])){
                $header['Status Skrining'] = $value->status_skrining;
            }
            foreach ($keyheader as $k => $v) {
                if($v['row'] == 'carabayar_penjamin'){
                    $newdata[$v['title'] ] = $value->carabayar_nama . ' / ' . $value->penjamin_nama;
                }else if($v['row'] == 'status_pulang'){
                    if($value->carakeluar_nama != '' && $value->kondisikeluar_nama){
                        $statusPulang = $value->carakeluar_nama . ' / ' . $value->kondisikeluar_nama;
                    }elseif(($value->carakeluar_nama != '') || ($value->kondisikeluar_nama != '')){
                        if($value->carakeluar_nama != ''){
                            $statusPulang = $value['carakeluar_nama'];
                        }else{
                            $statusPulang = $value['kondisikeluar_nama'];
                        }
                    }
                    else{
                        $statusPulang = '';
                    } 
                    $newdata[$v['title']] = $statusPulang;
                } else if ($v['row'] == 'no_identitas_pasien' && $v['title'] == 'NIK') {
                    $newdata[$v['title']] = ($value['jenisidentitas'] == 'KTP') ? $value->$v['row'] : '';
                }else{
                    $newdata[$v['title']] = $value->$v['row'];
                }
            }
            $result[] = $newdata;
        }
        $header['periode'] = $periode;
        $filePath = DocoHelpers::exportExcel('Laporan Kunjungan Rawat Jalan', $result, $header, [], $footer, [], true);
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

        $total_data = $this->getDataExcel($getData)->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $total_data,
            'countData' => $total_data,
            'sendToUrl' => 'lap-kunjungan-rawat-jalan/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('pendaftaran'),
        ], 'laporan_kunjungan_rawat_jalan',  'import_data_laporan_kunjungan_rawat_jalan');

        return [
            'totalPerPage' => $total_data,
            'unique_str' => $randString,
            'countData' => $total_data,
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
        $date = date('Y-m-d');
        $model = new LaporanKunjunganRawatJalanView;
        $query = $model::find();

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

       if(isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
            }

            if(isset($_GET['advanced-filter']['no_induk_kependudukan'])) {
                $no_induk_kependudukan = $_GET['advanced-filter']['no_induk_kependudukan'];
                $query->andWhere(['no_induk_kependudukan' => $no_induk_kependudukan]);
                unset($_GET['advanced-filter']['no_induk_kependudukan']);
            }

            if(isset($_GET['advanced-filter']['carabayar_id'])) {
                $carabayar_id = $_GET['advanced-filter']['carabayar_id'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_id']);
            }

            if(isset($_GET['advanced-filter']['penjamin_id'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_id'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_id']);
            }

            if(isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_id'];
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                unset($_GET['advanced-filter']['ruangan_id']);
            }

            if(isset($_GET['advanced-filter']['pegawai_id'])) {
                $pegawai_id = $_GET['advanced-filter']['pegawai_id'];
                $query->andWhere(['pegawai_id' => $pegawai_id]);
                unset($_GET['advanced-filter']['pegawai_id']);
            }

            if(isset($_GET['advanced-filter']['status_periksa'])) {
                $status_periksa_id = $_GET['advanced-filter']['status_periksa'];
                $query->andWhere(['status_periksa_id' => $status_periksa_id]);
                unset($_GET['advanced-filter']['status_periksa']);
            }

            if(isset($_GET['advanced-filter']['status_skrining'])) {
                $status_skrining = $_GET['advanced-filter']['status_skrining'];
                $query->andWhere(['status_skrining' => $status_skrining]);
                unset($_GET['advanced-filter']['status_periksa']);
            }

            if(isset($_GET['advanced-filter']['carakeluar_nama'])) {
                $carakeluar_id = $_GET['advanced-filter']['carakeluar_nama'];
                $query->andWhere(['carakeluar_id' => $carakeluar_id]);
                unset($_GET['advanced-filter']['carakeluar_nama']);
            }

            if(isset($_GET['advanced-filter']['toggle'])){
                $toggle = $_GET['advanced-filter']['toggle'];
                unset($_GET['advanced-filter']['toggle']);
            }
        }
        
        $query->andWhere(['NOT', [self::STATUS_PERIKSA => self::$look_exclude]]);
        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

}
