<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LapkunjunganpasienrsV as InfoKunjungan;
use app\modules\v1\models\LaporanKunjunganPasienRsDiagnosa;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Jabatan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\KondisiKeluar;
use yii\data\ArrayDataProvider;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use Doco\rabbitmq\RabbitBgProcess;
use yii\web\UploadedFile;

class LapKunjunganController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoKunjungan';
    public static $look_exlude = [402,628];
    const DIPERIKSA_PDFTRN = 2;
    const PULANG_PDFTRN = 4;
    const BATAL_PDFTRN = 402;
    const BELUM_PDFTRN = 486;
    const DIPERIKSA_ADMISI = 441;
    const PULANG_ADMISI = 487;
    const BATAL_ADMISI = 453;
    const BELUM_ADMISI = 440;
    public static $look_diperiksa = [self::DIPERIKSA_PDFTRN,self::DIPERIKSA_ADMISI];
    public static $look_pulang = [self::PULANG_PDFTRN,self::PULANG_ADMISI];
    public static $look_batal = [self::BATAL_PDFTRN,self::BATAL_ADMISI];
    public static $look_belum = [self::BELUM_PDFTRN,self::BELUM_ADMISI];
    const UPDATE = 'update';
    const FINISH = 'finish';

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
        // unset($actions['view']);
        // unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => $this->getObjectData(),
        ]);
    }

    private function getObjectData()
    {
        $request = Yii::$app->request;
        
        $model   = new LaporanKunjunganPasienRsDiagnosa;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        $exceptionStatus = $this->getExceptionStatus();

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }

            if(isset($_GET['advanced-filter']['tanggal_lahir'])) {
                $tglLahir = date('Y-m-d', strtotime($_GET['advanced-filter']['tanggal_lahir']));
                $query->andWhere(['tanggal_lahir' => $tglLahir ]);
                unset($_GET['advanced-filter']['tanggal_lahir']);
            }

            if(isset($_GET['advanced-filter']['jenis_kelamin'])) {
                $_GET['advanced-filter']['jeniskelamin'] = $_GET['advanced-filter']['jenis_kelamin'];
                unset($_GET['advanced-filter']['jenis_kelamin']);
            }

            if(isset($_GET['advanced-filter']['diagnosa_utama'])) {
                $icdUtama = $_GET['advanced-filter']['diagnosa_utama'];
                unset($_GET['advanced-filter']['diagnosa_utama']);
                $query->andWhere(['diagnosa_utama_id' => $icdUtama]);
            }

            if(isset($_GET['advanced-filter']['diagnosa_penyerta'])) {
                $icdPenyerta = $_GET['advanced-filter']['diagnosa_penyerta'];
                unset($_GET['advanced-filter']['diagnosa_penyerta']);
                $query->andWhere(['ilike', 'diagnosa_penyerta_id', $icdPenyerta]);
            }

            if(isset($_GET['advanced-filter']['kelaspelayanan_id'])) {
                $kelasPelayanan_id = $_GET['advanced-filter']['kelaspelayanan_id'];
                unset($_GET['advanced-filter']['kelaspelayanan_id']);
                $query->andWhere(['kelaspelayanan_id' => $kelasPelayanan_id]);
            }

            if(isset($_GET['advanced-filter']['status_periksa'])) {
                $status_periksa = $_GET['advanced-filter']['status_periksa'];
                unset($_GET['advanced-filter']['status_periksa']);
                switch ($status_periksa) {
                    case self::DIPERIKSA_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    case self::PULANG_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    case self::BATAL_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    case self::BELUM_PDFTRN:
                        $query->andWhere(['IN', 'id_status_periksa', self::$look_diperiksa]);
                        break;
                    default:
                        $query->andWhere(['id_status_periksa' => $status_periksa]);
                        break;
                }
            }

            if(isset($_GET['advanced-filter']['no_identitas'])) {
                $noIdentitas = $_GET['advanced-filter']['no_identitas'];
                unset($_GET['advanced-filter']['no_identitas']);
                $query->andWhere(['ilike', 'additional_pasien', $noIdentitas]);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query->andWhere([
            'NOT IN', 'id_status_periksa', [
                DocoConstants::STATUS_PERIKSA_BTL_KUNJ,
                DocoConstants::STATUS_PERIKSA_BTL_PERIKSA,
                DocoConstants::STATUS_PERIKSA_BTL_KONSUL,
                DocoConstants::STATUS_PERIKSA_BTL_RUJUK_RAWAT_INAP,
                DocoConstants::STATUS_RANAP_BATAL_RAWAT
            ]
        ]);

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionGetRuanganBy($id)
    {
        $data = Ruangan::find()->where(['instalasi_id' => $id]);

        return $data->all();
    }

    public function actionGetPenjaminBy($id)
    {
        $data = Penjamin::find()->where(['carabayar_id' => $id]);

        return $data->all();
    }

    public function actionGenerateApi()
    {
        // cara bayar
        $modelCaraBayar = new CaraBayar;
        $queryCaraBayar = $modelCaraBayar::find()->all();

        // penjamin
        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find()->all();

        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find()->all();

        // ruangan
        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()->all();

        // jenis kasus penyakit
        $modelKasusPenyakit = new JenisKasusPenyakit;
        $queryKasusPenyakit = $modelKasusPenyakit::find()->all();

        // dokter PJ
        $modelDokter = new Pegawai;
        $queryDokter = $modelDokter::find()->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER])->all();

        // Jenis kelamin
        $modelJenisKelamin = new Lookup;
        $queryJenisKelamin = $modelJenisKelamin::find()->where(['lookup_type' => 'jenis_kelamin'])->all();

        $modelStatusPeriksa = new Lookup;
        $queryStatusPeriksa = $modelStatusPeriksa::find()->where(['lookup_type' => Lookup::STATUS_PERIKSA])
        ->andWhere(['NOT', ['lookup_id' => self::$look_exlude]]);
        $queryStatusPeriksa = DocoRestActiveFilter::advancedFilter($modelStatusPeriksa, $queryStatusPeriksa);
        $queryStatusPeriksa = new ActiveDataProvider([
            'query' => $queryStatusPeriksa,
            'pagination' => false
        ]);

        // Kondisi Pulang
        $modelKondisiKeluar = new KondisiKeluar;
        $queryKondisiKeluar = $modelKondisiKeluar::find()->all();

        // Cara Pulang
        $modelCaraKeluar = new CaraKeluar;
        $queryCaraKeluar = $modelCaraKeluar::find()->all();

        // Status Kunjungan
        $modelKunjungan = new Lookup;
        $queryKunjungan = $modelKunjungan::find()->where(['lookup_type' => 'kunjungan'])->all();

        // Kelas Pelayanan
        $modelKelasPelayanan = new KelasPelayanan;
        $queryKelasPelayanan = $modelKelasPelayanan::find()->all();

        return [
            'cara_bayar' => $queryCaraBayar,
            'penjamin' => $queryPenjamin,
            'instalasi' => $queryInstalasi,
            'ruangan' => $queryRuangan,
            'kasus_penyakit' => $queryKasusPenyakit,
            'dokter' => $queryDokter,
            'jenis_kelamin' => $queryJenisKelamin,
            'statusPeriksa' => $queryStatusPeriksa->getModels(),
            'kondisi_keluar' => $queryKondisiKeluar,
            'cara_keluar' => $queryCaraKeluar,
            'kunjungan' => $queryKunjungan,
            'kelas_pelayanan' => $queryKelasPelayanan
        ];
    }

    public function actionListPegawai()
    {
        $model = new Pegawai;
        $query = $model::find()->joinWith(['jabatan']);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListJabatan()
    {
        $model = new Jabatan;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionExportExcel()
    {
        ini_set('memory_limit', '-1');
        $request = Yii::$app->request;

        /*$no_rekam_medik          = '';
        $nama_pasien             = '';
        $jenis_kelamin           = '';
        $carabayar_nama          = '';
        $penjamin_nama           = '';
        $jeniskasuspenyakit_nama = '';
        $instalasi_nama          = '';
        $ruangan_nama            = '';
        $nama_pegawai            = '';
        

        $model   = new LaporanKunjunganPasienRsDiagnosa;
        $query   = $model::find();*/

        $post = [];
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');
        
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }

            if(isset($_GET['advanced-filter']['jenis_kelamin'])) {
                $_GET['advanced-filter']['jeniskelamin'] = $_GET['advanced-filter']['jenis_kelamin'];
                $post['jenis_kelamin'] = $_GET['advanced-filter']['jenis_kelamin'];
                unset($_GET['advanced-filter']['jenis_kelamin']);
            }

            if(isset($_GET['advanced-filter']['diagnosa_utama'])) {
                //$icdUtama = $_GET['advanced-filter']['diagnosa_utama'];
                $post['diagnosa_utama'] = $_GET['advanced-filter']['diagnosa_utama'];
                unset($_GET['advanced-filter']['diagnosa_utama']);
                //$query->andWhere(['diagnosa_utama_id' => $icdUtama]);
            }

            if(isset($_GET['advanced-filter']['diagnosa_penyerta'])) {
                //$icdPenyerta = $_GET['advanced-filter']['diagnosa_penyerta'];
                $post['diagnosa_penyerta'] = $_GET['advanced-filter']['diagnosa_penyerta'];
                unset($_GET['advanced-filter']['diagnosa_penyerta']);
                //$query->andWhere(['ilike', 'diagnosa_penyerta_id', $icdPenyerta]);
            }

            if(isset($_GET['advanced-filter']['status_periksa'])) {
                $status_periksa = $_GET['advanced-filter']['status_periksa'];
                if(!empty($status_periksa)) {
                    $post['status_periksa'] = $_GET['advanced-filter']['status_periksa'];
                    //$query->andWhere(['id_status_periksa' => $status_periksa]);
                }
                unset($_GET['advanced-filter']['status_periksa']);
            }

            if(isset($_GET['advanced-filter']['no_rekam_medik'])) {
                //$no_rekam_medik = $_GET['advanced-filter']['no_rekam_medik'];
                $post['no_rekam_medik'] = $_GET['advanced-filter']['no_rekam_medik'];
                //unset($_GET['advanced-filter']['no_rekam_medik']);
                // $query->andWhere(['no_rekam_medik' => $no_rekam_medik]);
            }

            if(isset($_GET['advanced-filter']['nama_pasien'])) {
                //$nama_pasien = $_GET['advanced-filter']['nama_pasien'];
                $post['nama_pasien'] = $_GET['advanced-filter']['nama_pasien'];
                unset($_GET['advanced-filter']['nama_pasien']);
                //$query->andWhere(['ilike', 'nama_pasien', $nama_pasien]);
            }

            if(isset($_GET['advanced-filter']['carabayar_id'])) {
                $carabayar_id = $_GET['advanced-filter']['carabayar_id'];
                if(!empty($carabayar_id)) {
                    $post['carabayar_id'] = $_GET['advanced-filter']['carabayar_id'];
                   // $query->andWhere(['carabayar_id' => $carabayar_id]);
                }
                unset($_GET['advanced-filter']['carabayar_id']);
            }

            if(isset($_GET['advanced-filter']['penjamin_id'])) {
                $penjamin_id = $_GET['advanced-filter']['penjamin_id'];
                if(!empty($penjamin_id)) {
                    $post['penjamin_id'] = $_GET['advanced-filter']['penjamin_id'];
                    //$query->andWhere(['penjamin_id' => $penjamin_id]);
                }
                unset($_GET['advanced-filter']['penjamin_id']);
            }

            if(isset($_GET['advanced-filter']['jeniskasuspenyakit_nama'])) {
                //$jeniskasuspenyakit_nama = $_GET['advanced-filter']['jeniskasuspenyakit_nama'];
                $post['jeniskasuspenyakit_nama'] = $_GET['advanced-filter']['jeniskasuspenyakit_nama'];
                unset($_GET['advanced-filter']['jeniskasuspenyakit_nama']);
                //$query->andWhere(['ilike', 'jeniskasuspenyakit_nama', $jeniskasuspenyakit_nama]);
            }

            if(isset($_GET['advanced-filter']['dokterdpjp_nama'])) {
                //$dokterdpjp_nama = $_GET['advanced-filter']['dokterdpjp_nama'];
                $post['dokterdpjp_nama'] = $_GET['advanced-filter']['dokterdpjp_nama'];
                unset($_GET['advanced-filter']['dokterdpjp_nama']);
                //$query->andWhere(['ilike', 'dokterdpjp_nama', $dokterdpjp_nama]);
            }

            if(isset($_GET['advanced-filter']['instalasi_id'])) {
                $instalasi_id = $_GET['advanced-filter']['instalasi_id'];
                if(!empty($instalasi_id)) {
                    $post['instalasi_id'] = $_GET['advanced-filter']['instalasi_id'];
                    //$query->andWhere(['instalasi_id' => $instalasi_id]);
                }
                unset($_GET['advanced-filter']['instalasi_id']);
            }

            if(isset($_GET['advanced-filter']['ruangan_id'])) {
                $ruangan_id = $_GET['advanced-filter']['ruangan_id'];
                if($ruangan_id != 'Loading ...' && !empty($ruangan_id)) {
                    $post['ruangan_id'] = $_GET['advanced-filter']['ruangan_id'];
                   //$query->andWhere(['ruangan_id' => $ruangan_id]);
                }
                unset($_GET['advanced-filter']['ruangan_id']);
            }

            if(isset($_GET['advanced-filter']['kondisikeluar_id'])) {
                $kondisikeluar_id = $_GET['advanced-filter']['kondisikeluar_id'];
                if($kondisikeluar_id != 'Loading ...' && !empty($kondisikeluar_id)) {
                    $post['kondisikeluar_id'] = $_GET['advanced-filter']['kondisikeluar_id'];
                    //$query->andWhere(['kondisikeluar_id' => $kondisikeluar_id]);
                }
                unset($_GET['advanced-filter']['kondisikeluar_id']);
            }

            if(isset($_GET['advanced-filter']['carakeluar_id'])) {
                $carakeluar_id = $_GET['advanced-filter']['carakeluar_id'];
                if($carakeluar_id != 'Loading ...' && !empty($carakeluar_id)) {
                    $post['carakeluar_id'] = $_GET['advanced-filter']['carakeluar_id'];
                    //$query->andWhere(['carakeluar_id' => $carakeluar_id]);
                }
                unset($_GET['advanced-filter']['carakeluar_id']);
            }

            if(isset($_GET['advanced-filter']['kunjungan_id'])) {
                $kunjungan_id = $_GET['advanced-filter']['kunjungan_id'];
                if($kunjungan_id != 'Loading ...' && !empty($kunjungan_id)) {
                    $post['kunjungan_id'] = $_GET['advanced-filter']['kunjungan_id'];
                    //$query->andWhere(['kunjungan_id' => $kunjungan_id]);
                }
                unset($_GET['advanced-filter']['kunjungan_id']);
            }
        }

        //$query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        //$tmpData = $query->all();
        $post['start'] = $start;
        $post['end'] = $end;

        $restSerconn = Yii::$app->serconn->guzzle('nonblocking');
        $headers = Yii::$app->request->headers;
        $post['authorization'] = $headers['authorization'];
        $post['x-owner'] = $headers['x-owner'];
        $request = $restSerconn->post('on/createreport/visits', [
            'body' => json_encode($post)
        ]);
        $request = json_decode($request->getBody(), True);
        $response['uid'] = $request['ProcessUID'];

        return $request['ProcessUID'];

        /*$title   = Yii::t('app', 'Laporan Kunjungan Pasien Rumah Sakit');
        $row     = $profil = $footer = [];
        try {
            $resQueryDetail = $this->generateData($tmpData, true);
            $no     = 1;
            foreach ($resQueryDetail as $value) {
                $tmp[1]  = $no;
                $tmp[2]  = date('d M Y', strtotime($value['tgl_pendaftaran']));
                $tmp[3]  = $value['no_pendaftaran'];
                $tmp[4]  = $value['no_rekam_medik'];
                $tmp[5]  = $value['jenis_kelamin'];
                $tmp[6]  = $value['nama_pasien'];
                $tmp[7]  = $value['tanggal_lahir'];
                $tmp[8]  = $value['umur'];
                $tmp[9]  = $value['alamat_pasien'];
                $tmp[10] = $value['carabayar_nama'];
                $tmp[11] = $value['penjamin_nama'];
                $tmp[12]  = $value['jeniskasuspenyakit_nama'];
                $tmp[13]  = $value['instalasi_nama'];
                $tmp[14]  = $value['ruangan_nama'];
                $tmp[15]  = $value['dokterdpjp_nama'];

                if (!empty($value['diagnosa_penyerta'])) {
                    $value['diagnosa_penyerta'] = str_replace('$', ', ', $value['diagnosa_penyerta']);
                }

                $tmp[16] = $value['diagnosa_utama'];
                $tmp[17] = $value['diagnosa_penyerta'];
                $tmp[18] = $value['status_periksa'];
                $tmp[19] = isset($value['no_telepon_pasien'])? $value['no_telepon_pasien'] : $value['no_mobile_pasien'];
                $tmp[20] = $value['kondisikeluar_nama'];
                $tmp[21] = $value['carakeluar_nama'];
                $tmp[22] = $value['kunjungan_nama'];
                $row[]   = $tmp;
                if ($carabayar_nama) $carabayar_nama = $value['carabayar_nama'];
                if ($penjamin_nama) $penjamin_nama   = $value['penjamin_nama'];
                if ($instalasi_nama) $instalasi_nama = $value['instalasi_nama'];
                if ($ruangan_nama) $ruangan_nama     = $value['ruangan_nama'];
                $no++;
            }
            $header = [
                'Tanggal Pendaftaran'     => $start.' Sampai Dengan '.$end,
                'No Rekam Medik'          => $no_rekam_medik,
                'Nama Pasien'             => $nama_pasien,
                'Jenis Kelamin'           => $jenis_kelamin,
                'Cara Bayar'              => $carabayar_nama,
                'Penjamin'                => $penjamin_nama,
                'Jenis Kasus Penyakit'    => $jeniskasuspenyakit_nama,
                'Instalasi'               => $instalasi_nama,
                'Ruangan'                 => $ruangan_nama,
                'Dokter Penanggung Jawab' => $nama_pegawai,
            ];

        } catch (Exception $e) {
            $header = [];
            $row = [];
        }
        $custHeader = [
                [
                    [
                        'label'=>'No',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Pendaftaran',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No Pendaftaran',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No Rekam Medik',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jenis Kelamin',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Nama Pasien',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Lahir',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Umur',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Alamat',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Cara bayar',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Penjamin',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jenis Kasus Penyakit',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Instalasi',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Ruangan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Dokter DPJP',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Diagnosa Utama',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Diagnosa Penyerta',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Status Periksa',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No Telepon Pasien',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Kondisi Pulang',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Cara Pulang',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Status Kunjungan',
                        'rowspan'=>2,
                    ],
                ]
            ];

        $filePath = DocoHelpers::exportExcel($title, $row, $header,array(
                "skipIncrement" => true,
                'customHeader' => $custHeader,
            ), $footer, [], true);
        $filePath->save('php://output');
        die();*/
        
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);

        if (isset($getData['per-page'])) unset($getData['per-page']);

        $fetchLimit = 50;
        $timeLimit = 3;
        $countData = $this->getObjectData()->count();
        $randString = DocoHelpers::generateRandomString();
        $totalPerPage = ceil($countData/$fetchLimit);

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $countData,
            'countData' => $countData,
            'sendToUrl' => 'lap-kunjungan/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('apotek'),
            'owner' => $xOwner,
            'token' => $auth
        ], 'laporan_kunjungan_pasien_rs',  'import_data_laporan_kunjungan_pasien_rs');

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData
        ];
    }


    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        $filePath = $request->get('filePath', null);
        if($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/".$filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $nameFile = $path .'/'. $model->file;
            if($files->saveAs($nameFile)) {
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


    private function generateData($tmpData, $isExcel = false)
    {
        $request = Yii::$app->request;
        $data = [];

        if(!empty($tmpData)) {
            $tmp = [];
            foreach($tmpData as $key => $value) {
                $pendaftranId = $value['pendaftaran_id'];
                $type = DocoConstants::INSTALASI_RAWAT_JALAN;

                if(isset($value['pasienadmisi_id']) && !empty($value['pasienadmisi_id'])) {
                    $type = DocoConstants::INSTALASI_RAWAT_INAP;
                } else if (empty($value['pasienadmisi_id']) && $value['instalasi_id'] == DocoConstants::VAR_I_RD) {
                    $type = DocoConstants::INSTALASI_RAWAT_DARURAT;
                }

                if(!isset($tmp[$type][$pendaftranId])) {
                    $tmp[$type][$pendaftranId] = $value;
                }
            }

            if(!empty($tmp)) {
                foreach($tmp as $k => $v) {
                    foreach($v as $kk => $vv) {
                        $data[] = $vv;
                    }
                }
            }
        }

        if($isExcel) {
            return $data;
        } else {
            return new ArrayDataProvider([
                'allModels' => $data, 
                'pagination' => [
                    'pageSize' => $request->get('per-page'),
                ],
            ]);
        }
    }

    public function actionGetDataKunjungan()
    {
        $request = Yii::$app->request;

        $model   = new LaporanKunjunganPasienRsDiagnosa;
        $query   = $model::find();

        $exceptionStatus = $this->getExceptionStatus();

        // $start   = date('Y-m-d 00:00:00');
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        $tgl_pendaftaran = $request->get('tgl_pendaftaran', null);
        $limit = $request->get('limit', null);
        $offset = $request->get('offset', null);

        //Filter
        $tgl_pendaftaran = $request->get('tgl_pendaftaran', null);
        $jenis_kelamin = $request->get('jenis_kelamin', null);
        $diagnosa_utama = $request->get('diagnosa_utama', null);
        $diagnosa_penyerta = $request->get('diagnosa_penyerta', null);
        $status_periksa = $request->get('status_periksa', null);
        $no_rekam_medik = $request->get('no_rekam_medik', null);
        $nama_pasien = $request->get('nama_pasien', null);
        $carabayar_id = $request->get('carabayar_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $jeniskasuspenyakit_nama = $request->get('jeniskasuspenyakit_nama', null);
        $dokterdpjp_nama = $request->get('dokterdpjp_nama', null);
        $instalasi_id = $request->get('instalasi_id', null);
        $ruangan_id = $request->get('ruangan_id', null);
        $kondisikeluar_id = $request->get('kondisikeluar_id', null);
        $carakeluar_id = $request->get('carakeluar_id', null);
        $kunjungan_id = $request->get('kunjungan_id', null);

        if($tgl_pendaftaran != null) {
            $explode = explode(" - ", $tgl_pendaftaran);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
        }

        if($jenis_kelamin != null) {
            $query->andWhere(['jeniskelamin' => $jenis_kelamin]);
        }

        if($diagnosa_utama != null) {
            $query->andWhere(['diagnosa_utama_id' => $diagnosa_utama]);
        }

        if($diagnosa_penyerta != null) {
            $query->andWhere(['ilike', 'diagnosa_penyerta_id', $diagnosa_penyerta]);
        }

        if($status_periksa != null) {
            $query->andWhere(['id_status_periksa' => $status_periksa]);
        }

        if($no_rekam_medik != null) {
            $query->andWhere(['no_rekam_medik' => $no_rekam_medik]);
        }

        if($nama_pasien != null) {
            $query->andWhere(['ilike', 'nama_pasien', $nama_pasien]);
        }

        if($carabayar_id != null) {
            $query->andWhere(['carabayar_id' => $carabayar_id]);
        }

        if($penjamin_id != null) {
            $query->andWhere(['penjamin_id' => $penjamin_id]);
        }

        if($jeniskasuspenyakit_nama != null) {
            $query->andWhere(['ilike', 'jeniskasuspenyakit_nama', $jeniskasuspenyakit_nama]);
        }

        if($dokterdpjp_nama != null) {
            $query->andWhere(['ilike', 'dokterdpjp_nama', $dokterdpjp_nama]);
        }

        if($instalasi_id != null) {
            $query->andWhere(['instalasi_id' => $instalasi_id]);
        }

        if($ruangan_id != null) {
            if($ruangan_id != 'Loading ...' && !empty($ruangan_id)) {
                $query->andWhere(['ruangan_id' => $ruangan_id]);
            }
        }

        if($kondisikeluar_id != null) {
            if($kondisikeluar_id != 'Loading ...' && !empty($kondisikeluar_id)) {
                $query->andWhere(['kondisikeluar_id' => $kondisikeluar_id]);
            }
        }

        if($carakeluar_id != null) {
            if($carakeluar_id != 'Loading ...' && !empty($carakeluar_id)) {
                $query->andWhere(['carakeluar_id' => $carakeluar_id]);
            }
        }

        if($kunjungan_id != null) {
            if($kunjungan_id != 'Loading ...' && !empty($kunjungan_id)) {
                $query->andWhere(['kunjungan_id' => $kunjungan_id]);
            }
        }

        if ( !empty($exceptionStatus) ) {
            $query->andWhere([
                'NOT IN', 'id_status_periksa', $exceptionStatus
            ]);
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);

        if($limit != null) {
            $query->limit($limit);
        }

        if($offset != null) {
            $query->offset($offset);
        }

        $result = $query->all();

        return [
             'results' => $result,
             'limit' => $limit,
             'offset' => $offset
        ];
    }

    public function actionGetTotalData()
    {
        $request = Yii::$app->request;

        $exceptionStatus = $this->getExceptionStatus();
        
        $model   = new LaporanKunjunganPasienRsDiagnosa;
        $query   = $model::find();
        
        // $start   = date('Y-m-d 00:00:00');
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        //Excel Header
        $no_rekam_medik          = '';
        $nama_pasien             = '';
        $jenis_kelamin_nama      = '';
        $carabayar_nama          = '';
        $penjamin_nama           = '';
        $jeniskasuspenyakit_nama = '';
        $instalasi_nama          = '';
        $ruangan_nama            = '';
        $dokterdpjp_nama         = '';

        //Filter
        $tgl_pendaftaran = $request->get('tgl_pendaftaran', null);
        $jenis_kelamin = $request->get('jenis_kelamin', null);
        $diagnosa_utama = $request->get('diagnosa_utama', null);
        $diagnosa_penyerta = $request->get('diagnosa_penyerta', null);
        $status_periksa = $request->get('status_periksa', null);
        $no_rekam_medik = $request->get('no_rekam_medik', null);
        $nama_pasien = $request->get('nama_pasien', null);
        $carabayar_id = $request->get('carabayar_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $jeniskasuspenyakit_nama = $request->get('jeniskasuspenyakit_nama', null);
        $dokterdpjp_nama = $request->get('dokterdpjp_nama', null);
        $instalasi_id = $request->get('instalasi_id', null);
        $ruangan_id = $request->get('ruangan_id', null);
        $kondisikeluar_id = $request->get('kondisikeluar_id', null);
        $carakeluar_id = $request->get('carakeluar_id', null);
        $kunjungan_id = $request->get('kunjungan_id', null);

        if($tgl_pendaftaran != null) {
            $explode = explode(" - ", $tgl_pendaftaran);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
        }

        if($jenis_kelamin != null) {
            $query->andWhere(['jeniskelamin' => $jenis_kelamin]);
            $jenis_kelamin_nama = Lookup::find()
            ->where(['lookup_type' => 'jenis_kelamin'])
            ->andWhere(['lookup_id' => $jenis_kelamin])
            ->one();
            $jenis_kelamin_nama = $jenis_kelamin_nama['lookup_name'];
        }

        if($diagnosa_utama != null) {
            $query->andWhere(['diagnosa_utama_id' => $diagnosa_utama]);
        }

        if($diagnosa_penyerta != null) {
            $query->andWhere(['ilike', 'diagnosa_penyerta_id', $diagnosa_penyerta]);
        }

        if($status_periksa != null) {
            $query->andWhere(['id_status_periksa' => $status_periksa]);
        }

        if($no_rekam_medik != null) {
            $query->andWhere(['no_rekam_medik' => $no_rekam_medik]);
        }

        if($nama_pasien != null) {
            $query->andWhere(['ilike', 'nama_pasien', $nama_pasien]);
        }

        if($carabayar_id != null) {
            $query->andWhere(['carabayar_id' => $carabayar_id]);
            $carabayar_nama = CaraBayar::find()
            ->where(['carabayar_id' => $carabayar_id])
            ->one();
            $carabayar_nama = $carabayar_nama['carabayar_nama'];
        }

        if($penjamin_id != null) {
            $query->andWhere(['penjamin_id' => $penjamin_id]);
            $penjamin_nama = Penjamin::find()
            ->where(['penjamin_id' => $penjamin_id])
            ->one();
            $penjamin_nama = $penjamin_nama['penjamin_nama'];
        }

        if($jeniskasuspenyakit_nama != null) {
            $query->andWhere(['ilike', 'jeniskasuspenyakit_nama', $jeniskasuspenyakit_nama]);
        }

        if($dokterdpjp_nama != null) {
            $query->andWhere(['ilike', 'dokterdpjp_nama', $dokterdpjp_nama]);
        }

        if($instalasi_id != null) {
            $query->andWhere(['instalasi_id' => $instalasi_id]);
            $instalasi_nama = Instalasi::find()
            ->where(['instalasi_id' => $instalasi_id])
            ->one();
            $instalasi_nama = $instalasi_nama['instalasi_nama'];
        }

        if($ruangan_id != null) {
            if($ruangan_id != 'Loading ...' && !empty($ruangan_id)) {
                $query->andWhere(['ruangan_id' => $ruangan_id]);
                $ruangan_nama = Ruangan::find()
                ->where(['ruangan_id' => $ruangan_id])
                ->one();
                $ruangan_nama = $ruangan_nama['ruangan_nama'];
            }
        }

        if($kondisikeluar_id != null) {
            if($kondisikeluar_id != 'Loading ...' && !empty($kondisikeluar_id)) {
                $query->andWhere(['kondisikeluar_id' => $kondisikeluar_id]);
            }
        }

        if($carakeluar_id != null) {
            if($carakeluar_id != 'Loading ...' && !empty($carakeluar_id)) {
                $query->andWhere(['carakeluar_id' => $carakeluar_id]);
            }
        }

        if($kunjungan_id != null) {
            if($kunjungan_id != 'Loading ...' && !empty($kunjungan_id)) {
                $query->andWhere(['kunjungan_id' => $kunjungan_id]);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        if ( !empty($exceptionStatus) ) {
            $query->andWhere([
                'NOT IN', 'id_status_periksa', $exceptionStatus
            ]);
        }
        $result = $query->count();
        $data_count = $result;
        $data['status'] = self::UPDATE;
        $data['countedData'] = $data_count;

        self::publishData($data);

        return [ 
            'total_data' => $data_count ,
            'header' => [
                'no_rekam_medik'          => $no_rekam_medik,
                'nama_pasien'             => $nama_pasien ,
                'jenis_kelamin'           => $jenis_kelamin_nama,
                'carabayar_nama'          => $carabayar_nama ,
                'penjamin_nama'           => $penjamin_nama ,
                'jeniskasuspenyakit_nama' => $jeniskasuspenyakit_nama,
                'instalasi_nama'          => $instalasi_nama,
                'ruangan_nama'            => $ruangan_nama,
                'dokterdpjp_nama'         => $dokterdpjp_nama,
            ]
        ];
    }

    public function actionDownloadExcel()
    {
        $post = file_get_contents('php://input');
        $post = json_decode($post);

        $data['status'] = self::FINISH;
        $data['filename'] = $post->download_url;
        self::publishData($data);
    }

    public function actionGetExcelUrl()
    {
        $request = Yii::$app->request;
        $uid = $request->get('uid', null);

        if ($uid != null) {
            $download_url = Yii::$app->cache->get('urlexcel_'.$uid);
        }
        $result['download_url'] = $download_url;

        return [
            'result' => $result,
        ];
    }

    private static function publishData($data)
    {
        $mode = Yii::$app->params['mode'];
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel-'.$mode,
            'message' => json_encode($data),
        ]);

        return true;
    }

    private function getExceptionStatus()
    {
        return Yii::$app->docoPlugin->execute('lap_kunjungan_status');
    }
}