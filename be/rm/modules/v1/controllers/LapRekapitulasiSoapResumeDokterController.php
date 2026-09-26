<?php

namespace app\modules\v1\controllers;

use Yii;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\InfopasienbelumsoapV;
use app\modules\v1\models\InfopasienbelumsoapdetailV;
use app\modules\v1\models\InfodokterbelumisirmV;
use app\modules\v1\models\InfodokterbelumisirmdetailV;
use app\modules\v1\models\InforuanganinstalasipegV;
use app\modules\v1\models\LaporanRekapitulasiSoapResumeDokterFn;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class LapRekapitulasiSoapResumeDokterController extends DocoActiveController
{
    /**
     * @inheritdoc
     */
    public $modelClass = '';
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
        $request = Yii::$app->request;
        $getRequest = $request->get();
        
        return $this->generateData($getRequest);
    }

    public function actionGetDataPasien()
    {
        $request = Yii::$app->request;
        $instalasi_id = $request->get(self::INSTALASI_ID);
        $ruangan_id = $request->get(self::RUANGAN_ID);
        $pegawai_id = $request->get(self::PEGAWAI_ID);
        $jenis_laporan = $request->get('jenis_laporan');
        $tgl_pendaftaran = $request->get('tgl_pendaftaran');
        $explode = explode('/', $tgl_pendaftaran);
                
        if (count($explode) == 2) {
            $start = date('Y-m-d', strtotime($explode[0]));
            $end = date('Y-m-d', strtotime($explode[1]));
        } else {
            $start = date('Y-m-d');
            $end = date('Y-m-d');
        }

        unset($_GET['advanced-filter']['tgl_pendaftaran']); 

        if($jenis_laporan) {
            if($jenis_laporan == DocoConstants::L_T_SOAP_DOKTER) {
                $model = new InfopasienbelumsoapdetailV;
            } else {
                $model = new InfodokterbelumisirmdetailV;
            }
        } else {
            $model = new InfopasienbelumsoapdetailV;
        }
        $query = $model::find()->andWhere([
            self::INSTALASI_ID => $instalasi_id,
            self::RUANGAN_ID => $ruangan_id,
            self::PEGAWAI_ID => $pegawai_id
        ]);

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetNama()
    {
        $getRequest = Yii::$app->request->get();
        $instalasi_id = $getRequest[self::INSTALASI_ID];
        $ruangan_id = $getRequest[self::RUANGAN_ID];
        $pegawai = $getRequest['pegawai'];

        $dokter = Pegawai::findOne($pegawai)->nama_pegawai;
        $instalasi_nama = Instalasi::findOne($instalasi_id)->instalasi_nama;
        $ruangan_nama = Ruangan::findOne($ruangan_id)->ruangan_nama;

        return [
            'instalasi_nama' => $instalasi_nama,
            'ruangan_nama' => $ruangan_nama,
            'dokter' => $dokter,
        ];
    }

    /**
    * @controller actionExportPdf
    * @attribute #title# => Judul
    * @attribute #datatable# => Table
    * @attribute #tgl_pendaftaran# => Filter tanggal pendaftaran
    * @attribute #instalasi# => Filter instalasi
    * @attribute #ruangan# => Filter ruangan
    * @attribute #dokter# => Filter dokter
    **/
    public function actionExportPdf() 
    {
        $getRequest = Yii::$app->request->get();
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $instalasi = '-';
        $ruangan = '-';
        $dokter = '-';
        $jenis_laporan = DocoConstants::L_T_SOAP_DOKTER;
        $print = new DocoPrint();
        $data = $this->generateData($getRequest, true);

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

            if (isset($getRequest['advanced-filter']['jenis_laporan'])) {
                $jenis_laporan = $getRequest['advanced-filter']['jenis_laporan'];
            }
        }

        if($jenis_laporan == DocoConstants::L_T_RESUME_DOKTER) {
            $title = 'LAPORAN REKAPITULASI RESUME DOKTER BELUM TERISI';
        } else {
            $title = 'LAPORAN REKAPITULASI SOAP DOKTER BELUM TERISI';
        }

        $print->attributes = [
            '#title#' => $title,
            '#tgl_pendaftaran#' => $start.' - '.$end,
            '#instalasi#' => $instalasi,
            '#ruangan#' => $ruangan,
            '#dokter#' => $dokter,
            '#datatable#' => $this->renderPartial('index', [
                'data' => $data
            ]),
        ];
        $print->Output();
    }

    public function actionExportExcel()
    {
        $getRequest = Yii::$app->request->get();
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $instalasi = '-';
        $ruangan = '-';
        $dokter = '-';
        $jenis_laporan = DocoConstants::L_T_SOAP_DOKTER;
        $tempData = $this->generateData($getRequest, true);

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

            if (isset($getRequest['advanced-filter']['jenis_laporan'])) {
                $jenis_laporan = $getRequest['advanced-filter']['jenis_laporan'];
            }
        }

        if($jenis_laporan == DocoConstants::L_T_RESUME_DOKTER) {
            $jenisTxt = 'Resume';
            $title = 'LAPORAN REKAPITULASI RESUME DOKTER BELUM TERISI';
        } else {
            $jenisTxt = 'SOAP';
            $title = 'LAPORAN REKAPITULASI SOAP DOKTER BELUM TERISI';
        }

        $data = $footer = [];
        if (!empty($tempData)) {
            $no = 1;
            foreach ($tempData as $key => $value) {
                $tmp[1] = $no;
                $tmp['Instalasi'] = $value['instalasi_nama'];
                $tmp['Ruangan'] = $value['ruangan_nama'];
                $tmp['Dokter'] = $value['nama_dokter'];
                $tmp['Jumlah Pasien'] = $value['jumlah_pasien'];
                $data[] = $tmp;
                $no++;
            }
        }

        $header = [
            'Laporan' => $jenisTxt,
            'Tanggal Pendaftaran' => $start.' - '.$end,
            'Instalasi' => $instalasi,
            'Ruangan' => $ruangan,
            'Dokter' => $dokter,
        ];

        $custHeader = [
            [
                [
                    'label' => 'No',
                    'rowspan' => 2,
                ],
                [
                    'label' => 'Laporan Rekapitulasi SOAP Belum Terisi',
                    'rowspan'=>1,
                    'colspan'=>4,
                ]
            ]
        ];

        $filePath = DocoHelpers::exportExcel(
            $title,
            $data,
            $header,
            [
                'skipIncrement' => true,
                'customHeader' => $custHeader
            ],
            $footer,
            [],
            true
        );
        $filePath->save('php://output');
        die;
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

        $modelJenisLaporan = new Lookup;
        $qryJenisLaporan = $modelJenisLaporan::find()->where(['lookup_type' => Lookup::JENIS_LAPORAN]);
        $qryJenisLaporan = DocoRestActiveFilter::advancedFilter($modelJenisLaporan, $qryJenisLaporan);
        $qryJenisLaporan = new ActiveDataProvider([
            'query' => $qryJenisLaporan,
            'pagination' => false
        ]);

        return [
            'instalasi' => $queryInstalasi->getModels(),
            'ruangan' => $queryRuangan->getModels(),
            'dokter' => $queryDokter->getModels(),
            'jenisLaporan' => $qryJenisLaporan->getModels(),
        ];
    }

    private function generateData($filter, $isExport = false)
    {
        $start = date('Y-m-d');
        $end = date('Y-m-d');
        $jenis_laporan = DocoConstants::L_T_SOAP_DOKTER;
        $instalasi_id = $ruangan_id = $pegawai_id = null;

        if (array_key_exists('advanced-filter', $filter)) {
            if (array_key_exists('tgl_pendaftaran', $filter['advanced-filter'])) {
                $explode = explode(' - ', $filter['advanced-filter']['tgl_pendaftaran']);
                
                if (count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));
                }

                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            }

            if (array_key_exists('jenis_laporan', $filter['advanced-filter'])) {
                $jenis_laporan = $filter['advanced-filter']['jenis_laporan'];
                unset($_GET['advanced-filter']['jenis_laporan']);
            }

            if (array_key_exists('instalasi_id', $filter['advanced-filter'])) {
                $instalasi_id = $filter['advanced-filter']['instalasi_id'];
                unset($_GET['advanced-filter']['instalasi_id']);
            }

            if (array_key_exists('ruangan_id', $filter['advanced-filter'])) {
                $ruangan_id = $filter['advanced-filter']['ruangan_id'];
                unset($_GET['advanced-filter']['ruangan_id']);
            }

            if (array_key_exists('pegawai_id', $filter['advanced-filter'])) {
                $pegawai_id = $filter['advanced-filter']['pegawai_id'];
                unset($_GET['advanced-filter']['pegawai_id']);
            }
        }

        $model = LaporanRekapitulasiSoapResumeDokterFn::getData($start, $end, $jenis_laporan);
        
        if ($instalasi_id != null) {
            $model->andWhere(['instalasi_id' => $instalasi_id]);
        }

        if ($ruangan_id != null) {
            $model->andWhere(['ruangan_id' => $ruangan_id]);
        }

        if ($pegawai_id != null) {
            $model->andWhere(['pegawai_id' => $pegawai_id]);
        }
        
        $data = $model->asArray()->all();

        if($isExport) {
            return $data;
        } else {
            return new ArrayDataProvider([
                'allModels' => $data, 
                'pagination' => [
                    'pageSize' => $filter['per-page'],
                ],
            ]);
        }
    }
}