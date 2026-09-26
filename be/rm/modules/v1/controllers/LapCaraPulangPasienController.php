<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanCaraPulangV;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\CaraKeluar;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\KondisiKeluar;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;
use app\modules\v1\models\UploadForm;

class LapCaraPulangPasienController extends DocoActiveController
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
        
        $model   = new LaporanCaraPulangV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

            if (array_key_exists('rumahsakit_rujukan', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'rumahsakit_rujukan', $_GET['advanced-filter']['rumahsakit_rujukan']]);
    
                unset($_GET['advanced-filter']['rumahsakit_rujukan']);
            }
        }

        $query->andWhere(['between', 'tgl_pulang', $start, $end]);
        
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetRuanganBy($id)
    {
        $data = Ruangan::find()->where(['instalasi_id' => $id]);

        return $data->all();
    }

    public function actionGenerateApi()
    {
        // instalasi
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find()->all();
        // Cara Keluar
        $modelCaraKeluar = new CaraKeluar;
        $queryICaraKeluar = $modelCaraKeluar::find()
            ->where(['is_deleted' => false])
            ->andWhere(['is_active' => true])->all();
        // Kondisi Keluar
        $modelKondisiKeluar = new KondisiKeluar;
        $queryKondisiKeluar = $modelKondisiKeluar::find()
            ->where(['is_deleted' => false])
            ->andWhere(['is_active' => true])->all();
        // Cara Bayar
        $modelCaraBayar = new CaraBayar;
        $queryCaraBayar = $modelCaraBayar::find()
            ->select('carabayar_id, carabayar_nama')
            ->where(['is_deleted' => false])
            ->andWhere(['is_active' => true])->all();

        return [
            'instalasi' => $queryInstalasi,
            'cara_pulang' => $queryICaraKeluar,
            'kondisi_pulang' => $queryKondisiKeluar,
            'cara_bayar' => $queryCaraBayar
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        
        $instalasi_nama          = '';
        $ruangan_nama            = '';

        $model   = new LaporanCaraPulangV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

            if (array_key_exists('instalasi_id', $_GET['advanced-filter'])) {
                $instalasi_nama = Instalasi::findOne($_GET['advanced-filter']['instalasi_id'])->instalasi_nama;
                $query->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);

                unset($_GET['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('ruangan_id', $_GET['advanced-filter'])) {
                $ruangan_nama = Ruangan::findOne($_GET['advanced-filter']['ruangan_id'])->ruangan_nama;
                $query->andWhere(['ruangan_id' => $_GET['advanced-filter']['ruangan_id']]);

                unset($_GET['advanced-filter']['ruangan_id']);
            }

            if (array_key_exists('carapulang_id', $_GET['advanced-filter'])) {
                $query->andWhere(['carapulang_id' => $_GET['advanced-filter']['carapulang_id']]);

                unset($_GET['advanced-filter']['carapulang_id']);
            }

            if (array_key_exists('kondisipulang_id', $_GET['advanced-filter'])) {
                $query->andWhere(['kondisipulang_id' => $_GET['advanced-filter']['kondisipulang_id']]);

                unset($_GET['advanced-filter']['kondisipulang_id']);
            }

            if (array_key_exists('no_rekam_medik', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_rekam_medik', $_GET['advanced-filter']['no_rekam_medik']]);

                unset($_GET['advanced-filter']['no_rekam_medik']);
            }

            if (array_key_exists('no_registrasi', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_registrasi', $_GET['advanced-filter']['no_registrasi']]);

                unset($_GET['advanced-filter']['no_registrasi']);
            }

            if (array_key_exists('nama_pasien', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'nama_pasien', $_GET['advanced-filter']['nama_pasien']]);

                unset($_GET['advanced-filter']['nama_pasien']);
            }

            if (array_key_exists('rumahsakit_rujukan', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'rumahsakit_rujukan', $_GET['advanced-filter']['rumahsakit_rujukan']]);
    
                unset($_GET['advanced-filter']['rumahsakit_rujukan']);
            }
        }

        $query->andWhere(['between', 'tgl_pulang', $start, $end]);
        

        $title   = Yii::t('app', 'LAPORAN CARA PULANG PASIEN RUMAH SAKIT');
        $row     = $footer = [];
        try {
            $resQueryDetail = $query->all();
            $no     = 1;
            foreach ($resQueryDetail as $value) {
                $tmp[1]  = $no;
                $tmp[2]  = $value['no_registrasi'];
                $tmp[3]  = $value['no_rekam_medik'];
                $tmp[4]  = $value['nama_pasien'];
                $tmp[5]  = $value['instalasi_nama'];
                $tmp[6]  = $value['ruangan_nama'];
                $tmp[7]  = $value['cara_pulang'];
                $tmp[8]  = $value['kondisi_pulang'];

                $row[]   = $tmp;
                $no++;
            }
        } catch (Exception $e) {
            $row = [];
        }
        $header = [
            'PERIODE' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            'INSTALASI' => $instalasi_nama,
            'RUANGAN' => $ruangan_nama
        ];
        $custHeader = [
                [
                    [
                        'label'=>'No',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No Registrasi',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'No Rekam Medik',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Nama Pasien',
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
                        'label'=>'Cara Pulang',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Kondisi Pulang',
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
    * @attribute #ruangan_nama# => Nama Ruangan
    * @attribute #periode# => Periode
    * @attribute #data# => Data
    **/
    public function actionExportPdf() 
    {
        $instalasi_nama          = '';
        $ruangan_nama            = '';

        $model   = new LaporanCaraPulangV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

            if (array_key_exists('instalasi_id', $_GET['advanced-filter'])) {
                $instalasi_nama = Instalasi::findOne($_GET['advanced-filter']['instalasi_id'])->instalasi_nama;
                $query->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);

                unset($_GET['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('ruangan_id', $_GET['advanced-filter'])) {
                $ruangan_nama = Ruangan::findOne($_GET['advanced-filter']['ruangan_id'])->ruangan_nama;
                $query->andWhere(['ruangan_id' => $_GET['advanced-filter']['ruangan_id']]);

                unset($_GET['advanced-filter']['ruangan_id']);
            }

            if (array_key_exists('carapulang_id', $_GET['advanced-filter'])) {
                $query->andWhere(['carapulang_id' => $_GET['advanced-filter']['carapulang_id']]);

                unset($_GET['advanced-filter']['carapulang_id']);
            }

            if (array_key_exists('kondisipulang_id', $_GET['advanced-filter'])) {
                $query->andWhere(['kondisipulang_id' => $_GET['advanced-filter']['kondisipulang_id']]);

                unset($_GET['advanced-filter']['kondisipulang_id']);
            }

            if (array_key_exists('no_rekam_medik', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_rekam_medik', $_GET['advanced-filter']['no_rekam_medik']]);

                unset($_GET['advanced-filter']['no_rekam_medik']);
            }

            if (array_key_exists('no_registrasi', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_registrasi', $_GET['advanced-filter']['no_registrasi']]);

                unset($_GET['advanced-filter']['no_registrasi']);
            }

            if (array_key_exists('nama_pasien', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'nama_pasien', $_GET['advanced-filter']['nama_pasien']]);

                unset($_GET['advanced-filter']['nama_pasien']);
            }

            if (array_key_exists('rumahsakit_rujukan', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'rumahsakit_rujukan', $_GET['advanced-filter']['rumahsakit_rujukan']]);
    
                unset($_GET['advanced-filter']['rumahsakit_rujukan']);
            }
        }

        $query->andWhere(['between', 'tgl_pulang', $start, $end]);
        $data = $query->all();

        $print = new DocoPrint();

        $print->attributes = [
            '#title#' => 'LAPORAN CARA PULANG PASIEN',
            '#periode#' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            '#instalasi_nama#' => $instalasi_nama,
            '#ruangan_nama#' => $ruangan_nama,
            '#data#' => $this->renderPartial('_cetak_pdf', [
                'data' => $data
            ]),
        ];
        $print->Output();
    }

    public function actionSyncPdf() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $filter = isset($getData['params']) ? $getData['params'] : [];
        
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        if (isset($filter['page'])) unset($filter['page']);
        if (isset($filter['per-page'])) unset($filter['per-page']);
        $fetchLimit = 20;
        $countData = $this->getDataLaporan()->count();
        
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);
        (new InternalService)->sendTo([
            'Sirs' => [ 
                'LaporanCaraPulangPasien' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $filter
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'CetakLaporanCaraPulangPasien' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLaporanCaraPulangPasien' => [
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

    public function getDataLaporan()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $filter = isset($getData['params']) ? $getData['params'] : [];
        
        $model   = new LaporanCaraPulangV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($filter['advanced-filter'])) {
            if(isset($filter['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $filter['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($filter['advanced-filter']['tgl_pulang']);
            }

            if (array_key_exists('instalasi_id', $filter['advanced-filter'])) {
                $instalasi_nama = Instalasi::findOne($filter['advanced-filter']['instalasi_id'])->instalasi_nama;
                $query->andWhere(['instalasi_id' => $filter['advanced-filter']['instalasi_id']]);

                unset($filter['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('ruangan_id', $filter['advanced-filter'])) {
                $ruangan_nama = Ruangan::findOne($filter['advanced-filter']['ruangan_id'])->ruangan_nama;
                $query->andWhere(['ruangan_id' => $filter['advanced-filter']['ruangan_id']]);

                unset($filter['advanced-filter']['ruangan_id']);
            }

            if (array_key_exists('carapulang_id', $filter['advanced-filter'])) {
                $query->andWhere(['carapulang_id' => $filter['advanced-filter']['carapulang_id']]);

                unset($filter['advanced-filter']['carapulang_id']);
            }

            if (array_key_exists('kondisipulang_id', $filter['advanced-filter'])) {
                $query->andWhere(['kondisipulang_id' => $filter['advanced-filter']['kondisipulang_id']]);

                unset($filter['advanced-filter']['kondisipulang_id']);
            }

            if (array_key_exists('no_rekam_medik', $filter['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_rekam_medik', $filter['advanced-filter']['no_rekam_medik']]);

                unset($filter['advanced-filter']['no_rekam_medik']);
            }

            if (array_key_exists('no_registrasi', $filter['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_registrasi', $filter['advanced-filter']['no_registrasi']]);

                unset($filter['advanced-filter']['no_registrasi']);
            }

            if (array_key_exists('nama_pasien', $filter['advanced-filter'])) {
                $query->andWhere(['ilike', 'nama_pasien', $filter['advanced-filter']['nama_pasien']]);

                unset($filter['advanced-filter']['nama_pasien']);
            }

            if (array_key_exists('rumahsakit_rujukan', $filter['advanced-filter'])) {
                $query->andWhere(['ilike', 'rumahsakit_rujukan', $filter['advanced-filter']['rumahsakit_rujukan']]);
    
                unset($filter['advanced-filter']['rumahsakit_rujukan']);
            }

            if (array_key_exists('carabayar_id', $filter['advanced-filter'])) {
                $query->andWhere(['carabayar_id' => $filter['advanced-filter']['carabayar_id']]);

                unset($filter['advanced-filter']['carabayar_id']);
            }
        }

        $query->andWhere(['between', 'tgl_pulang', $start, $end]);

        return DocoRestActiveFilter::advancedFilter($model, $query);    
    }

    public function actionGetObjectData()
    {
        $instalasi_nama          = '';
        $ruangan_nama            = '';

        $model   = new LaporanCaraPulangV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pulang']);
            }

            if (array_key_exists('instalasi_id', $_GET['advanced-filter'])) {
                $instalasi_nama = Instalasi::findOne($_GET['advanced-filter']['instalasi_id'])->instalasi_nama;
                $query->andWhere(['instalasi_id' => $_GET['advanced-filter']['instalasi_id']]);

                unset($_GET['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('ruangan_id', $_GET['advanced-filter'])) {
                $ruangan_nama = Ruangan::findOne($_GET['advanced-filter']['ruangan_id'])->ruangan_nama;
                $query->andWhere(['ruangan_id' => $_GET['advanced-filter']['ruangan_id']]);

                unset($_GET['advanced-filter']['ruangan_id']);
            }

            if (array_key_exists('carapulang_id', $_GET['advanced-filter'])) {
                $query->andWhere(['carapulang_id' => $_GET['advanced-filter']['carapulang_id']]);

                unset($_GET['advanced-filter']['carapulang_id']);
            }

            if (array_key_exists('kondisipulang_id', $_GET['advanced-filter'])) {
                $query->andWhere(['kondisipulang_id' => $_GET['advanced-filter']['kondisipulang_id']]);

                unset($_GET['advanced-filter']['kondisipulang_id']);
            }

            if (array_key_exists('no_rekam_medik', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_rekam_medik', $_GET['advanced-filter']['no_rekam_medik']]);

                unset($_GET['advanced-filter']['no_rekam_medik']);
            }

            if (array_key_exists('no_registrasi', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_registrasi', $_GET['advanced-filter']['no_registrasi']]);

                unset($_GET['advanced-filter']['no_registrasi']);
            }

            if (array_key_exists('nama_pasien', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'nama_pasien', $_GET['advanced-filter']['nama_pasien']]);

                unset($_GET['advanced-filter']['nama_pasien']);
            }

            if (array_key_exists('rumahsakit_rujukan', $_GET['advanced-filter'])) {
                $query->andWhere(['ilike', 'rumahsakit_rujukan', $_GET['advanced-filter']['rumahsakit_rujukan']]);
    
                unset($_GET['advanced-filter']['rumahsakit_rujukan']);
            }

            if (array_key_exists('carabayar_id', $_GET['advanced-filter'])) {
                $query->andWhere(['carabayar_id' => $_GET['advanced-filter']['carabayar_id']]);

                unset($_GET['advanced-filter']['carabayar_id']);
            }
        }

        $query->andWhere(['between', 'tgl_pulang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query, $_GET);
        $data = $query->all();


        $attributes = [
            '#title#' => 'LAPORAN CARA PULANG PASIEN',
            '#periode#' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            '#instalasi_nama#' => $instalasi_nama,
            '#ruangan_nama#' => $ruangan_nama,
            '#data#' => $this->renderPartial('_cetak_pdf', [
                'data' => $data
            ]),
        ];
        $periode = date('d-M-Y', strtotime($start)).' - '.date('d-M-Y', strtotime($end));
        return [
            'periode'=>$periode,
            'attributes'=>$attributes,
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

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        
        $data = $this->getDataLaporanExcel($getData)->asArray()->all();
        $limit = 20;
        $countData = count($data);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$limit);

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanCaraPulangPasienExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportCaraPulangPasien' => [
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
                'UploadLaporanCaraPulangPasienExcel' => [
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
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Cara Pulang Pasien.xlsx';

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

    public function getDataLaporanExcel($params)
    {
        $request = Yii::$app->request;
        $model   = new LaporanCaraPulangV;
        $query   = $model::find();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($params['advanced-filter'])) {
            if(isset($params['advanced-filter']['tgl_pulang'])) {
                $explode = explode(" - ", $params['advanced-filter']['tgl_pulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($params['advanced-filter']['tgl_pulang']);
            }

            if (array_key_exists('instalasi_id', $params['advanced-filter'])) {
                $instalasi_nama = Instalasi::findOne($params['advanced-filter']['instalasi_id'])->instalasi_nama;
                $query->andWhere(['instalasi_id' => $params['advanced-filter']['instalasi_id']]);

                unset($params['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('ruangan_id', $params['advanced-filter'])) {
                $ruangan_nama = Ruangan::findOne($params['advanced-filter']['ruangan_id'])->ruangan_nama;
                $query->andWhere(['ruangan_id' => $params['advanced-filter']['ruangan_id']]);

                unset($params['advanced-filter']['ruangan_id']);
            }

            if (array_key_exists('carapulang_id', $params['advanced-filter'])) {
                $query->andWhere(['carapulang_id' => $params['advanced-filter']['carapulang_id']]);

                unset($params['advanced-filter']['carapulang_id']);
            }

            if (array_key_exists('kondisipulang_id', $params['advanced-filter'])) {
                $query->andWhere(['kondisipulang_id' => $params['advanced-filter']['kondisipulang_id']]);

                unset($params['advanced-filter']['kondisipulang_id']);
            }

            if (array_key_exists('no_rekam_medik', $params['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_rekam_medik', $params['advanced-filter']['no_rekam_medik']]);

                unset($params['advanced-filter']['no_rekam_medik']);
            }

            if (array_key_exists('no_registrasi', $params['advanced-filter'])) {
                $query->andWhere(['ilike', 'no_registrasi', $params['advanced-filter']['no_registrasi']]);

                unset($params['advanced-filter']['no_registrasi']);
            }

            if (array_key_exists('nama_pasien', $params['advanced-filter'])) {
                $query->andWhere(['ilike', 'nama_pasien', $params['advanced-filter']['nama_pasien']]);

                unset($params['advanced-filter']['nama_pasien']);
            }

            if (array_key_exists('rumahsakit_rujukan', $params['advanced-filter'])) {
                $query->andWhere(['ilike', 'rumahsakit_rujukan', $params['advanced-filter']['rumahsakit_rujukan']]);
    
                unset($params['advanced-filter']['rumahsakit_rujukan']);
            }

            if (array_key_exists('carabayar_id', $params['advanced-filter'])) {
                $query->andWhere(['carabayar_id' => $params['advanced-filter']['carabayar_id']]);

                unset($params['advanced-filter']['carabayar_id']);
            }
        }

        $query->andWhere(['between', 'tgl_pulang', $start, $end]);
        

        return DocoRestActiveFilter::advancedFilter($model, $query);
    }
}