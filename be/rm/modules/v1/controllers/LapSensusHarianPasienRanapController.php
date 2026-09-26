<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use app\modules\v1\cache\Cache;
use app\modules\v1\models\LaporansensusharianriPasienmasukV;
use app\modules\v1\models\LaporansensusharianriPasienkeluarV;
use app\modules\v1\models\LaporansensusharianriPasienpindahanV;
use app\modules\v1\models\LaporansensusharianriPasienpindahkanV;
use app\modules\v1\models\LaporansensusharianriPasienmeninggalV;
use app\modules\v1\models\LaporansensusharianriPasienkeluarpindahrslainV;
use app\modules\v1\models\LaporansensusharianrekapitulasiFn;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\SensuspasienranapR;
use app\modules\v1\models\LaporansensusharianriSedangRanapv;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\GetHasilAkhirSensusFn;
use app\modules\v1\models\UploadForm;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\rabbitmq\SensusPasienRajalBgProcess;
use yii\web\UploadedFile;

class LapSensusHarianPasienRanapController extends DocoActiveController
{
    public $modelClass = '';
    const TITLE = 'Laporan Sensus Harian Pasien Rawat Inap';
    const TGL_ADMISI = 'tgl_admisi';
    const TGL_PENDAFTARAN = 'tgl_pendaftaran';
    const TGL_PASIEN_PLG = 'tgl_pasienplg';
    const TGL_PINDAH_KAMAR = 'tgl_pindahkamar';
    const GROUP_KONDISIKELUAR_MORE_48 = [5];

    public function verbs()
    {
        $verbs = parent::verbs();
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
        return $this->getDataPasienMasuk();
    }

    public function actionGenerateApi()
    {
        $ruangan = $this->getRuangan()->all();
        $kelas = $this->getKelaspelayanan()->all();
        $statusRanap = $this->getSatusRanap()->all();
        
        return [
            'ruangan' => $ruangan,
            'kelas' => $kelas,
            'status_pasien' => $statusRanap
        ];
    }

