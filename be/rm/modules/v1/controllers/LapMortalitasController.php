<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use app\modules\v1\models\LaporanMortalitasView;
use app\modules\v1\models\LaporankematianV;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;

class LapMortalitasController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanMortalitasView';
    const I_RJ = 1;
    const I_RI = 3;
    const I_RD = 2;
    const KTP = 94;
    const INSTALASI_ID = 'instalasi_id';
    const RUANGAN_ID = 'ruangan_id';
    const TGL_PASIEN_PLG = 'tglpasienpulang';
    const CARABAYAR_ID = 'carabayar_id';
    const PENJAMIN_ID = 'penjamin_id';
    const ADV_FILTER = 'advanced-filter';
    public static $LIST_INSTALASI = [
        self::I_RJ, 
        self::I_RI,        
        self::I_RD,       
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["cau"] = ["POST", "GET"];
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
        // $_GET['expand'] = $request->get('expand', 'kelompokjabatan_m,indexing_m');
        
        $model = new LaporanMortalitasView;
        $query = $model::find();

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionListDiagnosa()
    {
        try {
            $request = Yii::$app->request;

            $data = Diagnosa::find()->select([
                "diagnosa_id",
                "diagnosa_kode",
            ])->asArray()->all();

            return [
                'data' => $data
            ];
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

    private static function modelKematian($new = false)
    {
        if($new) {
            return new LaporankematianV;
        } else {
            return LaporankematianV::find();
        }
    }

    public function actionGenerateApi()
    {
        $modelInstalasi = new Instalasi;
        $queryInstalasi = $modelInstalasi::find()
        ->where(['in', self::INSTALASI_ID,self::$LIST_INSTALASI]);

        $queryInstalasi = DocoRestActiveFilter::advancedFilter($modelInstalasi, $queryInstalasi);
        $queryInstalasi = new ActiveDataProvider([
            'query' => $queryInstalasi,
            'pagination' => false
        ]);

        $modelRuangan = new Ruangan;
        $queryRuangan = $modelRuangan::find()
        ->where(['in', self::INSTALASI_ID,self::$LIST_INSTALASI]);

        $queryRuangan = DocoRestActiveFilter::advancedFilter($modelRuangan, $queryRuangan);
        $queryRuangan = new ActiveDataProvider([
            'query' => $queryRuangan,
            'pagination' => false
        ]);
        
        $modelCabar = new CaraBayar;
        $queryCabar = $modelCabar::find();

        $queryCabar = DocoRestActiveFilter::advancedFilter($modelCabar, $queryCabar);
        $queryCabar = new ActiveDataProvider([
            'query' => $queryCabar,
            'pagination' => false
        ]);

        $modelPenjamin = new Penjamin;
        $queryPenjamin = $modelPenjamin::find();

        $queryPenjamin = DocoRestActiveFilter::advancedFilter($modelPenjamin, $queryPenjamin);
        $queryPenjamin = new ActiveDataProvider([
            'query' => $queryPenjamin,
            'pagination' => false
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels() ?: [],
            'ruangan' => $queryRuangan->getModels() ?: [],
            'penjamin' => $queryPenjamin->getModels() ?: [],
            'caraBayar' => $queryCabar->getModels() ?: [],
        ];
    }

    public function actionDataLaporan()
    {
        try {
            $request = Yii::$app->request;
            $getRequest = $request->get();
            
            $model   = self::modelKematian(true);
            $query   = self::modelKematian();
            
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');

            if (array_key_exists(self::ADV_FILTER, $getRequest)) {
                if (array_key_exists(self::TGL_PASIEN_PLG, $getRequest[self::ADV_FILTER])) {
                    $explode = explode(' - ', $getRequest[self::ADV_FILTER][self::TGL_PASIEN_PLG]);
                    
                    if (count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
    
                    unset($_GET[self::ADV_FILTER][self::TGL_PASIEN_PLG]);
                }
    
                if (array_key_exists(self::INSTALASI_ID, $getRequest[self::ADV_FILTER])) {
                    $instalasi_id = $getRequest[self::ADV_FILTER][self::INSTALASI_ID];
                    $query->andWhere([self::INSTALASI_ID => $instalasi_id]);
                    unset($_GET[self::ADV_FILTER][self::INSTALASI_ID]);
                }
    
                if (array_key_exists(self::RUANGAN_ID, $getRequest[self::ADV_FILTER])) {
                    $ruangan_id = $getRequest[self::ADV_FILTER][self::RUANGAN_ID];
                    $query->andWhere([self::RUANGAN_ID => $ruangan_id]);
                    unset($_GET[self::ADV_FILTER][self::RUANGAN_ID]);
                }
    
                if (array_key_exists(self::CARABAYAR_ID, $getRequest[self::ADV_FILTER])) {
                    $carabayar_id = $getRequest[self::ADV_FILTER][self::CARABAYAR_ID];
                    $query->andWhere([self::CARABAYAR_ID => $carabayar_id]);
                    unset($_GET[self::ADV_FILTER][self::CARABAYAR_ID]);
                }
    
                if (array_key_exists(self::PENJAMIN_ID, $getRequest[self::ADV_FILTER])) {
                    $penjamin_id = $getRequest[self::ADV_FILTER][self::PENJAMIN_ID];
                    $query->andWhere([self::PENJAMIN_ID => $penjamin_id]);
                    unset($_GET[self::ADV_FILTER][self::PENJAMIN_ID]);
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

    public function actionExportExcel()
    {
        $title = 'Laporan Mortalitas';
        $instalasi = '-';
        $ruangan = '-';
        $penjamin = '-';
        $carabayar = '-';
        $data = [];
        $request = Yii::$app->request;
        $getRequest = $request->get();
        
        $model   = self::modelKematian(true);
        $query   = self::modelKematian();
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if (array_key_exists(self::ADV_FILTER, $getRequest)) {
            if (array_key_exists(self::TGL_PASIEN_PLG, $getRequest[self::ADV_FILTER])) {
                $explode = explode(' - ', $getRequest[self::ADV_FILTER][self::TGL_PASIEN_PLG]);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }

                unset($_GET[self::ADV_FILTER][self::TGL_PASIEN_PLG]);
            }

            if (array_key_exists(self::INSTALASI_ID, $getRequest[self::ADV_FILTER])) {
                $instalasi_id = $getRequest[self::ADV_FILTER][self::INSTALASI_ID];
                $query->andWhere([self::INSTALASI_ID => $instalasi_id]);
                unset($_GET[self::ADV_FILTER][self::INSTALASI_ID]);
            }

            if (array_key_exists(self::RUANGAN_ID, $getRequest[self::ADV_FILTER])) {
                $ruangan_id = $getRequest[self::ADV_FILTER][self::RUANGAN_ID];
                $query->andWhere([self::RUANGAN_ID => $ruangan_id]);
                unset($_GET[self::ADV_FILTER][self::RUANGAN_ID]);
            }

            if (array_key_exists(self::CARABAYAR_ID, $getRequest[self::ADV_FILTER])) {
                $carabayar_id = $getRequest[self::ADV_FILTER][self::CARABAYAR_ID];
                $query->andWhere([self::CARABAYAR_ID => $carabayar_id]);
                unset($_GET[self::ADV_FILTER][self::CARABAYAR_ID]);
            }

            if (array_key_exists(self::PENJAMIN_ID, $getRequest[self::ADV_FILTER])) {
                $penjamin_id = $getRequest[self::ADV_FILTER][self::PENJAMIN_ID];
                $query->andWhere([self::PENJAMIN_ID => $penjamin_id]);
                unset($_GET[self::ADV_FILTER][self::PENJAMIN_ID]);
            }
        }

        $query->andWhere(['between', self::TGL_PASIEN_PLG, $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        $dataProvider = new ActiveDataProvider(['query' => $query, 'pagination' => false]);
        $tmpData = $dataProvider->getModels();
        $no = 1;
        if(!empty($tmpData)) {
            foreach ($tmpData as $key => $value) {
                $ktp = '';
                $arrUmur = !empty($value['tanggal_lahir']) ? self::generateUmur($value['tanggal_lahir'], $value['tgl_meninggal']) : [];
                $rtRw = !empty($value['rt']) || !empty($value['rw']) ? $value['rt']."/".$value['rw'] : "";
                if($value['no_identitas_pasien'] && self::isJson($value['no_identitas_pasien'])) {
                    $tmpIdentitas = json_decode($value['no_identitas_pasien'], true);
                    foreach($tmpIdentitas as $k => $v) {
                        if($v['jenisidentitas'] == self::KTP) {
                            $ktp = $v['no_identitas_pasien'];
                        }
                    }
                } else {
                    $ktp = $value['no_identitas_pasien'];
                }

                $tmp[1] = $no;
                $tmp[2] = $value['nama_pasien'];
                $tmp[3] = $value['no_rekam_medik'];
                $tmp[4] = $ktp . " ";
                $tmp[5] = $value['jeniskelamin'];
                $tmp[6] = $value['tempat_lahir'];
                $tmp[7] = date('d', strtotime($value['tanggal_lahir']));
                $tmp[8] = date('M', strtotime($value['tanggal_lahir']));
                $tmp[9] = date('Y', strtotime($value['tanggal_lahir']));
                $tmp[10] = $value['pendidikan_nama'];
                $tmp[11] = $value['pekerjaan_nama'];
                $tmp[12] = $value['status_kependudukan'];
                $tmp[13] = $value['alamat_pasien'];
                $tmp[14] = $rtRw;
                $tmp[15] = $value['kelurahan_nama'];
                $tmp[16] = $value['kecamatan_nama'];
                $tmp[17] = $value['kabupaten_nama'];
                $tmp[18] = $value['penanggungjawab_notelp'];
                $tmp[19] = date('d-M-y', strtotime($value['tgl_meninggal']));
                $tmp[20] = date('H:i', strtotime($value['tgl_meninggal']));
                $tmp[21] = !empty($arrUmur) ? $arrUmur['tahun'] : "";
                $tmp[22] = !empty($arrUmur) ? $arrUmur['bulan'] : "";
                $tmp[23] = !empty($arrUmur) ? $arrUmur['hari'] : "";
                $tmp[24] = $value['lahir_mati'];
                $tmp[25] = "";
                $tmp[26] = $value['tempat_meninggal'];
                $tmp[27] = $value['diagnosa_utama'];
                $tmp[28] = $value['diagnosa_penyerta'];
                $tmp[29] = $value['kondisikeluar_nama'];
                $tmp[30] = $value['rencana_pemulasaran'];
                $tmp[31] = $value['tempat_pemulasaran'];
                $tmp[32] = $value['dokter_menerangkan'];
                $data[] = $tmp;
                $no++;
            }
        }

        $header = [
            'Tanggal Pulang' => date('d-m-Y', strtotime($start))." - ".  date('d-m-Y', strtotime($end)),
            'Instalasi' => $instalasi,
            'Ruangan' => $ruangan,
            'Carabayar' => $carabayar,
            'Penjamin' => $penjamin,
        ];

        $firstRow = [
            [
                'label' => 'No',
                'rowspan' => 2
            ],
            [
                'label' => 'Nama Lengkap',
                'rowspan' => 2
            ],
            [
                'label' => 'No Rekam Medik',
                'rowspan' => 2
            ],
            [
                'label' => 'Nomor Induk Kependudukan',
                'rowspan' => 2
            ],
            [
                'label' => 'Jenis Kelamin',
                'rowspan' => 2
            ],
            [
                'label' => 'Tempat Lahir',
                'rowspan' => 2
            ],
            [
                'label' => 'Tanggal Lahir',
                'colspan' => 3
            ],
            [
                'label' => 'Pendidikan',
                'rowspan' => 2
            ],
            [
                'label' => 'Pekerjaan',
                'rowspan' => 2
            ],
            [
                'label' => 'Status Kependudukan',
                'rowspan' => 2
            ],
            [
                'label' => 'Alamat Sesuai KTP',
                'colspan' => 6
            ],
            [
                'label' => 'Jam Meninggal',
                'colspan' => 2
            ],
            [
                'label' => 'Umur Saat Meninggal',
                'colspan' => 3
            ],
            [
                'label' => 'Apakah Lahir Mati',
                'rowspan' => 2
            ],
            [
                'label' => 'Khusus Perempuan 10-54 tahun, almarhum dlm keadaan',
                'rowspan' => 2
            ],
            [
                'label' => 'Tempat Meninggal',
                'rowspan' => 2
            ],
            [
                'label' => 'Diagnosa Utama',
                'rowspan' => 2
            ],
            [
                'label' => 'Diagnosa Penyerta',
                'rowspan' => 2
            ],
            [
                'label' => 'Jika Meninggal Di Rs',
                'rowspan' => 2
            ],
            [
                'label' => 'Rencana Pemulasaran',
                'rowspan' => 2
            ],
            [
                'label' => 'Tempat Pemulasaran',
                'rowspan' => 2
            ],
            [
                'label' => 'Dokter Menerangkan',
                'rowspan' => 2
            ],
        ];
        $secondRow = [
            [
                'label' => 'Tanggal',
                'startfrom' => 7
            ],
            [
                'label' => 'Bulan',
            ],
            [
                'label' => 'Tahun',
            ],
            [
                'label' => 'Jalan/Gang',
                'startfrom' => 4
            ],
            [
                'label' => 'RT/RW',
            ],
            [
                'label' => 'Kel/Desa',
            ],
            [
                'label' => 'Kecamatan',
            ],
            [
                'label' => 'Kota/Kabupaten',
            ],
            [
                'label' => 'Telp Keluarga',
            ],
            [
                'label' => 'Tgl/Bulan/Tahun',
            ],
            [
                'label' => 'Jam',
            ],
            [
                'label' => 'Tahun',
            ],
            [
                'label' => 'Bulan',
            ],
            [
                'label' => 'Hari',
            ],
        ];

        $custHeader = [
            $firstRow,
            $secondRow,
        ];

        $filePath = DocoHelpers::exportExcel($title, $data, $header ,array(
            "skipIncrement" => true,
            'customHeader' => $custHeader,
        ), [], [], true);
        $filePath->save('php://output');
        die();
    }

    private static function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * @function : generate umur (helper doco bug sudah dipake banyak tempat sepertinya)
     */
    private static function generateUmur($date, $dateDie)
    {
        $arr_umur = [];
        $tmpDate = date('Y-m-d', strtotime($date));
        $diff = date_diff(date_create(date('Y-m-d', strtotime($tmpDate))), date_create(date('Y-m-d', strtotime($dateDie))));

        $arr_umur['hari'] = $diff->d;
        $arr_umur['bulan'] = $diff->m;
        $arr_umur['tahun'] = $diff->y;

        return $arr_umur;
    }
}