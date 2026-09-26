<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPtmV;
use app\modules\v1\models\LaporanPtmEkgV;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\UploadForm;
use app\modules\v1\payload\UploadPayload;
use Doco\Services\InternalService;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\web\UploadedFile;

class LapPtmController extends DocoActiveController
{
    public $modelClass = '';

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
        $request = Yii::$app->request;
        
        $model   = new LaporanPtmV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_registrasi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_registrasi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_registrasi']);
            }
        }

        $query->andWhere(['between', 'tgl_registrasi', $start, $end]);
        // $query->andWhere(['not', ['diag_utama_kode' => null, 'diag_utama' => null]]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDetail()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasien_id = $request->get('pasien_id');
        $instalasi_id = $request->get('instalasi_id');
        $tgl_registrasi = $request->get('tgl_registrasi');
        $tgl_registrasi = date('Y-m-d H:i:s', strtotime($tgl_registrasi));
        $tgl_awal = $request->get('tgl_awal', null);
        $start = !empty($tgl_awal)? date('Y-m-d 00:00:00', strtotime($tgl_awal)) : date('Y-m-d 00:00:00');
        
        $modelEkg   = new LaporanPtmEkgV;
        $data_ekg   = $modelEkg::find()
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->andWhere(['instalasi_id' => $instalasi_id])
            ->all();

        $modelPtm = new LaporanPtmV;
        $data_JK = $modelPtm::find()
            ->where(['pasien_id' => $pasien_id])
            ->andWhere(['instalasi_id' => $instalasi_id])
            ->andWhere(['between', 'tgl_registrasi', $start, $tgl_registrasi])
            ->all();
        $jumlah_kunjungan = count($data_JK);
        
        return ['data' => [
            'ekg' => $data_ekg,
            'jumlah_kunjungan' => $jumlah_kunjungan
        ]];
    }

    public function actionGenerateApi()
    {
        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find()->all();

        return [
            'instalasi' => $queryInstalasi,
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        
        $instalasi_nama          = '';
        $diagnosa_nama           = '';

        $model   = new LaporanPtmV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_registrasi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_registrasi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                //unset($_GET['advanced-filter']['tgl_registrasi']);
            }

            if (array_key_exists('instalasi_id', $_GET['advanced-filter'])) {
                $instalasi_nama = Instalasi::findOne($_GET['advanced-filter']['instalasi_id'])->instalasi_nama;
                $query->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);

                //unset($_GET['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('diagnosa_id', $_GET['advanced-filter'])) {
                //$diagnosa_nama = Diagnosa::findOne($_GET['advanced-filter']['diagnosa_id'])->diagnosa_nama;
                $diagnosa_nama = 'by Diagnosa';
                $query->andWhere(['diagnosa_id' => $_GET['advanced-filter']['diagnosa_id']]);

                //unset($_GET['advanced-filter']['diagnosa_id']);
            }
        }

        $query->andWhere(['between', 'tgl_registrasi', $start, $end]);
        $query->orderBy(['tgl_registrasi' => 'desc']);
        
        $title   = Yii::t('app', 'LAPORAN PTM RUMAH SAKIT');
        $row     = $footer = [];
        try {
            $resQueryDetail = $query->all();
            $no     = 1;
            foreach ($resQueryDetail as $value) {
                $tgl_registrasi = date('Y-m-d H:i:s', strtotime($value['tgl_registrasi']));
                $value['tgl_registrasi'] = date('d M Y H:i:s', strtotime($value['tgl_registrasi']));
                $value['tgl_pulang'] =  isset($value['tgl_pulang'])?date('d M Y H:i:s', strtotime($value['tgl_pulang'])):'';
                $value['tanggal_lahir'] = date('d M Y', strtotime($value['tanggal_lahir']));

                $value['pemeriksaan_ekg'] = 'Tidak';
                $modelEkg   = LaporanPtmEkgV::find()
                    ->where(['pendaftaran_id' => $value['pendaftaran_id']])
                    ->andWhere(['instalasi_id' => $value['instalasi_id']])
                    ->andWhere(['pemeriksaan_ekg' => 'YA'])
                    ->all();
                if(!empty($modelEkg)) {
                    $value['pemeriksaan_ekg'] = 'Ya';
                }

                $identitas_pasien = json_decode($value['no_identitas_pasien'], true);
                $no_ktp = '';
                if(is_array($identitas_pasien)) {
                    foreach ($identitas_pasien as $identitas) {
                        if($identitas['jenisidentitas'] == '94') {
                            $no_ktp = $identitas['no_identitas_pasien'];
                        }
                    }
                }
                $diag_utama_kode = isset($value['diag_utama_kode'])?$value['diag_utama_kode']:'';
                $diag_utama = isset($value['diag_utama'])?$value['diag_utama']:'';
                $diagnosa =  $diag_utama_kode. ' - ' .$diag_utama;
                $nama_keluarga = !empty($value['nama_ayah'])?$value['nama_ayah']:$value['nama_ibu'];
                $nama_keluarga = isset($nama_keluarga)?$nama_keluarga:'';

                $queryJmlKunjungan   = (new \yii\db\Query())
                    ->select([
                        'COUNT(pasien_id) AS jumlah_kunjungan'
                    ])
                    ->from('laporanptm_v')
                    ->where(['pasien_id' => $value['pasien_id']])
                    ->andWhere(['instalasi_id' => $value['instalasi_id']]);
                
                if(isset($_GET['advanced-filter'])) {
                    if (array_key_exists('instalasi_id', $_GET['advanced-filter'])) {
                        $queryJmlKunjungan->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);
                    }
                    if (array_key_exists('diagnosa_id', $_GET['advanced-filter'])) {
                        $queryJmlKunjungan->andWhere(['diagnosa_id' => $_GET['advanced-filter']['diagnosa_id']]);
                    }
                }
                $queryJmlKunjungan->andWhere(['between', 'tgl_registrasi', $start, $tgl_registrasi]);
                $jml_kunjungan = $queryJmlKunjungan->one();
                $object = preg_replace("/<br \/>/", " ", $value['object']);
                
                $tmp[1]  = $no;
                $tmp[2]  = $no_ktp;
                $tmp[3]  = $value['nopeserta_bpjs'];
                $tmp[4]  = $value['nama_pasien'];
                $tmp[5]  = $value['no_rekam_medik'];
                $tmp[6]  = $value['tanggal_lahir'];
                $tmp[7]  = $value['no_telepon_pasien'];
                $tmp[8]  = $value['alamatemail'];
                $tmp[9]  = $value['alamat_pasien'];
                $tmp[10]  = $value['tgl_registrasi'];
                $tmp[11]  = $value['no_registrasi'];
                $tmp[12]  = $diagnosa;
                $tmp[13]  = $object;
                $tmp[14]  = $value['umur'];
                $tmp[15]  = $jml_kunjungan['jumlah_kunjungan'];
                $tmp[16]  = $value['nama_dokter'];
                $tmp[17]  = $value['golongan_darah'];
                $tmp[18]  = $value['pemeriksaan_ekg'];
                $tmp[19]  = $nama_keluarga;
                $tmp[20]  = $value['tgl_pulang'];
                $tmp[21]  = $value['keadaan_sekarang'];

                $row[]   = $tmp;
                $no++;
            }
        } catch (Exception $e) {
            $row = [];
        }
        $header = [
            'Periode' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            'Pilihan Registrasi' => $instalasi_nama,
            'Searching' => $diagnosa_nama
        ];
        $custHeader = [
                [
                    [
                        'label'=>'No',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No KTP',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No BPJS',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Nama Pasien',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No Rekam Medik',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Lahir',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No HP',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Email',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Alamat',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Registrasi',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No Registrasi',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Diagnosa',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'SOAP (O)',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Umur',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jumlah Kunjungan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Nama Dokter',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Golongan Darah',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Pemeriksaan EKG',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Nama Keluarga',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Tanggal Pulang',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Keadaan Sekarang',
                        'rowspan'=>2,
                    ],
                ]
            ];

        $filePath = DocoHelpers::exportExcel($title, $row, $header,array(
                "skipIncrement" => true,
                'customHeader' => $custHeader,
            ), $footer, [], true);
        $filePath->save('php://output');
        die();
        
    }

    /**
    * @controller actionExportPdf
    * @attribute #title# => Judul
    * @attribute #instalasi_nama# => Nama Instalasi
    * @attribute #diagnosa_nama# => Nama Diagnosa
    * @attribute #periode# => Periode
    * @attribute #data# => Data
    **/
    public function actionExportPdf() 
    {
        $instalasi_nama          = '';
        $diagnosa_nama           = '';
        $data = [];

        $model   = new LaporanPtmV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_registrasi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_registrasi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                //unset($_GET['advanced-filter']['tgl_registrasi']);
            }

            if (array_key_exists('instalasi_id', $_GET['advanced-filter'])) {
                $instalasi_nama = Instalasi::findOne($_GET['advanced-filter']['instalasi_id'])->instalasi_nama;
                $query->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);

                //unset($_GET['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('diagnosa_id', $_GET['advanced-filter'])) {
                //$diagnosa_nama = Diagnosa::findOne($_GET['advanced-filter']['diagnosa_id'])->diagnosa_nama;
                $diagnosa_nama = 'Searching: by Diagnosa';
                $query->andWhere(['diagnosa_id' => $_GET['advanced-filter']['diagnosa_id']]);

                //unset($_GET['advanced-filter']['diagnosa_id']);
            }
        }

        $query->andWhere(['between', 'tgl_registrasi', $start, $end]);
        $query->orderBy(['tgl_registrasi' => 'desc']);
        $resQueryDetail = $query->all();
        
        foreach ($resQueryDetail as $key => $value) {
            $tgl_registrasi = date('Y-m-d H:i:s', strtotime($value['tgl_registrasi']));
            $value['tgl_registrasi'] = date('d M Y H:i:s', strtotime($value['tgl_registrasi']));
            $value['tgl_pulang'] =  isset($value['tgl_pulang'])?date('d M Y H:i:s', strtotime($value['tgl_pulang'])):'';
            $value['tanggal_lahir'] = date('d M Y', strtotime($value['tanggal_lahir']));

            $value['pemeriksaan_ekg'] = 'Tidak';
            $modelEkg   = LaporanPtmEkgV::find()
                ->where(['pendaftaran_id' => $value['pendaftaran_id']])
                ->andWhere(['instalasi_id' => $value['instalasi_id']])
                ->andWhere(['pemeriksaan_ekg' => 'YA'])
                ->all();
            if(!empty($modelEkg)) {
                $value['pemeriksaan_ekg'] = 'Ya';
            }

            $identitas_pasien = json_decode($value['no_identitas_pasien'], true);
            $no_ktp = '';
            if(is_array($identitas_pasien)) {
                foreach ($identitas_pasien as $identitas) {
                    if($identitas['jenisidentitas'] == '94') {
                        $no_ktp = $identitas['no_identitas_pasien'];
                    }
                }
            }
            $value['no_identitas_pasien'] = $no_ktp;

            $diag_utama_kode = isset($value['diag_utama_kode'])?$value['diag_utama_kode']:'';
            $diag_utama = isset($value['diag_utama'])?$value['diag_utama']:'';
            $diagnosa =  $diag_utama_kode. ' - ' .$diag_utama;
            $value['diag_utama'] = $diagnosa;

            $nama_keluarga = !empty($value['nama_ayah'])?$value['nama_ayah']:$value['nama_ibu'];
            $nama_keluarga = isset($nama_keluarga)?$nama_keluarga:'';
            $value['nama_ayah'] = $nama_keluarga;

            $queryJmlKunjungan   = (new \yii\db\Query())
                ->select([
                    'COUNT(pasien_id) AS jumlah_kunjungan'
                ])
                ->from('laporanptm_v')
                ->where(['pasien_id' => $value['pasien_id']])
                ->andWhere(['instalasi_id' => $value['instalasi_id']]);
            
            if(isset($_GET['advanced-filter'])) {
                if (array_key_exists('instalasi_id', $_GET['advanced-filter'])) {
                    $queryJmlKunjungan->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);
                }
                if (array_key_exists('diagnosa_id', $_GET['advanced-filter'])) {
                    $queryJmlKunjungan->andWhere(['diagnosa_id' => $_GET['advanced-filter']['diagnosa_id']]);
                }
            }
            $queryJmlKunjungan->andWhere(['between', 'tgl_registrasi', $start, $tgl_registrasi]);
            $jml_kunjungan = $queryJmlKunjungan->one();
            $value['jumlah_kunjungan'] = $jml_kunjungan['jumlah_kunjungan'];
            
            $data[$key] = $value;
        }

        $print = new DocoPrint();

        $print->attributes = [
            '#title#' => 'LAPORAN PTM RUMAH SAKIT',
            '#periode#' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            '#instalasi_nama#' => $instalasi_nama,
            '#diagnosa_nama#' => $diagnosa_nama,
            '#data#' => $this->renderPartial('_cetak_pdf', [
                'data' => $data
            ]),
        ];
        $print->Output();
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $data = $this->getDataLaporan($getData)->asArray()->all();
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = count($data);
        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanPtmExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportLaporanPtmExcel' => [
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
                'UploadLaporanPtmExcel' => [
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

    public function getDataLaporan($params)
    {
        $request = Yii::$app->request;
        
        $model   = new LaporanPtmV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($params['advanced-filter'])) {
            if(isset($params['advanced-filter']['tgl_registrasi'])) {
                $explode = explode(" - ", $params['advanced-filter']['tgl_registrasi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($params['advanced-filter']['tgl_registrasi']);
            }
        }

        $query->andWhere(['between', 'tgl_registrasi', $start, $end]);
        // $query->andWhere(['not', ['diag_utama_kode' => null, 'diag_utama' => null]]);

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm();
        
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

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/lap-ptm.xlsx';

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

    public function actionSyncExportPdf() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();

        $filter = isset($getData) ? $getData : [];
        
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        if (isset($filter['page'])) unset($filter['page']);
        if (isset($filter['per-page'])) unset($filter['per-page']);
        $fetchLimit = 20;
        $countData = $this->getDataLaporan($getData)->count();
        
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [ 
                'LaporanPtmPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $filter
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakLaporanPtmPdf' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLaporanPtmPdf' => [
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

    public function actionGetObjectData()
    {
        $request = Yii::$app->request;
        $instalasi_nama          = '';
        $diagnosa_nama           = '';
        $data = [];
        $attributes = [];

        $model   = new LaporanPtmV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_registrasi'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_registrasi']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                //unset($_GET['advanced-filter']['tgl_registrasi']);
            }

            if (array_key_exists('instalasi_id', $_GET['advanced-filter'])) {
                $instalasi_nama = Instalasi::findOne($_GET['advanced-filter']['instalasi_id'])->instalasi_nama;
                $query->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);

                //unset($_GET['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('diagnosa_id', $_GET['advanced-filter'])) {
                //$diagnosa_nama = Diagnosa::findOne($_GET['advanced-filter']['diagnosa_id'])->diagnosa_nama;
                $diagnosa_nama = 'Searching: by Diagnosa';
                $query->andWhere(['diagnosa_id' => $_GET['advanced-filter']['diagnosa_id']]);

                //unset($_GET['advanced-filter']['diagnosa_id']);
            }
        }


        // $query->andWhere(['not', ['diag_utama_kode' => null, 'diag_utama' => null]]);
        $query->andWhere(['between', 'tgl_registrasi', $start, $end]);
        $query->orderBy(['tgl_registrasi' => 'desc']);
        $resQueryDetail = $query->all();
        
        foreach ($resQueryDetail as $key => $value) {
            $tgl_registrasi = date('Y-m-d H:i:s', strtotime($value['tgl_registrasi']));
            $value['tgl_registrasi'] = date('d M Y H:i:s', strtotime($value['tgl_registrasi']));
            $value['tgl_pulang'] =  isset($value['tgl_pulang'])?date('d M Y H:i:s', strtotime($value['tgl_pulang'])):'';
            $value['tanggal_lahir'] = date('d M Y', strtotime($value['tanggal_lahir']));

            $value['pemeriksaan_ekg'] = 'Tidak';
            $modelEkg   = LaporanPtmEkgV::find()
                ->where(['pendaftaran_id' => $value['pendaftaran_id']])
                ->andWhere(['instalasi_id' => $value['instalasi_id']])
                ->andWhere(['pemeriksaan_ekg' => 'YA'])
                ->all();
            if(!empty($modelEkg)) {
                $value['pemeriksaan_ekg'] = 'Ya';
            }

            $nik_pasien = json_decode($value['no_identitas_pasien'], true);
            $identitas_pasien = json_decode($value['additional_pasien'], true);
            $no_ktp = '';
            if(is_array($identitas_pasien) && ! empty($identitas_pasien)) {
                foreach ($identitas_pasien as $identitas) {
                    if($identitas['jenisidentitas'] == '94') {
                        $no_ktp = $identitas['no_identitas_pasien'];
                    }
                }
            } else {
                if (!is_array($nik_pasien) && ! empty($nik_pasien)) {
                    $no_ktp = $nik_pasien;
                }
            }
            $value['no_identitas_pasien'] = $no_ktp;

            $diag_utama_kode = isset($value['diag_utama_kode'])?$value['diag_utama_kode']:'';
            $diag_utama = isset($value['diag_utama'])?$value['diag_utama']:'';
            $diagnosa =  $diag_utama_kode. ' - ' .$diag_utama;
            $value['diag_utama'] = $diagnosa;

            $nama_keluarga = !empty($value['nama_ayah'])?$value['nama_ayah']:$value['nama_ibu'];
            $nama_keluarga = isset($nama_keluarga)?$nama_keluarga:'';
            $value['nama_ayah'] = $nama_keluarga;

            $queryJmlKunjungan   = (new \yii\db\Query())
                ->select([
                    'COUNT(pasien_id) AS jumlah_kunjungan'
                ])
                ->from('laporanptm_v')
                ->where(['pasien_id' => $value['pasien_id']])
                ->andWhere(['instalasi_id' => $value['instalasi_id']]);
            
            if(isset($_GET['advanced-filter'])) {
                if (array_key_exists('instalasi_id', $_GET['advanced-filter'])) {
                    $queryJmlKunjungan->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);
                }
                if (array_key_exists('diagnosa_id', $_GET['advanced-filter'])) {
                    $queryJmlKunjungan->andWhere(['diagnosa_id' => $_GET['advanced-filter']['diagnosa_id']]);
                }
            }
            $queryJmlKunjungan->andWhere(['between', 'tgl_registrasi', $start, $tgl_registrasi]);
            $jml_kunjungan = $queryJmlKunjungan->one();
            $value['jumlah_kunjungan'] = $jml_kunjungan['jumlah_kunjungan'];
            
            $data[$key] = $value;
        }
        $periode = date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end));
        $attributes = [
            '#title#' => 'LAPORAN PTM RUMAH SAKIT',
            '#periode#' => $periode,
            '#instalasi_nama#' => $instalasi_nama,
            '#diagnosa_nama#' => $diagnosa_nama,
            '#data#' => $this->renderPartial('_cetak_pdf', [
                'data' => $data
            ]),
        ];

        return [
            'periode'=>$periode,
            'attributes'=>$attributes,

        ]; 
    }

    public function actionSendFilePdf()
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

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $rootPath = 'uploads';
        $file = $rootPath.'/'.$fileName.'.pdf';
        if(file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
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