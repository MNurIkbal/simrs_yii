<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-07 09:20:43
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-12-11 10:22:34
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-10 18:03:38
 * @Last Modified by:   Muhamad Lukman Hakim
 * @Last Modified time: 2020-09-15 10:38:00
 */


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoSpout;
use Doco\rabbitmq\RabbitBgProcess;

use app\modules\v1\models\LaporanPenjualanResepView;
use app\modules\v1\models\LaporanPenjualanResepDetailView;
use app\modules\v1\models\InfoPenjualanResepDetailView;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\DataDokterView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\models\LaporanPenjualanObatalkesView;

class LapPenjualanResepController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPenjualanResepView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["GET"];
        $verbs["generate-api"] = ["GET"];
        $verbs["export-excel"] = ["GET"];
        return $verbs;
    }
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new LaporanPenjualanObatalkesView;
        $query = $model::find(true);
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgltransaksi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgltransaksi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgltransaksi']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['carabayar_nama'])){
                $_GET['advanced-filter']['carabayar_id'] = $_GET['advanced-filter']['carabayar_nama'];
                unset($_GET['advanced-filter']['carabayar_nama']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])){
                $_GET['advanced-filter']['penjamin_id'] = $_GET['advanced-filter']['penjamin_nama'];
                unset($_GET['advanced-filter']['penjamin_nama']);
            }
            if(isset($_GET['advanced-filter']['resep'])){
                $resep = $_GET['advanced-filter']['resep'];
                $query->andWhere(['ILIKE', 'resep', $resep]);
            }
            if(isset($_GET['advanced-filter']['jenisobatalkes_nama'])){
                $jenisobatalkes_nama = $_GET['advanced-filter']['jenisobatalkes_nama'];
                $query->andWhere(['ILIKE', 'jenisobatalkes_nama', $jenisobatalkes_nama]);
            }

            if(isset($_GET['advanced-filter']['tanggal_lahir'])){
                $tanggal_lahir = $_GET['advanced-filter']['tanggal_lahir'];
                $tanggalMew = date('Y-m-d', strtotime($tanggal_lahir));
                $query->andWhere(['=', 'tanggal_lahir', $tanggalMew]);
            }

        }
        $query->andWhere(['between', 'tgltransaksi', $start, $end]);

        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $query->orderby([
                'tgltransaksi' => SORT_ASC,
                'noresep' => SORT_ASC,
                'nama_obat' => SORT_ASC
            ]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGenerateApi()
    {
        // jenis penjualan
        $modelJenisPenjualan = new Lookup;
        $queryJenisPenjualan = $modelJenisPenjualan::find()->where(['lookup_type' => 'jenis_resep']);

        $queryJenisPenjualan = DocoRestActiveFilter::advancedFilter($modelJenisPenjualan, $queryJenisPenjualan);
        $queryJenisPenjualan = new ActiveDataProvider([
            'query' => $queryJenisPenjualan,
        ]);

        // no resep
        $modelPenjualanResep = new PenjualanResep;
        $queryPenjualanResep = $modelPenjualanResep::find();

        $queryPenjualanResep = DocoRestActiveFilter::advancedFilter($modelPenjualanResep, $queryPenjualanResep);
        $queryPenjualanResep = new ActiveDataProvider([
            'query' => $queryPenjualanResep,
        ]);

        // Ruangan
        $modelRuangan = new Ruangan;

        $queryRuangan = $modelRuangan::find()->select(['ruangan_nama','ruangan_id'])->where(['instalasi_id' => DocoConstants::INSTALASI_FARMASI, 'is_deleted' => false])->all();

        // $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        // $queryRuangan = new ActiveDataProvider([
        //     'query' => $queryRuangan,
        // ]);

        // dokter
        $modelDokter = new Pegawai;
        $querDokter = $modelDokter::find()->select(['nama_pegawai','pegawai_id'])->where(['kelompokpegawai_id' => DocoConstants::KELOMPOK_PEGAWAI_DOKTER])->asArray()->all();

        $statusReseptur = new Lookup;
        $queryStatusReseptur = $statusReseptur::find()->where(['IN','lookup_type', ['status_reseptur', 'status_bmhp']]);
        
        $queryStatusReseptur = DocoRestActiveFilter::advancedFilter($statusReseptur, $queryStatusReseptur);
        $queryStatusReseptur = new ActiveDataProvider([
            'query' => $queryStatusReseptur,
        ]);

        $data = [
            'jenispenjualan' => $queryJenisPenjualan->getModels(),
            'penjualanresep' => $queryPenjualanResep->getModels(),
            'ruangan' => $queryRuangan,
            'dokter' => $querDokter,
            'statusPenjualan' => $queryStatusReseptur->getModels(),
        ];
        return $this->responseJson(200, 'success', $data);
    }

    public function actionDataResep()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataResep();
        $result->select(['noresep','noresep']);
        if(!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE','LOWER(noresep)',$term]);
        }
        return $result->asArray()->all();
    }

    public function dataResep()
    {
        $data = InfoPenjualanResep::find();
        return $data;
    }

    public function actionDataObat()
    {
        $model = new InfoPenjualanResepDetailView;
        $query = $model::find(true);
        $query->select(['obatalkes_namalain','hargajual_oa','ppn_persen','qty_oa','obatalkes_id']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected $_title = 'LAPORAN PENJUALAN RESEP';
    public function actionExportExcelPenjualanResep()
    {
        $model = new LaporanPenjualanResepDetailView;
        $query = $model::find(true);

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpenjualan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpenjualan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpenjualan']); // Unset Advanced Filter  date range
            }

            if(isset($_GET['advanced-filter']['carabayar_nama'])){
                $carabayar_id = $_GET['advanced-filter']['carabayar_nama'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])){
                $penjamin_nama = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
            }

            if(isset($_GET['advanced-filter']['jenispenjualan'])){
                $jenispenjualan = $_GET['advanced-filter']['jenispenjualan'];
                $query->andWhere(['jenispenjualan' => $jenispenjualan]);
            }

            if(isset($_GET['advanced-filter']['noresep'])){
                $noresep = $_GET['advanced-filter']['noresep'];
                $query->andWhere(['ILIKE', 'noresep', $noresep]);
            }

            if(isset($_GET['advanced-filter']['no_rekammedik'])){
                $no_rekammedik = $_GET['advanced-filter']['no_rekammedik'];
                $query->andWhere(['ILIKE', 'no_rekammedik', $no_rekammedik]);
            }

            if(isset($_GET['advanced-filter']['nama_pasien'])){
                $nama_pasien_filter = $_GET['advanced-filter']['nama_pasien'];
                $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien_filter]);
            }
            if(isset($_GET['advanced-filter']['jenisobatalkes_nama'])){
                $jenisobatalkes_nama_filter = $_GET['advanced-filter']['jenisobatalkes_nama'];
                $query->andWhere(['ILIKE', 'jenisobatalkes_nama', $jenisobatalkes_nama_filter]);
            }
        }

        $query->andWhere(['between', 'tglpenjualan', $start, $end]);
        $query->orderBy($_GET['order']);

        $result = [];

        $modelPegawai = new Pegawai;
        $queryPegawai = $modelPegawai::find()->all();
        $pegawai = ArrayHelper::map($queryPegawai, 'pegawai_id', 'nama_pegawai');

        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $nama_pasien = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '-';
            $nama_user = '-';

            if(isset($value['user'])) {
                $log_status = json_decode($value['user'])->log_status;

                foreach ($log_status as $logvalue) {
                    if($logvalue->status_worklist == "Ditelaah" || $logvalue->status_worklist == "Siap diserahkan") {
                        $nama_user = $pegawai[$logvalue->pegawai_id];
                    }
                }
            }

            $subtotal = $value['totalhargajual']+$value['totaltarifservice']+$value['biayaadministrasi']+$value['biayakonseling']+$value['pembulatanharga']+$value['jasadokterresep'];

            $newValue[\Yii::t('app', 'Tanggal Penjualan')] = date('d M Y', strtotime($value['tglpenjualan']));
            $newValue[\Yii::t('app', 'No. Pendaftaran')] = !empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '-';
            $newValue[\Yii::t('app', 'No. Resep')] = $value['noresep'];
            $newValue[\Yii::t('app', 'Nama Dokter')] = $value['nama_dokter'];
            $newValue[\Yii::t('app', 'No. Rekam Medik')] = $value['no_rekammedik'];
            $newValue[\Yii::t('app', 'Nama Pasien')] = $nama_pasien;
            $newValue[\Yii::t('app', 'Rke')] = $value['rke'];
            $newValue[\Yii::t('app', 'Kode Obat')] = $value['kode_obat'];
            $newValue[\Yii::t('app', 'Nama Obat')] = $value['nama_obat'];
            $newValue[\Yii::t('app', 'Jumlah Obat')] = $value['jumlah_obat'];
            $newValue[\Yii::t('app', 'Jenis Obat')] = $value['jenisobatalkes_nama'];
            $newValue[\Yii::t('app', 'Satuan')] = $value['satuan'];
            $newValue[\Yii::t('app', 'Total Tagihan (Rp)')] = number_format($value['totaltagihan'], 2, ',', '.');
            $newValue[\Yii::t('app', 'Status')] = $value['status_reseptur_nama'];
            $newValue[\Yii::t('app', 'Cara Bayar')] = $value['carabayar_nama'];
            $newValue[\Yii::t('app', 'Nama Penjamin')] = $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Formularium')] = $value['is_formularium'] ? 'Iya' : 'Tidak';
            $newValue[\Yii::t('app', 'Supplier')] = $value['supplier'];
            $newValue[\Yii::t('app', 'Principle')] = $value['principle'];
            $newValue[\Yii::t('app', 'User')] = $nama_user;
            $newValue[\Yii::t('app', '')] = " ";

            $result[$key] = $newValue;
        }
        // Directory Creation

        $carabayar = new CaraBayar;
        $jenispenjualan = new Lookup;

        $carabayar_nama = '-';
        $jenispenjualan_nama = '-';
        $penjamin_nama = '-';

        if(isset($_GET['advanced-filter']['carabayar_nama'])) {
            $queryCaraBayar = $carabayar->findOne($_GET['advanced-filter']['carabayar_nama']);
            $carabayar_nama = ($queryCaraBayar) ? $queryCaraBayar->carabayar_nama : '-';
        }

        if(isset($_GET['advanced-filter']['jenispenjualan'])){
            $queryJenisPenjualan = $jenispenjualan::findOne($_GET['advanced-filter']['jenispenjualan']);
            $jenispenjualan_nama = ($queryJenisPenjualan) ? $queryJenisPenjualan->lookup_name : '-';
        }

        if(isset($_GET['advanced-filter']['penjamin_nama'])) {
            $penjamin_nama = $_GET['advanced-filter']['penjamin_nama'];
        }

        $noresep = isset($noresep) ? $noresep : '-';
        $no_rekammedik = isset($no_rekammedik) ? $no_rekammedik : '-';
        $nama_pasien_filter = isset($nama_pasien_filter) ? $nama_pasien_filter : '-';

        $header = array(
            Yii::t('app', "Tanggal penjualan") => ((date('d M Y', strtotime($start))." - ".date('d M Y', strtotime($end)))),
            Yii::t('app', "Jenis Penjualan") => ($jenispenjualan_nama),
            Yii::t('app', "Cara Bayar") => ($carabayar_nama),
            Yii::t('app', "Penjamin") => ($penjamin_nama),
            Yii::t('app', "Nomor Rekam Medis") => ($no_rekammedik),
            Yii::t('app', "Nama Pasien") => ($nama_pasien_filter),
            Yii::t('app', "Nomor Resep") => ($noresep)
        );

        $options = [
            "titleStyle" => [
                "fontSize" => 11,
                "alignment" => "left"
            ],
            "customFormatCode" => [
                [
                    'selectColumn' => 'B',
                    'formatCode' => 'date'
                ],
                ['selectColumn' => 'C', 'formatCode' => 'general'],
                ['selectColumn' => 'D', 'formatCode' => 'general'],
                ['selectColumn' => 'E', 'formatCode' => 'general'],
                ['selectColumn' => 'F', 'formatCode' => 'general'],
                ['selectColumn' => 'G', 'formatCode' => 'general'],
                ['selectColumn' => 'H', 'formatCode' => 'general'],
                ['selectColumn' => 'I', 'formatCode' => 'general'],
                ['selectColumn' => 'J', 'formatCode' => 'general'],
                ['selectColumn' => 'K', 'formatCode' => 'number'],
                ['selectColumn' => 'L', 'formatCode' => 'general'],
                ['selectColumn' => 'M', 'formatCode' => 'number'],
                ['selectColumn' => 'N', 'formatCode' => 'general'],
                ['selectColumn' => 'O', 'formatCode' => 'general'],
                ['selectColumn' => 'P', 'formatCode' => 'general'],
                ['selectColumn' => 'Q', 'formatCode' => 'general'],
                ['selectColumn' => 'R', 'formatCode' => 'general'],
                ['selectColumn' => 'S', 'formatCode' => 'general'],
                ['selectColumn' => 'T', 'formatCode' => 'general']
            ],
        ];

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, $options, [], [], true);
        $filePath->save('php://output');
        die;