    private function getDataPasienMasuk()
    {
        try {
            $model   = $this->modelPasienMasuk(true);
            $query   = $this->modelPasienMasuk();
            
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
    
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_ADMISI])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_ADMISI]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter'][self::TGL_ADMISI]);
                }
            }
    
            $query->andWhere(['between', self::TGL_ADMISI, $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
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

    public function actionGetDataPasienKeluar()
    {
        try {
            $model   = $this->modelPasienKeluar(true);
            $query   = $this->modelPasienKeluar();
            
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
    
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_PASIEN_PLG])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_PASIEN_PLG]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter'][self::TGL_PASIEN_PLG]);
                }
            }
    
            $query->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
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

    public function actionGetDataPasienPindahanDari()
    {
        try {
            $model   = $this->modelPasienPindahanDari(true);
            $query   = $this->modelPasienPindahanDari();
            
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
    
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_PINDAH_KAMAR])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_PINDAH_KAMAR]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter'][self::TGL_PINDAH_KAMAR]);
                }
            }
    
            $query->andWhere(['between', self::TGL_PINDAH_KAMAR, $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
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

    public function actionGetDataPasienPindahanKe()
    {
        try {
            $model   = $this->modelPasienPindahanKe(true);
            $query   = $this->modelPasienPindahanKe();
            
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
    
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_PINDAH_KAMAR])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_PINDAH_KAMAR]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter'][self::TGL_PINDAH_KAMAR]);
                }
            }
    
            $query->andWhere(['between', self::TGL_PINDAH_KAMAR, $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
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

    public function actionGetDataPasienMeninggal()
    {
        try {
            $model   = $this->modelPasienMeninggal(true);
            $query   = $this->modelPasienMeninggal();
            
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
    
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_PASIEN_PLG])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_PASIEN_PLG]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter'][self::TGL_PASIEN_PLG]);
                }
            }
    
            $query->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
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

    public function actionGetDataPasienKeluarRujuk()
    {
        try {
            $model   = $this->modelPasienKeluarRujuk(true);
            $query   = $this->modelPasienKeluarRujuk();
            
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
    
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_PASIEN_PLG])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_PASIEN_PLG]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter'][self::TGL_PASIEN_PLG]);
                }
            }
    
            $query->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
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

    public function actionGetDataRekapitulasi()
    {
        try {
            $start   = date('Y-m-d');
            $end     = date('Y-m-d');
            $ruangan = null;
            $kelaspelayanan = null;
            $statusRanap = null;
            $result = [];

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_ADMISI])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_ADMISI]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d', strtotime($explode[0]));
                        $end = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter'][self::TGL_ADMISI]);
                }

                if(isset($_GET['advanced-filter']['kelaspelayanan_id'])) {
                    $kelaspelayanan = (int) $_GET['advanced-filter']['kelaspelayanan_id'];
                    unset($_GET['advanced-filter']['kelaspelayanan_id']);
                }

                if(isset($_GET['advanced-filter']['ruangan_id'])) {
                    $ruangan = (int) $_GET['advanced-filter']['ruangan_id'];
                    unset($_GET['advanced-filter']['ruangan_id']);
                }

                if(isset($_GET['advanced-filter']['status_ranap_id'])) {
                    $statusRanap = (int) $_GET['advanced-filter']['status_ranap_id'];
                    unset($_GET['advanced-filter']['status_ranap_id']);
                }
            }

            $result = $this->generateRekapitulasiData($start, $end, $kelaspelayanan, $ruangan, false, $statusRanap);

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
    
    private function modelPasienMasuk($new = false)
    {
        if($new) {
            return new LaporansensusharianriPasienmasukV;
        } else {
            return LaporansensusharianriPasienmasukV::find();
        }
    }

    private function modelPasienKeluar($new = false)
    {
        if($new) {
            return new LaporansensusharianriPasienkeluarV;
        } else {
            return LaporansensusharianriPasienkeluarV::find();
        }
    }

    private function modelPasienSedangRanap($new = false)
    {
        if($new) {
            return new LaporansensusharianriSedangRanapv;
        } else {
            return LaporansensusharianriSedangRanapv::find();
        }
    }

    private function modelPasienPindahanDari($new = false)
    {
        if($new) {
            return new LaporansensusharianriPasienpindahanV;
        } else {
            return LaporansensusharianriPasienpindahanV::find();
        }
    }

    private function modelPasienPindahanKe($new = false)
    {
        if($new) {
            return new LaporansensusharianriPasienpindahkanV;
        } else {
            return LaporansensusharianriPasienpindahkanV::find();
        }
    }

    private function modelPasienMeninggal($new = false)
    {
        if($new) {
            return new LaporansensusharianriPasienmeninggalV;
        } else {
            return LaporansensusharianriPasienmeninggalV::find();
        }
    }

    private function modelRekapitulasiFn()
    {
        return LaporansensusharianrekapitulasiFn::find();
    }

    private function modelPasienKeluarRujuk($new = false)
    {
        if($new) {
            return new LaporansensusharianriPasienkeluarpindahrslainV;
        } else {
            return LaporansensusharianriPasienkeluarpindahrslainV::find();
        }
    }

    private function modelSensusPasienRanap($new = false)
    {
        if($new) {
            return new SensuspasienranapR;
        } else {
            return SensuspasienranapR::find();
        }
    }

    private function getRuangan()
    {
        return Ruangan::find()
        ->where(['is_deleted' => false, 'is_active' => true])
        ->orderBy(['ruangan_nama' => SORT_ASC]);
    }

    private function getKelaspelayanan()
    {
        return KelasPelayanan::find()
        ->where(['is_deleted' => false, 'is_active' => true])
        ->orderBy(['kelaspelayanan_nama' => SORT_ASC]);
    }
    
    private function getSatusRanap()
    {
        return Lookup::find()
        ->where(['in', 'lookup_id', [441,487]])
        ->orderBy(['lookup_name' => SORT_ASC]);
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data table
    * @attribute #title# => Judul
    * @attribute #kelasNama# => Nama Kelas
    * @attribute #ruanganNama# => Nama Ruangan
    * @attribute #periode# => Periode
    * @attribute #nama_rs# => Nama RS
    * @attribute #lokasi# => Lokasi
    * @attribute #printed_by# => Print By
    * @attribute #printed_date# => Print Date
    */
    public function actionExportPdf()
    {
        try {
            ini_set('memory_limit','-1');
            ini_set('max_execution_time', 300);
    
            $request = Yii::$app->request->get();
            $title = self::TITLE;
            $kelaspelayanan = '';
            $ruangan = '';
            $statusRanap = '';
            $nama_pegawai = '';
            $ruanganNama = '-';
            $kelasNama = '-';
            $statusRanapNama = '-';
            $tmp = [];
            $data = [];
    
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
    
            if(isset($request['advanced-filter']['tgl_awal']) && isset($request['advanced-filter']['tgl_akhir'])) {
                $start = date('Y-m-d 00:00:00', strtotime($request['advanced-filter']['tgl_awal']));
                $end = date('Y-m-d 23:59:00', strtotime($request['advanced-filter']['tgl_akhir']));
            }

            if(isset($request['advanced-filter']['kelaspelayanan_id'])) {
                $kelaspelayanan = $request['advanced-filter']['kelaspelayanan_id'];
                $kelasNama = KelasPelayanan::findOne($kelaspelayanan)->kelaspelayanan_nama;
            }

            if(isset($request['advanced-filter']['ruangan_id'])) {
                $ruangan = $request['advanced-filter']['ruangan_id'];
                $ruanganNama = Ruangan::findOne($ruangan)->ruangan_nama;
            }

            if(isset($request['advanced-filter']['pegawai'])) {
                $nama_pegawai = $request['advanced-filter']['pegawai'];
            }

            if(isset($request['advanced-filter']['status_ranap_id'])) {
                $statusRanap = $request['advanced-filter']['status_ranap_id'];
                $statusRanapNama = Lookup::findOne($statusRanap)->lookup_name;
            }

            $data = $this->generateData($start, $end, $kelaspelayanan, $ruangan, false, $statusRanap);
            $profile = $this->getProfileRs();
            $kota = $profile['kota'];
            $namaRs = $profile['namaRs'];

            $print = new DocoPrint();
            error_reporting(0); 
            $print->attributes = [
                '#title#' => $title,
                '#periode#' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
                '#kelasNama#' => $kelasNama,
                '#ruanganNama#' => $ruanganNama,
                '#statusRanapNama#' => $statusRanapNama,
                '#lokasi#' => $kota. ', ' .date('d M Y'),
                '#nama_rs#' => !empty($namaRs) ? $namaRs : '-',
                '#printed_by#' => $nama_pegawai,
                '#printed_date#' => date('d/m/Y g:i A'),
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $data,
                ]),
            ];
            $print->Output();
        } catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSyncExportExcelRabbitmq()
    {
        try {
            
            
            $request = Yii::$app->request->get();
            $randString = ArrayHelper::getValue($request, 'randString');
            $tgl_pendaftaran = ArrayHelper::getValue($request, 'tgl_pendaftaran');
            $kelas = ArrayHelper::getValue($request, 'kelas');
            $ruangan = ArrayHelper::getValue($request, 'ruangan');
            $statusperiksa = ArrayHelper::getValue($request, 'statusperiksa');

            (new SensusPasienRajalBgProcess())->send([
                'unique_str' => $randString,
                'filter' => $request,
                'totalPerPage' => 1,
                'countData' => 1,
                'sendToUrl' => 'lap-sensus-harian-pasien-ranap/drop-file',
                'base_uri' => Yii::$app->docoRest->getBaseUri('rm'),
            ], 'laporan_sensus_harian_ranap');

            return [
                'totalPerPage' => 1,
                'unique_str' => $randString,
                'countData' => 1,
            ];
            
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
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

    public function actionExportExcel()
    {
        // try {
            ini_set('memory_limit','-1');
            ini_set('max_execution_time', 300);
    
            $request = Yii::$app->request->get();
            
            $kelaspelayanan = '';
            $ruangan = '';
            $statusRanap = '';
            $kelasNama = '';
            $ruanganNama = '';
            $statusRanapNama = '';
            $result = [];
            $tmp = [];
            $subTitle = [];
            $SheetName = [];
            
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');

            if(isset($request['advanced-filter']['tgl_awal']) && isset($request['advanced-filter']['tgl_akhir'])) {
                $start = date('Y-m-d 00:00:00', strtotime($request['advanced-filter']['tgl_awal']));
                $end = date('Y-m-d 23:59:00', strtotime($request['advanced-filter']['tgl_akhir']));
            }

            if(isset($request['advanced-filter']['kelaspelayanan_id'])) {
                $kelaspelayanan = $request['advanced-filter']['kelaspelayanan_id'];
                $kelasNama = KelasPelayanan::findOne($kelaspelayanan)->kelaspelayanan_nama;
            }

            if(isset($request['advanced-filter']['ruangan_id'])) {
                $ruangan = $request['advanced-filter']['ruangan_id'];
                $ruanganNama = Ruangan::findOne($ruangan)->ruangan_nama;
            }

            if(isset($request['advanced-filter']['status_ranap_id'])) {
                $statusRanap = $request['advanced-filter']['status_ranap_id'];
                $statusRanapNama = Lookup::findOne($statusRanap)->lookup_name;
            }

            $tmp = $this->generateData($start, $end, $kelaspelayanan, $ruangan, true, $statusRanap);
            $no = 0;
            foreach ($tmp as $key => $value) {
                switch ($key) {
                    case 'dataMasuk':
                        $result[$no] = (!empty($tmp['dataMasuk']) ? $tmp['dataMasuk'] : [$this->modelPasienMasuk(true)->getAttributes()]);
                        $subTitle[$no] = 'PASIEN MASUK RAWAT INAP';
                        $SheetName[$no] = 'PASIEN MASUK';
                        break;
                    case 'dataSedangRanap':
                        $result[$no] = (!empty($tmp['dataSedangRanap']) ? $tmp['dataSedangRanap'] : [$this->modelPasienSedangRanap(true)->getAttributes()]);
                        $subTitle[$no] = 'PASIEN SEDANG DIRAWAT INAP';
                        $SheetName[$no] = 'PASIEN SEDANG DIRAWAT INAP';
                        break;
                    case 'dataKeluar':
                        $result[$no] = (!empty($tmp['dataKeluar']) ? $tmp['dataKeluar'] : [$this->modelPasienKeluar(true)->getAttributes()]);
                        $subTitle[$no] = 'PASIEN KELUAR RAWAT';
                        $SheetName[$no] = 'PASIEN KELUAR';
                        break;
                    case 'dataKeluarRujuk':
                        $result[$no] = (!empty($tmp['dataKeluarRujuk']) ? $tmp['dataKeluarRujuk'] : [$this->modelPasienKeluarRujuk(true)->getAttributes()]);
                        $subTitle[$no] = 'PASIEN KELUAR RUJUK RS LAIN';
                        $SheetName[$no] = 'PASIEN KELUAR RUJUK';
                        break;
                    case 'dataPindahanDari':
                        $result[$no] = (!empty($tmp['dataPindahanDari']) ? $tmp['dataPindahanDari'] : [$this->modelPasienPindahanDari(true)->getAttributes()]);
                        $subTitle[$no] = 'PASIEN PINDAHAN DARI INSTALASI / RUANGAN LAIN';
                        $SheetName[$no] = 'PINDAHAN DARI';
                        break;
                    case 'dataPindahanKe':
                        $result[$no] = (!empty($tmp['dataPindahanKe']) ? $tmp['dataPindahanKe'] : [$this->modelPasienPindahanKe(true)->getAttributes()]);
                        $subTitle[$no] = 'PASIEN PINDAHAN KE INSTALASI / RUANGAN LAIN';
                        $SheetName[$no] = 'PINDAHAN KE';
                        break;
                    case 'dataMeninggal':
                        $result[$no] = (!empty($tmp['dataMeninggal']) ? $tmp['dataMeninggal'] : [$this->modelPasienMeninggal(true)->getAttributes()]);
                        $subTitle[$no] = 'PASIEN MENINGGAL';
                        $SheetName[$no] = 'PASIEN MENINGGAL';
                        break;
                    case 'dataRekapitulasi':
                        $result[$no] = $tmp['dataRekapitulasi'];
                        $subTitle[$no] = 'REKAPITULASI';
                        $SheetName[$no] = 'REKAPITULASI';
                        break;
                    default:
                        continue;
                        break;
                }
                $no++;
            }
            $header = [
                'Periode' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
                'Ruangan' => $ruanganNama,
                'Kelas' => $kelasNama,
                'Status Pasien' => $statusRanapNama
            ];
            $filePath = DocoHelpers::exportExcelMultiSheet(self::TITLE, $result, $header,  array("uploadPath" => "./uploads", "skipIncrement" => false, "subHeader" => $subTitle),[],[],true, $SheetName);
            // $filePath = DocoHelpers::exportExcel(self::TITLE, $tmp['dataKeluar'], $header,  array("uploadPath" => "./uploads", "skipIncrement" => false),[],[],true);
            // return $filePath;

            $filePath->save('php://output');
            die;

        // } catch (\Exception $e) {
        //     \Yii::$app->response->statusCode = 500;
        //     return ['message' => $e->getMessage()];
        // }
    }

    private function generatePasienSebelumnyaNew($start, $kelaspelayanan=null, $ruangan =null)
    {
        $sql = "
                SELECT 
                    SUM(pasien_akhir) AS pasien_hari_sebelumnya
                FROM sensuspasienranap_v
                WHERE id IN (
                    SELECT MAX(id) FROM sensuspasienranap_v 
                    WHERE tgl_sensus < :date 
                ";
        if($kelaspelayanan == null && $ruangan == null) {
            $sql .= "
                GROUP BY ruangan_id, kelaspelayanan_id)
            ";
        } else if($kelaspelayanan != null && $ruangan == null) {
            $sql .= "
                AND kelaspelayanan_id = {$kelaspelayanan}
                GROUP BY ruangan_id, kelaspelayanan_id)
            ";
        } else if ($kelaspelayanan == null && $ruangan != null) {
            $sql .= "
                AND ruangan_id = {$ruangan}
                GROUP BY ruangan_id, kelaspelayanan_id)
            ";
        } else {
            $sql .= "
                AND kelaspelayanan_id = {$kelaspelayanan}
                AND ruangan_id = {$ruangan}
                GROUP BY ruangan_id, kelaspelayanan_id)
            ";
        }

        return Yii::$app->db->createCommand($sql)
        ->bindValue(':date', $start)
        ->queryOne();
        
    }

    private function generateRekapitulasiData($start, $end, $kelaspelayanan = null, $ruangan = null, $isExcel = false, $status = null)
    {
        $result = [];
        $data = [];
        $tmp = [];
        $isCurrentDate = true;
        $currentDate =  date('Y-m-d', strtotime('NOW'));

        if($currentDate != $start) {
            $isCurrentDate = false;
        }

        if(!$kelaspelayanan) {
            $kelaspelayanan = null;
        }
        if(!$ruangan) {
            $ruangan = null;
        }
        if(!$status) {
            $status = null;
        }

        // if($isCurrentDate) {
            $model = Yii::$app->db->createCommand('
            SELECT * FROM laporansensusharianri_rekapitulasi_fn(:firstdate,:lastdate, :ruangan_id,:kelaspelayanan_id, :status)
            ')
            ->bindParam(':firstdate', $start)
            ->bindParam(':lastdate', $end)
            ->bindParam(':ruangan_id', $ruangan)
            ->bindParam(':kelaspelayanan_id', $kelaspelayanan)
            ->bindParam(':status', $status);

            $query = $model->queryAll();
            $result = array_shift($query);
            // $getJmlAwal = $this->generatePasienSebelumnyaNew($start, $kelaspelayanan, $ruangan);
            $getJmlAwal =  (new GetHasilAkhirSensusFn(['extParam'=>[date('Y-m-d', strtotime("-1 day", strtotime($start)))]]))->find()->asArray()->one();
            $jmlAwal = isset($getJmlAwal['hasil_sensus']) ? $getJmlAwal['hasil_sensus'] : 0;
            unset($result['vtanggal']);
            unset($result['pasien_hari_sebelumnya']);
            unset($result['jml_123']);
            unset($result['jml_567']);
            unset($result['keluar_meninggalkur48']);
            unset($result['keluar_meninggalleb48']);
            unset($result['pasien_akhir']);

            //get konfig
            // $queryKonfig = (new \yii\db\Query())
            // ->select([
            //     'set_tgl_sensus'
            // ])
            // ->from('konfigsystem_k');
            // $konfig = $queryKonfig->one();

            // $tgl_sebelumnya = date('Y-m-d', strtotime('-1 days', strtotime($end))); //kurang tanggal sebanyak 6 hari

            //rujuk rs lain
            // $query2 = (new \yii\db\Query())
            // ->select([
            //     'COUNT(*) as jumlah'
            // ])
            // ->from('pasienadmisi_t')
            // ->leftjoin("pasienpulang_t", "pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id")
            // ->where(['between','date(pasienpulang_t.tglpasienpulang)', $konfig['set_tgl_sensus'], $tgl_sebelumnya]);
            
            // $query2->andWhere(['and','pasienpulang_t.pasienadmisi_id is not null']);
            // $query2->andWhere(['pasienpulang_t.carakeluar_id' => 2]);
            // $query2->andWhere(['pasienpulang_t.kondisikeluar_id' => 3]);
            // if ($kelaspelayanan) {
            //     $query2->andWhere(['pasienadmisi_t.kelaspelayanan_id' => $kelaspelayanan]);
            // }

            // if ($ruangan) {
            //     $query2->andWhere(['pasienpulang_t.ruanganakhir_id' => $ruangan]);
            // }
            // $model2 = $query2->one();

            // $jmlAwal = $jmlAwal - $model2['jumlah'];

            foreach ($result as $key => &$value) {
                $value += array_sum(array_column($query, $key));
            }
            
            if(!isset($result['pasien_hari_sebelumnya'])){
                $result['pasien_hari_sebelumnya'] = (int) ($jmlAwal == null) ? 0 : $jmlAwal;
            }

            if(!isset($result['jml_123'])){
                // $result['jml_123'] = (int) $jmlAwal + $result['pasien_masuk'] + $result['pasien_pindahan'];
                $result['jml_123'] = (int) $jmlAwal + $result['pasien_masuk']; //pasien pindahan jgn di hitung
            }

            if(!isset($result['jml_567'])){
                // $result['jml_567'] = (int) $result['keluar_hidup'] + $result['keluar_dipindahkan'] + $result['keluar_meninggaljml'] + $result['keluar_rujukrslain'];
                $result['jml_567'] = (int) $result['keluar_hidup'] + $result['keluar_meninggaljml'] + $result['keluar_rujukrslain']; // pasien pindah kamar jgn di hitung
            }

            if(!isset($result['pasien_akhir'])){
                $result['pasien_akhir'] = (int) $result['jml_123'] - $result['jml_567'];
            }
        // } else {
        //     $result = $this->generatePasienSebelumnya($start, $kelaspelayanan, $ruangan, false);
        // }

        if(!empty($result)){
            foreach($result as $key => $val) {
                if(!$isExcel) {
                    $tmp['no'] = $this->generateOrder($key);
                }
                $tmp['order'] =  $this->generateOrder($key);
                $tmp['keterangan'] = $this->generateText($key);
                $tmp['jumlah'] = $val;
                $data[] = $tmp;
            }
        }

        ArrayHelper::multisort($data, ['order'], [SORT_ASC]);
        return $data;
    }

    private function generateText($text) 
    {
        switch ($text) {
            case 'pasien_masuk':
                return 'Pasien Masuk Perawatan';
                break;
            case 'jml_123':
                return 'Jumlah Pasien Sedang dirawat (1+2)';
                break;
            case 'keluar_hidup':
                return 'Pasien Keluar Hidup';
                break;
            case 'keluar_dipindahkan':
                return 'Pasien Dipindahkan';
                break;
            case 'keluar_meninggaljml':
                return 'Pasien Meninggal';
                break;
            case 'keluar_rujukrslain':
                return 'Pasien Keluar RS rujuk lain';
                break;
            case 'jml_567':
                return 'Jumlah Pasien Keluar Rawat(5+7+8)';
                break;
            case 'pasien_akhir':
                return 'Jumlah Sisa (4-9)';
                break;
            default:
                return ucwords(str_replace("_", " ", $text));
                break;
        }
    }

    private function generateOrder($text)
    {
        switch ($text) {
            case 'pasien_hari_sebelumnya':
                return 1;
                break;
            case 'pasien_masuk':
                return 2;
                break;
            case 'pasien_pindahan':
                return 3;
                break;
            case 'jml_123':
                return 4;
                break;
            case 'keluar_hidup':
                return 5;
                break;
            case 'keluar_dipindahkan':
                return 6;
                break;
            case 'keluar_meninggaljml':
                return 7;
                break;
            case 'keluar_rujukrslain':
                return 8;
                break;
            case 'jml_567':
                return 9;
                break;
            case 'pasien_akhir':
                return 10;
                break;
            default:
                return 0;
                break;
        }
    }

    private function generateData($start, $end, $kelaspelayanan = null, $ruangan = null, $isExcel = false, $status = null)
    {
        $dataMasuk = $dataKeluar = $dataKeluarRujuk = $dataPindahanDari = $dataPindahanKe = $dataMeninggal = $dataRekapitulasi = [];

        $dataMasukExcel = $dataKeluarExcel = $dataKeluarRujukExcel = $dataPindahanDariExcel = $dataPindahanKeExcel = $dataMeninggalExcel = $dataRekapitulasiExcel = $dataSedangRanapExcel = [];

        $modelMasuk = $this->modelPasienMasuk();
        $modelKeluar = $this->modelPasienKeluar();
        $modelKeluarRujuk = $this->modelPasienKeluarRujuk();
        $modelPindahanDari = $this->modelPasienPindahanDari();
        $modelPindahanKe = $this->modelPasienPindahanKe();
        $modelMeninggal = $this->modelPasienMeninggal();
        $modelSedangRanap = $this->modelPasienSedangRanap();
        $tmp = [];

        /* move up to avoid multiple OR condition */
        $modelSedangRanap->andWhere(['and', self::TGL_PASIEN_PLG.' '.new \yii\db\Expression('is null'), ['<=', self::TGL_ADMISI, $start]]);
        $modelSedangRanap->orWhere(['and', self::TGL_PASIEN_PLG.' '.new \yii\db\Expression('is not null'), ['>=', self::TGL_PASIEN_PLG, $start], ['<=', self::TGL_ADMISI, $start]]);

        if($kelaspelayanan) {
            $modelMasuk->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelKeluar->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelKeluarRujuk->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelPindahanDari->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelPindahanKe->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelMeninggal->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
            $modelSedangRanap->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
        }

        if($ruangan) {
            $modelMasuk->andWhere(['ruangan_id' => $ruangan]);
            $modelKeluar->andWhere(['ruangan_id' => $ruangan]);
            $modelKeluarRujuk->andWhere(['ruangan_id' => $ruangan]);
            $modelPindahanDari->andWhere(['ruangan_id' => $ruangan]);
            $modelPindahanKe->andWhere(['ruangan_id' => $ruangan]);
            $modelMeninggal->andWhere(['ruangan_id' => $ruangan]);
            $modelSedangRanap->andWhere(['ruangan_id' => $ruangan]);
        }

        if($status) {
            $modelMasuk->andWhere(['status_ranap_id' => $status]);
            $modelKeluar->andWhere(['status_ranap_id' => $status]);
            $modelKeluarRujuk->andWhere(['status_ranap_id' => $status]);
            $modelPindahanDari->andWhere(['status_ranap_id' => $status]);
            $modelPindahanKe->andWhere(['status_ranap_id' => $status]);
            $modelMeninggal->andWhere(['status_ranap_id' => $status]);
            $modelSedangRanap->andWhere(['status_ranap_id' => $status]);
        }

        $dataMasuk = $modelMasuk->andWhere(['between', self::TGL_ADMISI, $start, $end])->orderBy([self::TGL_ADMISI => SORT_ASC])->asArray()->all();
        $dataKeluar = $modelKeluar->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end])->orderBy([self::TGL_PASIEN_PLG => SORT_ASC])->asArray()->all();
        $dataKeluarRujuk = $modelKeluarRujuk->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end])->orderBy([self::TGL_PASIEN_PLG => SORT_ASC])->asArray()->all();
        $dataPindahanDari = $modelPindahanDari->andWhere(['between', self::TGL_PINDAH_KAMAR, $start, $end])->orderBy([self::TGL_PINDAH_KAMAR => SORT_ASC])->asArray()->all();
        $dataPindahanKe = $modelPindahanKe->andWhere(['between', self::TGL_PINDAH_KAMAR, $start, $end])->orderBy([self::TGL_PINDAH_KAMAR => SORT_ASC])->asArray()->all();
        $dataMeninggal = $modelMeninggal->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end])->orderBy([self::TGL_PASIEN_PLG => SORT_ASC])->asArray()->all();
        $dataRekapitulasi = $this->generateRekapitulasiData($start, $end, $kelaspelayanan, $ruangan, $isExcel, $status);
        $dataSedangRanap = $modelSedangRanap->orderBy([self::TGL_ADMISI => SORT_ASC])->asArray()->all();
        //$dataSedangRanap = $modelSedangRanap->andWhere(['between', self::TGL_ADMISI, $start, $end])->orderBy([self::TGL_ADMISI => SORT_ASC])->asArray()->all();

        if(!empty($dataMasuk)) {
            foreach ($dataMasuk as $k => $val) {
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if($isExcel) {
                    $tmpMsk['nama_pasien'] = $val['nama_pasien'];
                    $tmpMsk['norm'] = $val['no_rekam_medik'];
                    $tmpMsk['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpMsk['ruangan'] = $val['ruangan_nama'];
                    $tmpMsk['kamar'] = $val['kamar'];
                    $tmpMsk['bed'] = $val['tempattidur'];
                    $tmpMsk['jaminan'] = $val['penjamin_nama'];
                    $tmpMsk['dokter'] = $val['nama_dokter'];
                    $tmpMsk['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $dataMasukExcel[] = $tmpMsk;
                } else {
                    $dataMasuk[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if(!empty($dataSedangRanap)) {
            foreach ($dataSedangRanap as $k => $val) {
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if($isExcel) {
                    $tmpSdgRnp['nama_pasien'] = $val['nama_pasien'];
                    $tmpSdgRnp['norm'] = $val['no_rekam_medik'];
                    $tmpSdgRnp['jeniskelamin_nama'] = $val['jeniskelamin_nama'];
                    $tmpSdgRnp['no_telepon_pasien'] = $val['no_telepon_pasien'];
                    $tmpSdgRnp['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpSdgRnp['ruangan'] = $val['ruangan_nama'];
                    $tmpSdgRnp['kamar'] = $val['kamar'];
                    $tmpSdgRnp['tempattidur'] = $val['tempattidur'];
                    $tmpSdgRnp['penjamin_nama'] = $val['penjamin_nama'];
                    $tmpSdgRnp['nama_dokter'] = $val['nama_dokter'];
                    $tmpSdgRnp['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpSdgRnp['tgl_masukkamar'] = $val['tgl_masukkamar'];

                    $waktu = DocoHelpers::getDiffDateTime($val['tgl_masukkamar_1'], date('Y-m-d H:i:s'));
                    $tmpSdgRnp['jam_rawat'] = $waktu['jam'];
                    $tmpSdgRnp['lama_rawat'] = $waktu['hari'];
                    $dataSedangRanapExcel[] = $tmpSdgRnp;
                } else {
                    $waktu = DocoHelpers::getDiffDateTime($val['tgl_masukkamar_1'], date('Y-m-d H:i:s'));
                    $dataSedangRanap[$k]['jam_rawat'] = $waktu['jam'];
                    $dataSedangRanap[$k]['lama_rawat'] = $waktu['hari'];
                    $dataSedangRanap[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if(!empty($dataKeluar)) {
            foreach($dataKeluar as $k => $val) {
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if($isExcel) {
                    $tmpKlr['nama_pasien'] = $val['nama_pasien'];
                    $tmpKlr['norm'] = $val['no_rekam_medik'];
                    $tmpKlr['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpKlr['ruangan'] = $val['ruangan_nama'];
                    $tmpKlr['kamar'] = $val['kamar'];
                    $tmpKlr['bed'] = $val['tempattidur'];
                    $tmpKlr['jaminan'] = $val['penjamin_nama'];
                    $tmpKlr['dokter'] = $val['nama_dokter'];
                    $tmpKlr['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpKlr['tgl_msk_ruangan'] = $val['tgl_masukkamar'];
                    $tmpKlr['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    $tmpKlr['hari'] = $val['lama_rawat'];
                    $dataKeluarExcel[] = $tmpKlr;
                } else {
                    $dataKeluar[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    $dataKeluar[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if(!empty($dataKeluarRujuk)) {
            foreach($dataKeluarRujuk as $k => $val) {
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if($isExcel) {
                    $tmpKlrRjk['nama_pasien'] = $val['nama_pasien'];
                    $tmpKlrRjk['norm'] = $val['no_rekam_medik'];
                    $tmpKlrRjk['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpKlrRjk['ruangan'] = $val['ruangan_nama'];
                    $tmpKlrRjk['kamar'] = $val['kamar'];
                    $tmpKlrRjk['bed'] = $val['tempattidur'];
                    $tmpKlrRjk['jaminan'] = $val['penjamin_nama'];
                    $tmpKlrRjk['dokter'] = $val['nama_dokter'];
                    $tmpKlrRjk['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpKlrRjk['tgl_msk_ruangan'] = $val['tgl_masukkamar'];
                    $tmpKlrRjk['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    $tmpKlrRjk['hari'] = $val['lama_rawat'];
                    $tmpKlrRjk['RS_tujuan'] = $val['rumahsakit_rujukan'];
                    $dataKeluarRujukExcel[] = $tmpKlrRjk;
                } else {
                    $dataKeluarRujuk[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    $dataKeluarRujuk[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if(!empty($dataPindahanDari)) {
            foreach($dataPindahanDari as $k => $val) {
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if($isExcel) {
                    $tmpPindahDari['nama_pasien'] = $val['nama_pasien'];
                    $tmpPindahDari['norm'] = $val['no_rekam_medik'];
                    $tmpPindahDari['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpPindahDari['ruangan'] = $val['ruangan_skrg'];
                    $tmpPindahDari['kamar'] = $val['kamar_dari'];
                    $tmpPindahDari['bed'] = $val['tempattidur_dari'];
                    $tmpPindahDari['jaminan'] = $val['penjamin_nama'];
                    $tmpPindahDari['ruangan_asal'] = $val['ruangan_dari'];
                    $tmpPindahDari['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $dataPindahanDariExcel[] = $tmpPindahDari;
                } else {
                    $dataPindahanDari[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                }
            }
        }

        if(!empty($dataPindahanKe)) {
            foreach($dataPindahanKe as $k => $val) {
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                // $checkTime = date('H:i:s', strtotime($val['tgl_masukkamar_1']));
                // if($checkTime === '00:00:00') {
                //     $tglMasukKamar = explode(" ",$val['tgl_masukkamar_1']);
                //     $tglMasukKamar[1] = $val['jam_masukkamar'];
                //     $val['tgl_masukkamar_1'] = implode($tglMasukKamar);
                // }
                if($isExcel) {
                    $tmpPindahKe['nama_pasien'] = $val['nama_pasien'];
                    $tmpPindahKe['norm'] = $val['no_rekam_medik'];
                    $tmpPindahKe['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpPindahKe['ruangan'] = $val['ruangan_skrg'];
                    $tmpPindahKe['kamar'] = $val['kamar_skrg'];
                    $tmpPindahKe['bed'] = $val['tempattidur_skrg'];
                    $tmpPindahKe['jaminan'] = $val['penjamin_nama'];
                    $tmpPindahKe['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpPindahKe['tanggal_masuk'] = $val['tgl_masukkamar'];
                    $tmpPindahKe['lama_rawat_(Jam)'] = 0;
                    $tmpPindahKe['hari'] = $val['lama_rawat'];
                    $tmpPindahKe['kamar_tujuan'] = $val['kamar_ke'];
                    $tmpPindahKe['dokter'] = $val['dokter_admisi'];
                    if(empty($val['lama_rawat'])) {
                        $lamaRawat =  $this->generateJamRawat($val['tgl_masukkamar_1']);
                        $tmpPindahKe['lama_rawat'] = $lamaRawat;
                        if($lamaRawat == 0){
                            $tmpPindahKe['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, null, true);
                            $tmpPindahKe['lama_rawat'] = 1;
                        } else {
                            $tmpPindahKe['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, $lamaRawat, true);
                        }
                    } else {
                        $tmpPindahKe['lama_rawat_(Jam)'] = $this->generateJamRawat($val['tgl_masukkamar_1'],$val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    }
                    $dataPindahanKeExcel[] = $tmpPindahKe;
                } else {
                    $dataPindahanKe[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    if(empty($val['lama_rawat'])) {
                        $lamaRawat =  $this->generateJamRawat($val['tgl_masukkamar_1']);
                        $dataPindahanKe[$k]['lama_rawat'] = $lamaRawat;
                        if($lamaRawat == 0){
                            $dataPindahanKe[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, null, true);
                            $dataPindahanKe[$k]['lama_rawat'] = 1;
                        } else {
                            $dataPindahanKe[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], null, $lamaRawat, true);
                        }
                    } else {
                        $dataPindahanKe[$k]['jam_rawat'] = $this->generateJamRawat($val['tgl_masukkamar_1'], $val['tgl_keluarkamar'], $val['lama_rawat'], true);
                    }
                }
            }
        }

        if(!empty($dataMeninggal)) {
            foreach($dataMeninggal as $k => $val) {
                if(!empty($val['diagnosa_nama'])){
                    $diagnosa = json_decode($val['diagnosa_nama'], true);
                }
                if($isExcel){
                    $tmpMeninggal['nama_pasien'] = $val['nama_pasien'];
                    $tmpMeninggal['norm'] = $val['no_rekam_medik'];
                    $tmpMeninggal['kelas'] = $val['kelaspelayanan_nama'];
                    $tmpMeninggal['ruangan'] = $val['ruangan_nama'];
                    $tmpMeninggal['kamar'] = $val['kamar'];
                    $tmpMeninggal['bed'] = $val['tempattidur'];
                    $tmpMeninggal['jaminan'] = $val['penjamin_nama'];
                    $tmpMeninggal['dokter'] = $val['nama_dokter'];
                    $tmpMeninggal['diagnosis'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $tmpMeninggal['tanggal_masuk'] = $val['tgl_masukkamar'];
                    $tmpMeninggal['<_48'] = 0;
                    $tmpMeninggal['>_48'] = 0;
                    if($val['lama_rawat'] <= 2) {
                        $tmpMeninggal['<_48'] = 1;
                    } else {
                        $tmpMeninggal['>_48'] = 1;
                    }
                    $dataMeninggalExcel[] = $tmpMeninggal;
                } else {
                    $dataMeninggal[$k]['diagnosa_nama'] = isset($diagnosa['text']) ? $diagnosa['text'] : "";
                    $dataMeninggal[$k]['lama_rawat_leb48']= 0;
                    $dataMeninggal[$k]['lama_rawat_kur48']= 0;
                    if($val['lama_rawat'] <= 2) {
                        $dataMeninggal[$k]['lama_rawat_kur48'] = 1;
                    } else {
                        $dataMeninggal[$k]['lama_rawat_leb48'] = 1;
                    }
                }
            }
        }

        return [
            'dataMasuk' => ($isExcel) ? $dataMasukExcel : $dataMasuk,
            'dataSedangRanap' => ($isExcel) ? $dataSedangRanapExcel : $dataSedangRanap,
            'dataKeluar' => ($isExcel) ? $dataKeluarExcel : $dataKeluar,
            'dataKeluarRujuk' => ($isExcel) ? $dataKeluarRujukExcel : $dataKeluarRujuk,
            'dataPindahanDari' => ($isExcel) ? $dataPindahanDariExcel : $dataPindahanDari,
            'dataPindahanKe' => ($isExcel) ? $dataPindahanKeExcel : $dataPindahanKe,
            'dataMeninggal' => ($isExcel) ? $dataMeninggalExcel : $dataMeninggal,
            'dataRekapitulasi' => $dataRekapitulasi,
        ];
    }

    private function generateJamRawat($start , $end = null, $lamaRawat = null, $toHours = false)
    {
        $dateOne = new \DateTime($start);

        if($end) {
            $dateTwo = new \DateTime($end);
        } else {
            $dateTwo = new \DateTime();
        }
        $tmpResult = ($dateTwo->diff($dateOne));

        if($lamaRawat) {
            $result = ($tmpResult->d * 24) + $tmpResult->h;
        } else {
            if($toHours){
                $result =$tmpResult->h;
            } else {
                $result =$tmpResult->days;
            }
        }

        return $result;
    }

    private function getProfileRs()
    {
        $profilRs = Cache::getProfileRs();
        $kota = '-';
        $namaRs = '-';
        if (!empty($profilRs['nama_rumahsakit'])) {
            $namaRs = $profilRs['nama_rumahsakit'];
        }

        if (!empty($profilRs['kota'])) {
            if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
                $pattern = "KOTA ADM. ";
            }
            elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
                $pattern = "KAB. ADM. ";
            }
            elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
                $pattern = "KAB. ";
            }
            elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
                $pattern = "KOTA ";
            }

            $kota = str_replace($pattern,"", $profilRs['kota']);
        }
        
        return [
            'namaRs' => $namaRs,
            'kota' => $kota,
        ];
    }

    private function setDefault($item)
    {
        $output[] = array_map(function($item) { return null; }, $item);
        return $output;
    }

    private function generatePasienSebelumnya($start, $kelaspelayanan = null, $ruangan = null, $get_only_awal)
    {
        $data = $tmpData = [];
        $prev_date = date('Y-m-d', strtotime($start .' -1 day'));

        $prev_days = (int) date('j', strtotime($prev_date));
        $start_days = (int) date('j', strtotime($start));

        $bulan = date('m', strtotime($prev_date));
        $tahun = date('Y', strtotime($prev_date));
        $days = date('t', strtotime($prev_date));

        for ($i=1; $i <= $days; $i++) {
            $day = $i;

            if ($i < 10) {
                $day = (int) '0'.$i;
            }

            $tmpData[] = [
                'tgl_sensus' => date('Y-m-'.$day, strtotime($prev_date)),
                'vtanggal' => $day,
                'pasien_hari_sebelumnya' => 0,
                'pasien_masuk' => 0,
                'pasien_pindahan' => 0,
                'jml_123' => 0,
                'keluar_hidup' => 0,
                'keluar_dipindahkan' => 0,
                'keluar_meninggaljml' => 0,
                'keluar_meninggalkur48' => 0,
                'keluar_meninggalleb48' => 0,
                'jml_567' => 0,
                'pasien_akhir' => 0,
            ];
        }

        foreach ($tmpData as $key => $value) {
            $query = (new \yii\db\Query())
            ->select([
                'SUM(pasien_masuk) AS pasien_masuk',
                'SUM(pasien_pindahan) AS pasien_pindahan',
                'SUM(pasien_keluarhidup) AS pasien_keluarhidup',
                'SUM(pasien_keluardipindahkan) AS pasien_keluardipindahkan',
                'SUM(pasien_keluarmeniggalkur48) AS pasien_keluarmeniggalkur48',
                'SUM(pasien_keluarmeniggalleb48) AS pasien_keluarmeniggalleb48',
                'SUM(pasien_akhir) AS pasien_akhir'
            ])
            ->from('sensuspasienranap_v')
            ->where(['tgl_sensus' => $value['tgl_sensus']]);

            if ($kelaspelayanan) {
                $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan]);
                $query->addGroupBy('kelaspelayanan_id');
            }

            if ($ruangan) {
                $query->andWhere(['ruangan_id' => $ruangan]);
                $query->addGroupBy('ruangan_id');
            }

            $model = $query->one();

            if ($key == 0) {
                $awal = 0;
            } else {
                $awal = isset($tmpData[$key-1]['pasien_akhir']) ? $tmpData[$key-1]['pasien_akhir'] : 0;
            }

            $masuk = isset($model['pasien_masuk']) ? (int)$model['pasien_masuk'] : 0;
            $pindahan = isset($model['pasien_pindahan']) ? (int)$model['pasien_pindahan'] : 0;
            $klr_hidup = isset($model['pasien_keluarhidup']) ? (int)$model['pasien_keluarhidup'] : 0;
            $dipindahkan = isset($model['pasien_keluardipindahkan']) ? (int)$model['pasien_keluardipindahkan'] : 0;
            $meninggal_kur48jam = isset($model['pasien_keluarmeniggalkur48']) ? (int)$model['pasien_keluarmeniggalkur48'] : 0;
            $meninggal_leb48jam = isset($model['pasien_keluarmeniggalleb48']) ? (int)$model['pasien_keluarmeniggalleb48'] : 0;
            $jml_234 = $awal + $masuk + $pindahan;
            $meninggal_jml = $meninggal_kur48jam + $meninggal_leb48jam;
            $jml_678 = $klr_hidup + $dipindahkan + $meninggal_kur48jam + $meninggal_leb48jam;
            $pasien_akhir = $jml_234 - $jml_678;

            // if ($value['tgl_sensus'] <= date('Y-m-d')) {
                $tmpData[$key] = [
                    'vtanggal' => $value['vtanggal'],
                    'pasien_hari_sebelumnya' => $awal,
                    'pasien_masuk' => $masuk,
                    'pasien_pindahan' => $pindahan,
                    'jml_123' => $jml_234,
                    'keluar_hidup' => $klr_hidup,
                    'keluar_dipindahkan' => $dipindahkan,
                    'keluar_meninggaljml' => $meninggal_jml,
                    'keluar_meninggalkur48' => $meninggal_kur48jam,
                    'keluar_meninggalleb48' => $meninggal_leb48jam,
                    'jml_567' => $jml_678,
                    'pasien_akhir' => $pasien_akhir,
                ];
            // }
        }

        if(!empty($tmpData)) {
            foreach($tmpData as $k => $v) {
                if($get_only_awal) {
                    if($v['vtanggal'] == $prev_days) {
                        $data['pasien_hari_sebelumnya'] = $v['pasien_akhir'];
                    }
                } else {
                    if($v['vtanggal'] == $start_days) {
                        $data = [
                            'pasien_masuk' => $v['pasien_masuk'],
                            'pasien_pindahan' => $v['pasien_pindahan'],
                            'keluar_hidup' => $v['keluar_hidup'],
                            'keluar_dipindahkan' => $v['keluar_dipindahkan'],
                            'keluar_meninggaljml' => $v['keluar_meninggaljml'],
                            'pasien_hari_sebelumnya' => $v['pasien_hari_sebelumnya'],
                            'jml_123' => $v['jml_123'],
                            'jml_567' => $v['jml_567'],
                            'pasien_akhir' => $v['pasien_akhir'],
                        ];
                    }
                }
            }
        }

        return $data;
    }

    public function actionGetDataPasienSedangRanap()
    {
        try {
            $model   = $this->modelPasienSedangRanap(true);
            $query   = $this->modelPasienSedangRanap();

            $start   = date('Y-m-d');
            $end     = date('Y-m-d 23:59:59');
            
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_ADMISI])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_ADMISI]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter'][self::TGL_ADMISI]);
                }
            }
    
            // $query->andWhere(['between', self::TGL_ADMISI, $start, $end]);
            $query->andWhere(['and', self::TGL_PASIEN_PLG.' '.new \yii\db\Expression('is null'), ['<=', self::TGL_ADMISI, $start]]);
            $query->orWhere(['and', self::TGL_PASIEN_PLG.' '.new \yii\db\Expression('is not null'), ['>=', self::TGL_PASIEN_PLG, $start], ['<=', self::TGL_ADMISI, $start]]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
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
}