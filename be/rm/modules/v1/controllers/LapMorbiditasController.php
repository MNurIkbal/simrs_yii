<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanMorbiditasView;
use app\modules\v1\models\LapMorbiditasView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\GolonganUmur;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;

class LapMorbiditasController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanMorbiditasView';
    const KEL_DIAG_ID = 'kelompokdiagnosa_id';
    const DIAG_ID = 'diagnosa_id';
    const I_RJ = 1;
    const I_RI = 3;
    const I_RD = 2;
    const INSTALASI_ID = 'instalasi_id';
    const RUANGAN_ID = 'ruangan_id';
    const PEGAWAI_ID = 'pegawai_id';
    public static $LIST_INSTALASI = [
        self::I_RJ, 
        self::I_RI,        
        self::I_RD,       
    ];
    public static $LIST_KEL_DIAGNOSA = [
        DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA, 
        DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA,           
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
        
        $model = new LaporanMorbiditasView;
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

        $modelDokter = new Pegawai;
        $queryDokter = $modelDokter::find()->where(['kelompokpegawai_id' => Pegawai::KELOMPOK_DOKTER]);
        
        $queryDokter = DocoRestActiveFilter::advancedFilter($modelDokter, $queryDokter);
        $queryDokter = new ActiveDataProvider([
            'query' => $queryDokter,
            'pagination' => false
        ]);

        $modelGolonganUmur = new GolonganUmur;
        $queryGolUmur = $modelGolonganUmur::find();

        $queryGolUmur = DocoRestActiveFilter::advancedFilter($modelGolonganUmur, $queryGolUmur);
        $queryGolUmur = new ActiveDataProvider([
            'query' => $queryGolUmur,
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
            'dokter' => $queryDokter->getModels() ?: [],
            'penjamin' => $queryPenjamin->getModels() ?: [],
            'caraBayar' => $queryCabar->getModels() ?: [],
            'golUmur' => $queryGolUmur->getModels() ?: [],
        ];
    }

    public function actionDataLaporan()
    {
        return $this->generateData();
    }

    private function generateData()
    {
        $request = Yii::$app->request;
        $tmp = $arrUtama = $arrPenyerta = $data = $modelData = [];
        $getRequest = $request->get();
        $limit = !empty($getRequest['per-page']) ? $getRequest['per-page'] : 10;

        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        $model = new LapMorbiditasView;
        $query = $model::find();
        $query->where(['in', self::KEL_DIAG_ID ,self::$LIST_KEL_DIAGNOSA]);

        if (array_key_exists('advanced-filter', $getRequest)) {
            if (array_key_exists('tgl_pendaftaran', $getRequest['advanced-filter'])) {
                $explode = explode(' - ', $getRequest['advanced-filter']['tgl_pendaftaran']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }

            if (array_key_exists(self::INSTALASI_ID, $getRequest['advanced-filter'])) {
                $instalasi_id = $getRequest['advanced-filter'][self::INSTALASI_ID];
                $query->andWhere([self::INSTALASI_ID => $instalasi_id]);
                unset($_GET['advanced-filter'][self::INSTALASI_ID]);
            }

            if (array_key_exists(self::RUANGAN_ID, $getRequest['advanced-filter'])) {
                $ruangan_id = $getRequest['advanced-filter'][self::RUANGAN_ID];
                $query->andWhere([self::RUANGAN_ID => $ruangan_id]);
                unset($_GET['advanced-filter'][self::RUANGAN_ID]);
            }

            if (array_key_exists(self::PEGAWAI_ID, $getRequest['advanced-filter'])) {
                $pegawai_id = $getRequest['advanced-filter'][self::PEGAWAI_ID];
                $query->andWhere([self::PEGAWAI_ID => $pegawai_id]);
                unset($_GET['advanced-filter'][self::PEGAWAI_ID]);
            }

            if (array_key_exists('golonganumur_id', $getRequest['advanced-filter'])) {
                $golonganumur_id = $getRequest['advanced-filter']['golonganumur_id'];
                $query->andWhere(['golonganumur_id' => $golonganumur_id]);
                unset($_GET['advanced-filter']['golonganumur_id']);
            }

            if (array_key_exists('carabayar_id', $getRequest['advanced-filter'])) {
                $carabayar_id = $getRequest['advanced-filter']['carabayar_id'];
                $query->andWhere(['carabayar_id' => $carabayar_id]);
                unset($_GET['advanced-filter']['carabayar_id']);
            }

            if (array_key_exists('penjamin_id', $getRequest['advanced-filter'])) {
                $penjamin_id = $getRequest['advanced-filter']['penjamin_id'];
                $query->andWhere(['penjamin_id' => $penjamin_id]);
                unset($_GET['advanced-filter']['penjamin_id']);
            }
        }


        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $modelData = $query->asArray()->all();
        // return $modelData;
        if(!empty($modelData)){
            foreach($modelData as $k => $v) {
                $count = 1;
                $kel_diag = $v[self::KEL_DIAG_ID];
                $diag_id = $v[self::DIAG_ID];
                if(!isset($tmp[$kel_diag][$diag_id])){
                    $tmp[$kel_diag][$diag_id] = $v;
                    $tmp[$kel_diag][$diag_id]['jumlah'] = $count;
                } else {
                    $tmp[$kel_diag][$diag_id]['jumlah']++;
                }
            }

            if(!empty($tmp)) {
                foreach($tmp as $k => $v) {
                    foreach($v as $kk => $vv) {
                        if($k == DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA) {
                            $arrUtama[] = [
                                'kode_diagnosa_utama' => $vv['diagnosa_kode'],
                                'nama_diagnosa_utama' => $vv['diagnosa_namalainnya'],
                                'jumlah_utama' => $vv['jumlah'],
                                'tgl_koreksi' => $vv['tglmorbiditas'],
                            ];
                        } else {
                            $arrPenyerta[] = [
                                'kode_diagnosa_penyerta' => $vv['diagnosa_kode'],
                                'nama_diagnosa_penyerta' => $vv['diagnosa_namalainnya'],
                                'jumlah_penyerta' => $vv['jumlah'],
                                'tgl_koreksi' => $vv['tglmorbiditas'],
                            ];
                        }
                    }
                }
            }
        }

        return $this->generateOneTable($arrUtama, $arrPenyerta, $limit);
    }

    private function generateOneTable($arrUtama, $arrPenyerta, $limit)
    {
        $counter = $limit;
        $no = 1;
        $data = $sortedArrUtama = $sortedArrPenyerta = [];

        if(!empty($arrUtama)) {
            ArrayHelper::multisort($arrUtama, ['jumlah_utama', 'tgl_koreksi'], [SORT_DESC, SORT_DESC]);
            foreach($arrUtama as $k => $v) {
                $sortedArrUtama[] = $v;
            }
        }

        if(!empty($arrPenyerta)) {
            ArrayHelper::multisort($arrPenyerta, ['jumlah_penyerta', 'tgl_koreksi'], [SORT_DESC, SORT_DESC]);
            foreach($arrPenyerta as $k => $v) {
                $sortedArrPenyerta[] = $v;
            }
        }

        for ($i=0; $i < $counter; $i++) {
            $jmlUtama =  (int) isset($sortedArrUtama[$i]['jumlah_utama']) ? $sortedArrUtama[$i]['jumlah_utama'] : 0;
            $jmlPenyerta = (int) isset($arrPenyerta[$i]['jumlah_penyerta']) ? $arrPenyerta[$i]['jumlah_penyerta'] : 0;
            $total = $jmlUtama + $jmlPenyerta;

            $data[] = [
                'rowNum' => $no++,
                'kode_diagnosa_utama' => isset($sortedArrUtama[$i]['kode_diagnosa_utama']) ? $sortedArrUtama[$i]['kode_diagnosa_utama'] : '-',
                'nama_diagnosa_utama' => isset($sortedArrUtama[$i]['nama_diagnosa_utama']) ? $sortedArrUtama[$i]['nama_diagnosa_utama'] : '-',
                'jumlah_utama' => isset($sortedArrUtama[$i]['jumlah_utama']) ? $sortedArrUtama[$i]['jumlah_utama'] : '-',
                'kode_diagnosa_penyerta' => isset($arrPenyerta[$i]['kode_diagnosa_penyerta']) ? $arrPenyerta[$i]['kode_diagnosa_penyerta'] : '-',
                'nama_diagnosa_penyerta' => isset($arrPenyerta[$i]['nama_diagnosa_penyerta']) ? $arrPenyerta[$i]['nama_diagnosa_penyerta'] : '-',
                'jumlah_penyerta' => isset($arrPenyerta[$i]['jumlah_penyerta']) ? $arrPenyerta[$i]['jumlah_penyerta'] : '-',
                'total' => $total,
                'tgl_pendaftaran' => isset($arrPenyerta[$i]['tgl_pendaftaran']) ? $arrPenyerta[$i]['tgl_pendaftaran'] : null,
                self::INSTALASI_ID => isset($arrPenyerta[$i][self::INSTALASI_ID]) ? $arrPenyerta[$i][self::INSTALASI_ID] : null,
                self::RUANGAN_ID => isset($arrPenyerta[$i][self::RUANGAN_ID]) ? $arrPenyerta[$i][self::RUANGAN_ID] : null,
                self::PEGAWAI_ID => isset($arrPenyerta[$i][self::PEGAWAI_ID]) ? $arrPenyerta[$i][self::PEGAWAI_ID] : null,
                'golonganumur_id' => isset($arrPenyerta[$i]['golonganumur_id']) ? $arrPenyerta[$i]['golonganumur_id'] : null,
                'carabayar_id' => isset($arrPenyerta[$i]['carabayar_id']) ? $arrPenyerta[$i]['carabayar_id'] : null,
                'penjamin_id' => isset($arrPenyerta[$i]['penjamin_id']) ? $arrPenyerta[$i]['penjamin_id'] : null,
            ];
        }

        return $data;
    }

    public function actionExportExcel()
    {
        $getRequest = Yii::$app->request->get();
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $instalasi = '-';
        $ruangan = '-';
        $dokter = '-';
        $golUmur = '-';
        $penjamin = '-';
        $carabayar = '-';
        $data = array();
        $tempData = $this->generateData();

        if (isset($getRequest['advanced-filter'])) {
            if (isset($getRequest['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $getRequest['advanced-filter']['tgl_pendaftaran']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }
            }

            if (isset($getRequest['advanced-filter'][self::INSTALASI_ID])) {
                $instalasi = Instalasi::findOne($getRequest['advanced-filter'][self::INSTALASI_ID])->instalasi_nama;
            }

            if (isset($getRequest['advanced-filter'][self::RUANGAN_ID])) {
                $ruangan = Ruangan::findOne($getRequest['advanced-filter'][self::RUANGAN_ID])->ruangan_nama;
            }

            if (isset($getRequest['advanced-filter'][self::PEGAWAI_ID])) {
                $dokter = Pegawai::findOne($getRequest['advanced-filter'][self::PEGAWAI_ID])->nama_pegawai;
            }
            
            if (isset($getRequest['advanced-filter']['golonganumur_id'])) {
                $golUmur = GolonganUmur::findOne($getRequest['advanced-filter']['golonganumur_id'])->golonganumur_namalainnya;
            }

            if (isset($getRequest['advanced-filter']['carabayar_id'])) {
                $carabayar = CaraBayar::findOne($getRequest['advanced-filter']['carabayar_id'])->carabayar_nama;
            }

            if (isset($getRequest['advanced-filter']['penjamin_id'])) {
                $penjamin = Penjamin::findOne($getRequest['advanced-filter']['penjamin_id'])->penjamin_nama;
            }
        }


        if (!empty($tempData)) {
            foreach ($tempData as $key => $value) {
                $data[] = [
                    "kode_diagnosa_utama" => $value['kode_diagnosa_utama'],
                    "nama_diagnosa_utama" => $value['nama_diagnosa_utama'],
                    "jumlah_utama" => $value['jumlah_utama'],
                    "kode_diagnosa_penyerta" => $value['kode_diagnosa_penyerta'],
                    "nama_diagnosa_penyerta" => $value['nama_diagnosa_penyerta'],
                    "jumlah_penyerta" => $value['jumlah_penyerta'],
                    "total" => $value['total']
                ];
            }
        }

        $header = [
            'Tanggal Pendaftaran' => $start.' - '.$end,
            'Instalasi' => $instalasi,
            'Ruangan' => $ruangan,
            'Dokter' => $dokter,
            'Golongan Umur' => $golUmur,
            'Carabayar' => $carabayar,
            'Penjamin' => $penjamin,
        ];

        $filePath = DocoHelpers::exportExcel(
            'Laporan Morbiditas',
            $data,
            $header,
            [
                'uploadPath' => './uploads'
            ],
            [],
            [],
            true
        );
        $filePath->save('php://output');
        die;
    }
}