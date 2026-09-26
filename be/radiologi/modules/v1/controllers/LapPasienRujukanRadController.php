<?php


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\LaporanOrderanRadV;
use app\modules\v1\models\InfoOrderanRadDetailView;
use app\modules\v1\models\DokterView;
use app\modules\v1\models\PasienKirimUnitlain;
use app\modules\v1\models\PermintaanKePenunjang;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\TindakanPelayananT;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\BatalOrderPenunjangT;
use app\modules\v1\models\JenisPemeriksaanRad;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LaporanPasienRujukanRadView;
use app\modules\v1\models\RujukanKeluar;
use app\modules\v1\models\Pegawai;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;

use yii\helpers\ArrayHelper;


class LapPasienRujukanRadController extends \Doco\components\DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\LaporanOrderanRadV';

    // Verbs
    public function verbs()
    {
        // Verbs parent
        $verbs = parent::verbs();

        // Return verbs
        return $verbs;
    }

    // Actions
    public function actions()
    {
        // Actions parent
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $model = new LaporanPasienRujukanRadView;
            $query = $model::find();
            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $betweenLahir = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $startPersetujuan = '';
            $endPersetujuan = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_rujukan']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if (isset($_GET['advanced-filter']['tgl_persetujuan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_persetujuan']);
                if (count($explode) == 2) {
                        $startPersetujuan = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $endPersetujuan = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_persetujuan']); // Unset Advanced Filter  date range
                    $betweenPersetujuan = true;
                }
                if(isset($_GET['advanced-filter']['no_rekam_medik'])) {
                    $namaPasienRM = $_GET['advanced-filter']['no_rekam_medik'];
                    $query->andWhere(['ILIKE', 'LOWER(nama_pasien)', strtolower($namaPasienRM)]);
                    $query->orWhere(['ILIKE', 'no_rekam_medik', $namaPasienRM]);
                    unset($_GET['advanced-filter']['no_rekam_medik']);
                }
            }
            
            $query->andWhere(['between', 'tgl_rujukan', $start, $end]);

           
            if (!empty($startPersetujuan) && !empty($endPersetujuan) && $betweenPersetujuan) {
                $query->andWhere(['between', 'tgl_persetujuan', $startPersetujuan, $endPersetujuan]);
            }
            /**
             * End Special Condition date range
             **/

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }


    /**
     * @controller actionExportPdf
     * @attribute #table_exportpdf# => table 
     * @attribute #tgl_awal_bulan# => tanggal awal bulan
     * @attribute #tgl_akhir_bulan# => tanggal Akhir bulan
     * @attribute #periode# => periode
     * @attribute #tanggal# => tanggal cetak
     **/
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $model = new LaporanOrderanRadV;
            $query = $model::find();

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $betweenLahir = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $startLahir = '';
            $endLahir = '';

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_rujukan']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                    $betweenLahir = true;
                }
            }
            
            $query->andWhere(['between', 'tgl_rujukan', $start, $end]);

           
            if (!empty($startLahir) && !empty($endLahir) && $betweenLahir) {
                $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
            }

            $query->andWhere(['between', 'tgl_rujukan', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            $periode = date('d-M-Y', strtotime($start)).' - '.date('d-M-Y', strtotime($end));
            if (!empty($query)) {
                $header = array();
                $print = new DocoPrint();
                $print->attributes = [
                    '#tgl_awal_bulan#'=> DocoHelpers::convDateTime($start, true, false),
                    '#tgl_akhir_bulan#'=> DocoHelpers::convDateTime($end, true, false),
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'header' => $header,
                        'model' => $query,
                        'periode' => $periode,
                        'tanggal' => date('d-M-Y')
                    ]),
                ];
                $print->Output();
            }
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

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $result = $resultData = [];
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $type = $request->get('type', []);
        $additionalPayload = $request->get('additionalPayload', []);
        $carabayar_id = $instalasi_id = null;
        if(isset($additionalPayload['carabayar_id']) && !empty($additionalPayload['carabayar_id'])) {
            $carabayar_id = $additionalPayload['carabayar_id'];
        }
        if(isset($additionalPayload['instalasi_id']) && !empty($additionalPayload['instalasi_id'])) {
            $instalasi_id = $additionalPayload['instalasi_id'];
        }
        $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        switch ($type) {
            case 'pemeriksaan':
                $result = JenisPemeriksaanRad::find()
                    ->select(['jenispemeriksaanrad_id as id', 'jenispemeriksaanrad_nama as text'])
                    ->where(['is_active' => true]);

                if(!empty($term)) {
                    $result->andWhere(['like', 'LOWER(jenispemeriksaanrad_nama)', strtolower($term)]);
                }
                $result->orderBy(['jenispemeriksaanrad_nama' => SORT_ASC]);
                break;
            
            case 'ruangan': 
                $result = Ruangan::find()
                    ->select(['ruangan_id as id', 'ruangan_nama as text'])
                    ->where(['instalasi_id' => $instalasi_id, 'is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
                    }
                    $result->orderBy(['ruangan_nama' => SORT_ASC]);
                break;

            case 'carabayar':
                $result = CaraBayar::find()
                    ->select(['carabayar_id as id', 'carabayar_nama as text'])
                    ->where(['is_active' => true]);

                if(!empty($term)) {
                    $result->andWhere(['like', 'LOWER(carabayar_nama)', strtolower($term)]);
                }
                $result->orderBy(['carabayar_nama' => SORT_ASC]);
                break;
            
            case 'penjamin': 
                $result = Penjamin::find()
                    ->select(['penjamin_id as id', 'penjamin_nama as text'])
                    ->where(['carabayar_id' => $carabayar_id, 'is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(penjamin_nama)', strtolower($term)]);
                    }
                    $result->orderBy(['penjamin_nama' => SORT_ASC]);
                break;
        }
        $resultData = $result->asArray()->all();
        return $resultData;
    }

    public function actionExportExcel()
    {
        try {
            $data = array();
            $header = array();
            $request = Yii::$app->request;
            $model = new LaporanOrderanRadV;
            $query = $model::find();
            // $query->andWhere(['between', 'tgl_rujukan', $start, $end]);

            // filter
            $between = false;
            $betweenLahir = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $startLahir = '';
            $endLahir = '';
            $header = [];

            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    $header['Tanggal Rujukan'] = $_GET['advanced-filter']['tgl_rujukan'];
                    unset($_GET['advanced-filter']['tgl_rujukan']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if (isset($_GET['advanced-filter']['tanggal_lahir'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tanggal_lahir']);
                    if (count($explode) == 2) {
                        $startLahir = date('Y-m-d', strtotime($explode[0]));
                        $endLahir = date('Y-m-d', strtotime($explode[1]));
                    }
                    $header['Tanggal Lahir'] = $_GET['advanced-filter']['tanggal_lahir'];
                    unset($_GET['advanced-filter']['tanggal_lahir']); // Unset Advanced Filter  date range
                    $betweenLahir = true;
                }

                if (isset($advancedFilter['no_rujukan'])) {
                    $no_rujukan = $advancedFilter['no_rujukan'];
                    $header['No Rujukan'] = $no_rujukan;
                }

                if (isset($advancedFilter['no_rekam_medik'])) {
                    $no_rekam_medik = $advancedFilter['no_rekam_medik'];
                    $header['No Rekam Medis'] = $no_rekam_medik;
                }

                if (isset($advancedFilter['nama_pasien'])) {
                    $nama_pasien = $advancedFilter['nama_pasien'];
                    $header['Nama Pasien'] = $nama_pasien;
                }

                if (isset($advancedFilter['ruangan_nama'])) {
                    $ruangan_nama = $advancedFilter['ruangan_nama'];
                    $header['Asal Rujukan'] = $ruangan_nama;
                }

                if (isset($advancedFilter['dokter_perujuk'])) {
                    $dokter_perujuk = $advancedFilter['dokter_perujuk'];
                    $header['Dokter Perujuk'] = $dokter_perujuk;
                }

                if (isset($advancedFilter['carabayar_nama'])) {
                    $carabayar_nama = $advancedFilter['carabayar_nama'];
                    $header['Cara Bayar'] = $carabayar_nama;
                }

                if (isset($advancedFilter['penjamin_nama'])) {
                    $penjamin_nama = $advancedFilter['penjamin_nama'];
                    $header['Penjamin'] = $penjamin_nama;
                }

                if (isset($advancedFilter['stat_penunjang'])) {
                    $stat_penunjang = $advancedFilter['stat_penunjang'];
                    $header['Status'] = $stat_penunjang;
                }
            }

            $query->andWhere(['between', 'tgl_rujukan', $start, $end]);


            if (!empty($startLahir) && !empty($endLahir) && $betweenLahir) {
                $query->andWhere(['between', 'tanggal_lahir', $startLahir, $endLahir]);
            }
            // filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($query)) {
                $counter = 0;

                foreach ($query as $index => $value) {
                    $data[$counter]['tanggal_rujukan'] = DocoHelpers::convDateTime($value->tgl_rujukan, true, false);
                    $data[$counter]['nomer_rujukan'] = $value->no_rujukan;
                    $data[$counter]['nomer_rekam_medik'] = $value->no_rekam_medik;
                    $data[$counter]['nama_pasien'] = $value->nama_pasien;
                    $data[$counter]['tanggal_lahir'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value->tanggal_lahir)), true, false);
                    $data[$counter]['asal_rujukan'] = $value->ruangan_nama;
                    $data[$counter]['dokter_perujuk'] = $value->dokter_perujuk;
                    $data[$counter]['cara_bayar'] = $value->carabayar_nama;
                    $data[$counter]['penjamin'] = $value->penjamin_nama;
                    $data[$counter]['status'] = $value->stat_penunjang;

                    $counter++;
                }
            }
            $filePath = DocoHelpers::exportExcel("Laporan Pasien Rujukan Radiologi", $data, $header, array("uploadPath" => "./uploads"),[],[],true);

            $filePath->save('php://output');
            die;
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


    public function actionGetView($id)
    {
        try {
            $result = LaporanOrderanRadV::find()->andWhere(['pasienkirimkeunitlain_id' => $id])->one();
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

    public function actionGetPemeriksaanView($id)
    {
        try {
            $model = new InfoOrderanRadDetailView;
            $query = $model::find()->where(['pasienkirimkeunitlain_id' => $id]);
            // Doco active filter
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


    public function actionGetDokter()
    {
        try {
            $model = new DokterView;
            $query = $model::find();

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
             **/
            $between = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tgl_rujukan'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_rujukan']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_rujukan']); // Unset Advanced Filter  date range
                    $between = true;
                }
            }
            if ($between) {
                $query->andWhere(['between', 'tgl_rujukan', $start, $end]);
            }
            /**
             * End Special Condition date range
             **/

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
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
        ])->all();
        $lookup_type = 'status_periksa_penunjang';
        $statusPeriksa = Lookup::find()->where([
            'lookup_type' => $lookup_type,
            'is_active' => true,
        ])
        ->andWhere(['ILIKE','lookup_kode','RAD'])->all();
        $namaRsRujukan = RujukanKeluar::find()->where([
            'is_active' => true,
        ])->all();
        $dokterPerujuk = Pegawai::find()->where([
            'is_active' => true,
            'kelompokpegawai_id' => 1,
        ])->all();

        $jenisPemeriksaanRad = Yii::$app->runAction('v1/allow/get-pemeriksaan-rad');
        return  [
            'cara_bayar' => $caraBayar,
            'penjamin' => $penjamin,
            'rujukan' => $rujukan,
            'asalRujukan' => $asalRujukan,
            'statusPeriksa' => $statusPeriksa,
            'namaRsRujukan' => $namaRsRujukan,
            'jenisPemeriksaanRad' => $jenisPemeriksaanRad,
            'dokterPerujuk' => $dokterPerujuk,
        ];
    }

    public function actionGetAsalRujukan()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        if(isset($get['asal_rujukan'])) {
            $asal_rujukan = strtolower($get['asal_rujukan']);
            $whereRujukan = " AND LOWER(asalrujukan_nama) LIKE '%$asal_rujukan%' ";
            $whereInstalasi = " AND LOWER(instalasi_nama) LIKE '%$asal_rujukan%' ";
        }

        $sql = "SELECT asalrujukan_nama AS id, asalrujukan_nama AS text 
            FROM asalrujukan_m
            WHERE is_deleted = FALSE AND is_active = TRUE {$whereRujukan}
            UNION ALL 
            SELECT instalasi_nama AS id, instalasi_nama AS text 
            FROM instalasi_m
            WHERE is_deleted = FALSE AND is_active = TRUE AND is_pelayanan = TRUE {$whereInstalasi} 
            ORDER BY text ASC";
        
       return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function actionGetRujukanKeluar()
    {
        $request = Yii::$app->request;
        $get = $request->get();

        if(isset($get['rs_rujukan'])) {
            $rs_rujukan = strtolower($get['rs_rujukan']);
            $wherePerujuk = "AND LOWER(namaperujuk) LIKE '%$rs_rujukan%' ";
            $whereRujukanKeluar = "AND LOWER(rumahsakit_rujukan) LIKE '%$rs_rujukan%' ";
        }

        $sql = "SELECT namaperujuk AS id, namaperujuk AS text 
            FROM perujuk_m
            WHERE is_deleted = FALSE AND is_active = TRUE {$wherePerujuk}
            UNION ALL 
            SELECT rumahsakit_rujukan AS id, rumahsakit_rujukan AS text 
            FROM rujukankeluar_m
            WHERE is_deleted = FALSE AND is_active = TRUE {$whereRujukanKeluar} 
            ORDER BY text ASC";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public function actionGenerateApi($id)
    {
        $return = array('labDetail' => array(), 'listPemeriksaan' => array());
        $labDetail = LaporanOrderanRadV::find()->andWhere(['pasienkirimkeunitlain_id' => $id])->one();
        $pemeriksaan = InfoOrderanRadDetailView::find()->where(['pasienkirimkeunitlain_id' => $id])->all();
        $caraBayar = CaraBayar::find()->where([
            'is_active' => true
        ])->all();
        if (!empty($labDetail)) {
            $return['labDetail'] = $labDetail;
        }
        if (!empty($pemeriksaan)) {
            $return['listPemeriksaan'] = $pemeriksaan;
        }
        return $return;
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
        $jenisRujukan =  [
            1 => 'Rujukan RS',
            2 => 'Rujukan Masuk',
            3 => 'APS',
            4 => 'Rujukan Keluar'
        ];
        $start = date('d-M-Y');
        $end = date('d-M-Y');
        $startPersetujuan = $endPersetujuan = $jenis_rujukan_id = '';
        $no_pendaftaran = $no_rujukan = $nama_pasien_rm = $daftartindakan_nama = '-';
        $status_periksa_nama = $dokter_perujuk = $jenis_rujukan = $asal_rujukan = $nama_rs_rujukan = '-';
        if (isset($get['advanced-filter'])) {
            if (isset($get['advanced-filter']['tgl_rujukan'])) {
                $tgl_rujukan = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_rujukan']);
                $start = !empty($tgl_rujukan['startDate']) ?  date('d-M-Y', strtotime($tgl_rujukan['startDate'])) : '';
                $end = !empty($tgl_rujukan['endDate']) ?  date('d-M-Y', strtotime($tgl_rujukan['endDate'])) : '';
            }   
            if (isset($get['advanced-filter']['tgl_persetujuan'])) {
                $tgl_persetujuan = DocoHelpers::parsingRangeDate($get['advanced-filter']['tgl_persetujuan']);
                $startPersetujuan = !empty($tgl_persetujuan['startDate']) ?  date('d-M-Y', strtotime($tgl_persetujuan['startDate'])) : '';
                $endPersetujuan = !empty($tgl_persetujuan['endDate']) ?  date('d-M-Y', strtotime($tgl_persetujuan['endDate'])) : '';
            }
            $advancedFilter = $get['advanced-filter'];
            $no_pendaftaran = ArrayHelper::getValue($advancedFilter,'no_pendaftaran','-');
            $no_rujukan = ArrayHelper::getValue($advancedFilter,'no_rujukan','-');
            $nama_pasien_rm = ArrayHelper::getValue($advancedFilter,'no_rekam_medik','-');
            $daftartindakan_nama = ArrayHelper::getValue($advancedFilter,'daftartindakan_nama','-');
            $status_periksa_nama = ArrayHelper::getValue($advancedFilter,'status_periksa_nama','-');
            $dokter_perujuk = ArrayHelper::getValue($advancedFilter,'dokter_perujuk','-');   
            $asal_rujukan = ArrayHelper::getValue($advancedFilter,'rujukandari_nama','-');   
            $nama_rs_rujukan = ArrayHelper::getValue($advancedFilter,'asalrujukan_nama','-');   
            $jenis_rujukan_id = ArrayHelper::getValue($advancedFilter,'jenis_rujukan_id','');
            $jenis_rujukan = !empty($jenisRujukan[$jenis_rujukan_id]) ? $jenisRujukan[$jenis_rujukan_id] : '-';   
        }
        
        
        $headerExcel = [
            "Tanggal Rujukan" => $start . ' - ' . $end,
            "No Pendaftaran" => $no_pendaftaran,
            "No Rujukan" => $no_rujukan,
            "Tanggal Persetujuan" => $startPersetujuan . ' - ' . $endPersetujuan,
            "Nama Pasien / No RM" => $nama_pasien_rm,
            "Jenis Rujukan" => $jenis_rujukan,
            "Asal Rujukan" => $asal_rujukan,
            "Nama RS Rujukan" => $nama_rs_rujukan,
            "Jenis Pemeriksaan" => $daftartindakan_nama,
            "Status" => $status_periksa_nama,
            "Nama Dokter" => $dokter_perujuk,
        ];

        $header = $this->getheader();
        $data = $this->actionIndex();
        $countData = $data->getCount();
        $totalPerPage = $data->getCount();
        $options = [
            "skipIncrement" => true,
            "customHeader" => [],
        ];
        
        $uri_radiologi = Yii::$app->docoRest->getBaseUri('radiologi');
        $params = [
            'sendToUrl' => 'lap-pasien-rujukan-rad/drop-file',
            'base_uri' => $uri_radiologi,
        ];
        (new InternalService)->sendTo([
            'Sirs' => [
                'DataExportExcelPasienRujukanRad' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
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
                    'title' => 'Laporan Pasien Rujukan Radiologi',
                    'headerExcel' => $headerExcel,
                    'footer' => [],
                    'options' => $options,
                    'header' => $header,
                    // 'customData' => true,
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

    private function getheader(){
    $column = [];

    $column = [
        [
              'title' => 'No',
              'data' => 'rowNum',
              'searchable' => false,
              'visible' => true,
        ],
        [
             'title' => 'Tanggal Rujukan',
             'data' => 'tgl_rujukan',
             'searchable' => false,
             'visible' => true,
        ],
        [
             'title' => 'Tanggal Persetujuan',
             'data' => 'tgl_persetujuan',
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
              'title' => 'No Rujukan',
              'data' => 'no_rujukan',
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
            'title' => 'No. Rekam Medik',
            'data' => 'no_rekam_medik',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Tanggal Lahir',
            'data' => 'tanggal_lahir',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Pemeriksaan',
            'data' => 'daftartindakan_nama',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Jenis Rujukan',
            'data' => 'jenis_rujukan',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Rujukan',
            'data' => 'rujukan',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Asal Rujukan',
            'data' => 'asalrujukan_nama',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Nama RS',
            'data' => 'nama_rs',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Dokter Perujuk',
            'data' => 'dokter_perujuk',
            'searchable' => false,
            'visible' => true,
        ],
        [
            'title' => 'Cara Bayar',
            'data' => 'carabayar_nama',
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
            'title' => 'Status',
            'data' => 'status_periksa_nama',
            'searchable' => false,
            'visible' => true,
        ],
      ];
    return $column;
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
    

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName, $dir);
    }

    public function actionExportPdfBgprocess() 
    {
        $kode_doc = 'Lap-Pasien-Rad';
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        $fetchLimit = 20;
        $data = $this->actionIndex();
        $countData = $data->getCount();
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        $uri_radiologi = Yii::$app->docoRest->getBaseUri('radiologi');
        $params = [
            'base_uri' => $uri_radiologi,
            'getDataUrl' => 'lap-pasien-rujukan-rad/render-attributes',
            'sendToUrl' => 'lap-pasien-rujukan-rad/drop-file',
        ];

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'DataLaporanPasienRujukanRadPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $get,
                    'params' => $params,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakLapPasienRujukanRad' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'kode_doc' => $kode_doc,
                    'params' => $params,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLapPasienRujukanRad' => [
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

    public function actionDownloadFilePdf()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        // $dir = $rootPath.'/'.$no_request;
        $fileName = $rootPath.'/' . $no_request . '.pdf';
        DocoHelpers::downloadFileExcel($fileName);
    }

    public function actionRenderAttributes() 
   {
    $request = Yii::$app->request;
    $getData = $request->get()['params'];
    $model = ArrayHelper::getValue($getData, 'model', []);
    $periode = ArrayHelper::getValue($getData, 'periode', '');
    $dokPath = ArrayHelper::getValue($getData, 'dokPath', '');
    $start = ArrayHelper::getValue($getData, 'start', '');
    $end = ArrayHelper::getValue($getData, 'end', '');

         $periode = date('d-M-Y', strtotime($start)).' - '.date('d-M-Y', strtotime($end));
         $header = array();
         $attributes = [
            '#tgl_awal_bulan#'=> DocoHelpers::convDateTime($start, true, false),
            '#tgl_akhir_bulan#'=> DocoHelpers::convDateTime($end, true, false),
            '#table_exportpdf#' => $this->renderPartial($dokPath, [
                'header' => $header,
                'model' => $model,
                'periode' => $periode,
                'tanggal' => date('d-M-Y H:i')
            ]),
        ];
      return $attributes;
   }

}