//        $filePath = DocoSpout::exportExcel($this->_title, $result, $header, [],[],[],true);
//        $filePath->close();
//        die;
    }
    /**
    * @controller actionPrintPdf
    * @attribute #table_detail# => table
    **/
    public function actionPrintPdf()
    {
        $model = new LaporanPenjualanResepView;
        $query = $model::find(true);

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpenjualan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpenjualan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpenjualan']); // Unset Advanced Filter  date range
                $between = true;
            }

            if(isset($_GET['advanced-filter']['carabayar_nama'])){
                $_GET['advanced-filter']['carabayar_id'] = $_GET['advanced-filter']['carabayar_nama'];
                unset($_GET['advanced-filter']['carabayar_nama']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])){
                $penjamin_nama = $_GET['advanced-filter']['penjamin_nama'];
                $query->andWhere(['ILIKE', 'penjamin_nama', $penjamin_nama]);
            }
        }
        $query->andWhere(['between', 'tglpenjualan', $start, $end]);
        $query->orderBy($_GET['order']);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $filter = date('d-M-Y', strtotime($start)).' - '.date('d-M-Y', strtotime($end));
        $result = ['data'=>$data, 'filter'=>$filter];
        $print = new DocoPrint();
        $print->attributes = [
            '#table_detail#' => $this->renderPartial('index',$result),
        ];
        $print->Output();
    }

    public function actionExportExcelBgproses()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $advanced_filter = $request->get('advanced-filter');
        $countData = $this->getDataLaporanExcel()->count();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $headerExcel = [
            'Tanggal Penjualan' => ArrayHelper::getValue($advanced_filter, 'tgltransaksi'),
            'Jenis Penjualan' => ArrayHelper::getValue($advanced_filter, 'jenis_resep'),
            'Nama Obat' => ArrayHelper::getValue($advanced_filter, 'nama_obat'),
            'Jenis Obat' => ArrayHelper::getValue($advanced_filter, 'jenisobatalkes_nama'),
            'Cara Bayar' => $this->getCaraBayarNama(ArrayHelper::getValue($advanced_filter, 'carabayar_nama')), // id
            'Penjamin' => $this->getPenjaminNama(ArrayHelper::getValue($advanced_filter, 'penjamin_nama')), // id
            'Psikotropika' => ArrayHelper::getValue($advanced_filter, 'is_psycothropica')=='true'?'Ya':'Tidak',
            'Narkotika' => ArrayHelper::getValue($advanced_filter, 'is_narcotic')=='true'?'Ya':'Tidak',
            'No Rekam Medik' => ArrayHelper::getValue($advanced_filter, 'no_rekammedik'),
            'Nama Pasien' =>ArrayHelper::getValue($advanced_filter, 'nama_pasien'),
            'No Resep' => ArrayHelper::getValue($advanced_filter, 'noresep'),
            'Nama Dokter' => ArrayHelper::getValue($advanced_filter, 'nama_dokter'),
            'Ruangan' => ArrayHelper::getValue($advanced_filter, 'ruangan_nama'),
            'Status' => $this->getLookupMNama(ArrayHelper::getValue($advanced_filter, 'status_reseptur')),
            'Tanggal Lahir' => ArrayHelper::getValue($advanced_filter, 'tanggal_lahir'),

        ];        
        $randString = $request->get('randString');
        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $countData,
            'headerExcel' => $headerExcel,
            'countData' => $countData, 
            'title' => 'Laporan Excel Penjualan Resep',
            'sendToUrl' => 'lap-penjualan-resep/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('apotek'),
        ], 'laporan_penjualan_resep');

        return [
            'totalPerPage' => $countData,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }
    public function actionExportExcelLaporanPenjualanResep()
    {
        try {
            $data = array();
            $header = array();

            $query =  $this->getDataLaporanExcel()->all();

            if (!empty($query)) {
                $counter = 0;
                foreach ($query as $index => $value) {


                    // Assign data
                    $data[$counter]['tgltransaksi'] = $value->tgltransaksi;
                    $data[$counter]['no_pendaftaran'] = $value->no_pendaftaran;
                    $data[$counter]['jenispenjualan'] = $value->jenispenjualan;
                    $data[$counter]['ruangan_nama'] = $value->ruangan_nama;
                    $data[$counter]['noresep'] = $value->noresep;
                    $data[$counter]['nama_pasien'] = $value->nama_pasien;

                    $counter++;
                }
            }
            $filePath = DocoSpout::exportCsv($this->_title, $data, $header, [],[],[],true);
            $path = 'web/'.'uploads/'.'Laporan Penjualan resep' .'.xlsx';
            $filePath->save($path);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $countData = $this->getDataLaporanExcel()->count();
        $fetchLimit = 50;
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPenjualanResep' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcelLapPenjualanResep' => [
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
                'UploadExcelLapPenjualanResep' => [
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

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request.'.csv';
        $fileName = $dir;
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

    public function getDataLaporanExcel()
    {
        $model = new LaporanPenjualanObatalkesView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgltransaksi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgltransaksi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgltransaksi']);
            }

            if(isset($_GET['advanced-filter']['carabayar_nama'])){
                $carabayar_id = $_GET['advanced-filter']['carabayar_nama'];
                $_GET['advanced-filter']['carabayar_id'] = $carabayar_id;
                unset($_GET['advanced-filter']['carabayar_nama']);
            }

            if(isset($_GET['advanced-filter']['penjamin_nama'])){
                $penjamin_id = $_GET['advanced-filter']['penjamin_nama'];
                $_GET['advanced-filter']['penjamin_id'] = $penjamin_id;
                unset($_GET['advanced-filter']['penjamin_nama']);
            }

            if(isset($_GET['advanced-filter']['jenispenjualan'])){
                $jenispenjualan = $_GET['advanced-filter']['jenispenjualan'];
                $query->andWhere(['jenispenjualan' => $jenispenjualan]);
            }

            if(isset($_GET['advanced-filter']['noresep'])){
                $noresep = $_GET['advanced-filter']['noresep'];
                $query->andWhere(['ILIKE', 'noresep', $noresep]);
            }

            if(isset($_GET['advanced-filter']['no_rekammedik'])){
                $no_rekammedik = $_GET['advanced-filter']['no_rekammedik'];
                $query->andWhere(['ILIKE', 'no_rekammedik', $no_rekammedik]);
            }

            if(isset($_GET['advanced-filter']['nama_pasien'])){
                $nama_pasien_filter = $_GET['advanced-filter']['nama_pasien'];
                $query->andWhere(['ILIKE', 'nama_pasien', $nama_pasien_filter]);
            }
            if(isset($_GET['advanced-filter']['jenisobatalkes_nama'])){
                $jenisobatalkes_nama_filter = $_GET['advanced-filter']['jenisobatalkes_nama'];
                $query->andWhere(['ILIKE', 'jenisobatalkes_nama', $jenisobatalkes_nama_filter]);
            }

            if(isset($_GET['advanced-filter']['tanggal_lahir'])){
                $tanggal_lahir = $_GET['advanced-filter']['tanggal_lahir'];
                $tanggalMew = date('Y-m-d', strtotime($tanggal_lahir));
                $query->andWhere(['=', 'tanggal_lahir', $tanggalMew]);
            }
        }
        
        $query->andWhere(['between', 'tgltransaksi', $start, $end]);
        $query->orderBy($_GET['order']);
        $mantap =  DocoRestActiveFilter::advancedFilter($model, $query);
        return $mantap;
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

    public function actionGetPenjamin()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'carabayar_m');
        $carabayarId = $request->get('carabayar_id');

        $model = new Penjamin;
        $query = $model::find()
        ->joinWith(['caraBayar' => function($query){
            $query->from('carabayar_m');
        }])->where(['penjamin_m.carabayar_id' => $carabayarId]);
        
        $data =  $query->asArray()->all();
       return $this->responseJson(200, 'success', $data);
    }

    private function getCaraBayarNama($id = null)
    {
        if ($id) {
            $cara_bayar = Yii::$app->cache->getOrSet('master_carabayar:'.$id, function ($cache) use($id) {
                $query = Carabayar::find()->where(['carabayar_id' => $id]);
                return $query->asArray()->one();
            });
            return ArrayHelper::getValue($cara_bayar, 'carabayar_nama', '-');
        }
        return '-';
    }

    private function getPenjaminNama($id = null)
    {
        if ($id) {
            $penjamin = Yii::$app->cache->getOrSet('master_penjamin:'.$id, function ($cache) use($id) {
                $query = Penjamin::find()->where(['penjamin_id' => $id]);
                return $query->asArray()->one();
            });
            return ArrayHelper::getValue($penjamin, 'penjamin_nama', '-');
        }
        return '-';
    }

    private function getLookupMNama($id)
    {
        if ($id) {
            if ($id == '111') { // kondisi mengikuti view laporanpenjualanobatalkes_v, agak aneh
                return 'Batal';
            }

            $lookupm = Yii::$app->cache->getOrSet('lookupm:'.$id, function ($cache) use($id) {
                $query = Lookup::find()->where(['lookup_id' => $id]);
                return $query->asArray()->one();
            });
            return ArrayHelper::getValue($lookupm, 'lookup_value', '-');
        }
        return '-';
    }

}
