<?php


namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\LaporanWaktuTungguRadView;
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
use app\modules\v1\models\PemeriksaanRad;
use SirsCore\models\DaftarTindakan;
use yii\helpers\ArrayHelper;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;


class LapWaktuTungguPasienRadController extends \Doco\components\DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\LaporanWaktuTungguRadView';

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
            $model = new LaporanWaktuTungguRadView;
            $query = $model::find();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $startPersetujuan = '';
            $endPersetujuan = '';

            if (isset($_GET['advanced-filter'])) {
                $filter = $_GET['advanced-filter'];
                if (isset($filter['tglmasukpenunjang']) && !empty($filter['tglmasukpenunjang'])) {
                    $date = DocoHelpers::parsingRangeDate($filter['tglmasukpenunjang']);
                    $start = ArrayHelper::getValue($date, 'startDate');
                    $end = ArrayHelper::getValue($date, 'endDate');
                    unset($_GET['advanced-filter']['tglmasukpenunjang']);
                }
                if (isset($filter['tglpersetujuan']) && !empty($filter['tglpersetujuan'])) {
                    $date = DocoHelpers::parsingRangeDate($filter['tglpersetujuan']);
                    $startPersetujuan = ArrayHelper::getValue($date, 'startDate');
                    $endPersetujuan = ArrayHelper::getValue($date, 'endDate');
                    $query->andWhere(['between', 'tglpersetujuan', $startPersetujuan, $endPersetujuan]);
                    unset($_GET['advanced-filter']['tglpersetujuan']);
                }
                if(isset($filter['nama_pasien'])) {
                    $namaPasien = trim($filter['nama_pasien']);
                    $query->andFilterWhere(['or',
                       ['ILIKE','LOWER(nama_pasien)', strtolower($namaPasien)],
                       ['ILIKE','no_rekam_medik', $namaPasien]]);
           
                    unset($_GET['advanced-filter']['nama_pasien']);
                }
                if(isset($advancedFilter['asalrujukan_nama'])) {
                    $asalRujukanNama = strtolower(trim($advancedFilter['asalrujukan_nama']));
                    $query->andFilterWhere(['or',
                       ['ILIKE','LOWER(asalrujukan_nama)', $asalRujukanNama],
                       ['ILIKE','LOWER(instalasiasal_nama)', $asalRujukanNama]]);
           
                       unset($_GET['advanced-filter']['asalrujukan_nama']);
                }
                if(isset($filter['rujukandari_nama'])) {
                    $rujukanDariNama = strtolower(trim($filter['rujukandari_nama']));
                    $query->andFilterWhere(['ILIKE', 'LOWER(rujukandari_nama)', $rujukanDariNama]);
                    unset($_GET['advanced-filter']['rujukandari_nama']);
                }
            }

            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
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
     **/
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $model = new LaporanWaktuTungguRadView;
            $query = $model::find();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $ruangan_nama = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tglmasukpenunjang']);
                }
                if (isset($_GET['advanced-filter']['ruangan'])) {
                    $ruangan_nama = $_GET['advanced-filter']['ruangan'];
                }
            }
            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($model)) {
                $header = array();
                $print = new DocoPrint();
                $print->attributes = [
                    '#tgl_awal_bulan#'=> DocoHelpers::convDateTime($start, true, false),
                    '#tgl_akhir_bulan#'=> DocoHelpers::convDateTime($end, true, false),
                    '#tgl_cetak#'=> DocoHelpers::convDateTime(date("d-M-Y H:i:s"), true, false),
                    '#table_exportpdf#' => $this->renderPartial('pdf', [
                        'tgl_awal_bulan' => DocoHelpers::convDateTime($start, false, true),
                        'tgl_akhir_bulan' => DocoHelpers::convDateTime($end, false, true),
                        'ruangan_nama' => $ruangan_nama,
                        'header' => $header,
                        'model' => $query,
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

    public function actionExportExcel()
    {
        try {
            $data = array();
            $header = array();
            $request = Yii::$app->request;
            $model = new LaporanWaktuTungguRadView;
            $query = $model::find();

            $between = false;
            $betweenLahir = false;
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:00');
            $startLahir = '';
            $endLahir = '';
            $header = [];
            $ruangan_nama = '';


            if (isset($_GET['advanced-filter'])) {
                $advancedFilter = $_GET['advanced-filter'];
                if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    $header['Tanggal Rujukan'] = $_GET['advanced-filter']['tglmasukpenunjang'];
                    unset($_GET['advanced-filter']['tglmasukpenunjang']); // Unset Advanced Filter  date range
                    $between = true;
                }
                if (isset($advancedFilter['no_pendaftaran'])) {
                    $no_pendaftaran = $advancedFilter['no_pendaftaran'];
                    $header['No Pendaftaran'] = $no_pendaftaran;
                }

                if (isset($advancedFilter['no_rekam_medik'])) {
                    $no_rekam_medik = $advancedFilter['no_rekam_medik'];
                    $header['No Rekam Medis'] = $no_rekam_medik;
                }

                if (isset($advancedFilter['nama_pasien'])) {
                    $nama_pasien = $advancedFilter['nama_pasien'];
                    $header['Nama Pasien'] = $nama_pasien;
                }

                if (isset($advancedFilter['dokter'])) {
                    $dokter = $advancedFilter['dokter'];
                    $header['Dokter Radiologi'] = $dokter;
                }

                if (isset($advancedFilter['jenispemeriksaanrad_nama'])) {
                    $jenispemeriksaanrad_nama = $advancedFilter['jenispemeriksaanrad_nama'];
                    $header['Jenis Pemeriksaan'] = $jenispemeriksaanrad_nama;
                }

                if (isset($advancedFilter['daftartindakan_nama'])) {
                    $daftartindakan_nama = $advancedFilter['daftartindakan_nama'];
                    $header['Nama Pemeriksaan'] = $daftartindakan_nama;
                }
            }

            $header["Tanggal Cetak "] = date("d-M-Y H:i:s");

            $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            $average_lama_expertise = null;
            $average_lama_foto = null;
            $temp = [];
            if (!empty($query)) {
                $counter = 0;
                $interval_sample = $interval_daftar = [];
                foreach ($query as $index => $value) {
                    $temp[$value->no_rekam_medik] = $value->no_rekam_medik;

                    // waktu tunggu
                    $diffTglExpertise = '';
                    $diffFotoExpertise = '';
                    if(!empty($value->tgl_hasilrad)){
                        $diffTglExpertise = DocoHelpers::getLamaTunggu($value->tglmasukpenunjang, $value->tgl_hasilrad);
                    }
                    if(!empty($value->tgl_hasilrad)){
                        $diffFotoExpertise = DocoHelpers::getLamaTunggu($value->tgl_ambilfoto, $value->tgl_hasilrad);
                    }

                    $interval_expertise[] = DocoHelpers::timeToSeconds($diffTglExpertise);
                    $interval_foto[] = DocoHelpers::timeToSeconds($diffFotoExpertise);
                    $whtExpertise = !empty($value->tgl_hasilrad)
                        ? date('d-M-Y H:i:s',strtotime($value->tgl_hasilrad)) : null;
                    // waktu tunggu
                    $data[$counter]['tanggal_rujukan'] = date('d-M-Y H:i:s',strtotime($value->tglmasukpenunjang));
                    $data[$counter]['no_pendaftaran'] = $value->no_pendaftaran;
                    $data[$counter]['nomer_rekam_medik'] = $value->no_rekam_medik;
                    $data[$counter]['nama_pasien'] = $value->nama_pasien;
                    $data[$counter]['dokter_radiologi'] = $value->dokter;
                    $data[$counter]['jenis_pemeriksaan'] = $value->jenispemeriksaanrad_nama;
                    $data[$counter]['nama_pemeriksaan'] = $value->daftartindakan_nama;
                    $data[$counter]['tanggal_pendaftaran'] = date('d-M-Y H:i:s',strtotime($value->tglmasukpenunjang));
                    $data[$counter]['tanggal_Persetujuan'] = !empty($value->tglpersetujuan) ? date('d-M-Y H:i:s',strtotime($value->tglpersetujuan)) : date('d-M-Y H:i:s',strtotime($value->tglmasukpenunjang));
                    $data[$counter]['tanggal_ambil_foto'] = date('d-M-Y H:i:s',strtotime($value->tgl_ambilfoto));
                    $data[$counter]['tanggal_expertise'] = $whtExpertise;
                    $data[$counter]['waktu_tunggu_tanggal_expertise'] = $diffTglExpertise;
                    $data[$counter]['waktu_tunggu_pemeriksaan_radiologi'] = $diffFotoExpertise;

                    $counter++;
                }
                $find_average_second_expertise = DocoHelpers::getAverage($interval_expertise);
                $find_average_second_foto = DocoHelpers::getAverage($interval_foto);
                $average_lama_expertise = DocoHelpers::secondsToTime($find_average_second_expertise);
                $average_lama_foto = DocoHelpers::secondsToTime($find_average_second_foto);
            }


            $footer = [
                'title' => ['Jumlah Pasien', 3],
                'data' => [
                    'Dokter radiologi' => count($temp),
                    'Tanggal expertise' => 'Rata-rata waktu tunggu',
                    'Waktu tunggu tanggal expertise' => $average_lama_expertise,
                    'Waktu tunggu pemeriksaan radiologi' => $average_lama_foto,
                ]
            ];

            // return $footer;

            $periode = date('d M Y', strtotime($start)) . ' - ' . date('d M Y', strtotime($end));
            $filePath = DocoHelpers::exportExcel("Laporan Waktu Tunggu Radiologi", $data, $header, array("uploadPath" => "./uploads",
                "subTitle" => $ruangan_nama),$footer,[],true);

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
            $result = LaporanWaktuTungguRadView::find()->andWhere(['pasienkirimkeunitlain_id' => $id])->one();
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
        $jenisPemeriksaan = JenisPemeriksaanRad::find()->where(['is_active'=>true])->all();
        $pemeriksaanRad = PemeriksaanRad::find()->select([
            'pemeriksaanrad_m.daftartindakan_id',
            'daftartindakan_m.daftartindakan_nama'
        ])->joinWith([
            'daftarTindakan' => function ($query) {
                $query->select([
                    'daftartindakan_id'
                ]);
            }
        ])->where(['pemeriksaanrad_m.is_active'=>true])->asArray()->all();
        return  [
            'cara_bayar' => $caraBayar,
            'penjamin' => $penjamin,
            'rujukan' => $rujukan,
            'jenisPemeriksaan'=>$jenisPemeriksaan,
            'pemeriksaanRad'=>$pemeriksaanRad
        ];
    }

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $type = ArrayHelper::getValue($get, 'type');
        $page = ArrayHelper::getValue($get, 'page', 1);
        $limit = ArrayHelper::getValue($get, 'limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        $term = ArrayHelper::getValue($get, 'term');
        $term = !empty($term) ? strtolower($term) : '';
        $result = [];
        switch ($type) {
            case 'asal_rujukan':
                $limit = $limit + 1;
                $offset = ($page - 1) * $limit;
                $whereRujukan = $whereInstalasi = $whereRuangan = '';
                if(!empty($term)) {
                    $whereRujukan = " AND LOWER(asalrujukan_nama) LIKE '%$term%' ";
                    $whereInstalasi = " AND LOWER(instalasi_nama) LIKE '%$term%' ";
                    $whereRuangan = " AND LOWER(ruangan_nama) LIKE '%$term%' ";
                }
                $sql = "SELECT asalrujukan_nama AS id, asalrujukan_nama AS text 
                FROM asalrujukan_m
                WHERE is_deleted = FALSE AND is_active = TRUE {$whereRujukan}
                UNION ALL 
                SELECT instalasi_nama AS id, instalasi_nama AS text 
                FROM instalasi_m
                WHERE is_deleted = FALSE AND is_active = TRUE AND is_pelayanan = TRUE {$whereInstalasi}
                UNION ALL 
                SELECT ruangan_nama AS id, ruangan_nama AS text 
                FROM ruangan_m
                WHERE is_deleted = FALSE AND is_active = TRUE {$whereRuangan} 
                ORDER BY text ASC LIMIT {$limit} OFFSET {$offset}";
                
                $result = Yii::$app->db->createCommand($sql)->queryAll();
                break;

            case 'rs_rujukan':
                $limit = $limit + 1;
                $offset = ($page - 1) * $limit;
                $wherePerujuk = $whereRujukanKeluar = '';
                if(!empty($term)) {
                    $wherePerujuk = "AND LOWER(namaperujuk) LIKE '%$term%' ";
                    $whereRujukanKeluar = "AND LOWER(rumahsakit_rujukan) LIKE '%$term%' ";
                }

                $sql = "SELECT namaperujuk AS id, namaperujuk AS text 
                FROM perujuk_m
                WHERE is_deleted = FALSE AND is_active = TRUE {$wherePerujuk}
                UNION ALL 
                SELECT rumahsakit_rujukan AS id, rumahsakit_rujukan AS text 
                FROM rujukankeluar_m
                WHERE is_deleted = FALSE AND is_active = TRUE {$whereRujukanKeluar} 
                ORDER BY text ASC LIMIT {$limit} OFFSET {$offset}";
                $result = Yii::$app->db->createCommand($sql)->queryAll();
                break;

            case 'pemeriksaan':
                $result = DaftarTindakan::find()
                ->select(['daftartindakan_m.daftartindakan_id AS id', 'daftartindakan_m.daftartindakan_nama AS text'])
                ->leftJoin('pemeriksaanrad_m', 'daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id')
                ->where(['daftartindakan_m.is_active' => true]);
                
                if(!empty($term)) {
                $result->andWhere(['like', 'LOWER(daftartindakan_m.daftartindakan_nama)', strtolower($term)]);
                }
                $result->orderBy(['daftartindakan_m.daftartindakan_nama' => SORT_ASC]);
                break;
            
            default:
                
                break;
        }

        if(!empty($result)) {
            if($type != 'asal_rujukan' && $type != 'rs_rujukan') {
                $result = $result
                ->limit($limit + 1)
                ->offset(($page - 1) * $limit)
                ->asArray()
                ->all();
            }
        }

        return $result;
    }

    public function actionUnduhFile() 
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $tipe = ArrayHelper::getValue($get, 'tipe', 1);
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = ArrayHelper::getValue($get, 'randString');

        if (isset($get['page'])) unset($get['page']);
        if (isset($get['per-page'])) unset($get['per-page']);
        
        $data = $this->actionGetObjectData();
        $countData = ($tipe == 1) ? count($data) : ArrayHelper::getValue($data, 'countData', 0);
        $totalPerPage = ceil($countData/20);
        $url = Yii::$app->docoRest->getBaseUri('radiologi');
        $params = [
            'sendToUrl' => 'lap-waktu-tunggu-pasien-rad/drop-file',
            'getDataUrl' => 'lap-waktu-tunggu-pasien-rad/get-object-data',
            'base_uri' => $url,
        ];
        
        (new InternalService)->sendTo([
            'Sirs' => [
                'LapWaktuTungguRad' => [
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
                'CetakLapWaktuTungguRad' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $get,
                    'title' => 'Laporan Waktu Tunggu Pasien Radiologi',
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLapWaktuTungguRad' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'params' => $params,
                    'tipe' => $tipe
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function dateFilter($request)
    {
        $advancedFilter = $request->get('advanced-filter');
        $model = new LaporanWaktuTungguRadView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        $startPersetujuan = $endPersetujuan = null;
        $namaPasien = $asalRujukanNama = '';
        if(isset($advancedFilter['tglmasukpenunjang']) && !empty($advancedFilter['tglmasukpenunjang'])) {
            $rangeDate = DocoHelpers::parsingRangeDate($advancedFilter['tglmasukpenunjang']);
            $start = ArrayHelper::getValue($rangeDate, 'startDate');
            $end = ArrayHelper::getValue($rangeDate, 'endDate');
        }
        if(isset($advancedFilter['tglpersetujuan']) && !empty($advancedFilter['tglpersetujuan'])) {
            $rangeDatePersetujuan = DocoHelpers::parsingRangeDate($advancedFilter['tglpersetujuan']);
            $startPersetujuan = ArrayHelper::getValue($rangeDatePersetujuan, 'startDate');
            $endPersetujuan = ArrayHelper::getValue($rangeDatePersetujuan, 'endDate');
            $query->andWhere(['between', 'tglpersetujuan', $startPersetujuan, $endPersetujuan]);
        }
        if(isset($advancedFilter['nama_pasien'])) {
            $namaPasien = trim($advancedFilter['nama_pasien']);
            $query->andFilterWhere(['or',
            ['ILIKE','LOWER(nama_pasien)', strtolower($namaPasien)],
            ['ILIKE','no_rekam_medik', $namaPasien]]);

            unset($_GET['advanced-filter']['nama_pasien']);
        }
        if(isset($advancedFilter['asalrujukan_nama'])) {
            $asalRujukanNama = strtolower(trim($advancedFilter['asalrujukan_nama']));
            $query->andFilterWhere(['ILIKE', 'LOWER(rujukan)', $asalRujukanNama]);
            unset($_GET['advanced-filter']['asalrujukan_nama']);
        }
        if(isset($advancedFilter['rujukandari_nama'])) {
            $rujukanDariNama = strtolower(trim($advancedFilter['rujukandari_nama']));
            $query->andFilterWhere(['ILIKE', 'LOWER(rujukan)', $rujukanDariNama]);
            unset($_GET['advanced-filter']['rujukandari_nama']);
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->andWhere(['between', 'tglmasukpenunjang', $start, $end]);
        return [
            'query' => $query,
            'periode' => [
            'start' => $start,
            'end' => $end,
            ]
        ];
    }

    public function actionGetObjectData()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $tipe = ArrayHelper::getValue($getData, 'tipe', 1);
        $data = $this->dateFilter($request);
        $query = ArrayHelper::getValue($data, 'query');
        $periode = ArrayHelper::getValue($data, 'periode');
        $model = $query->asArray()->all();
        $result = $model;
        if($tipe == 2) {
            $periode = date('d M Y', strtotime(ArrayHelper::getValue($periode, 'start'))).' - '.date('d M Y', strtotime(ArrayHelper::getValue($periode, 'end')));
            $attributes = [
                '#table_exportpdf#' => $this->renderPartial('pdf', [
                    'model' => $model,
                ]),
                '#periode#' => $periode,
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#tanggal#' => date('d F Y H:i'),
            ];
            $result = [
                'periode' => $periode,
                'attributes' => $attributes,
                'countData' => count($model),
            ];
        }
        return $result;
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
        $filename = $request->get('filename', null);
        $tipe = $request->get('tipe', 1);
        $ext = ($tipe == 1) ? '.xlsx' : '.pdf';
        $rootPath = './uploads';
        $files = $rootPath.'/'.$filename . $ext;
        if(file_exists($files)) {
            header('Content-Description: File Transfer');
            if($tipe == 1) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            }
            else {
                header('Content-Type: application/pdf');
            }
            header("Content-Disposition: inline; filename=$files");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($files);
            unlink($files);
            die();
        }
    }
}
