<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanBatalRegV;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LapRekapPasienBatalRegistrasiController extends DocoActiveController
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
        
        $model   = new LaporanBatalRegV;
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

        return [
            'instalasi' => $queryInstalasi,
        ];
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        
        $instalasi_nama          = '';
        $ruangan_nama            = '';

        $model   = new LaporanBatalRegV;
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
        }

        $query->andWhere(['between', 'tgl_registrasi', $start, $end]);
        

        $title   = Yii::t('app', 'LAPORAN REKAPITULASI PASIEN BATAL REGISTRASI RUMAH SAKIT');
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
                $tmp[7]  = $value['alasan_batal'];
                $tmp[8]  = $value['nama_petugas'] . ' ' . date('d-m-Y H:i:s', strtotime($value['tgl_batal']));

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
                        'label'=>'Alasan Batal',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Petugas',
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

        $model   = new LaporanBatalRegV;
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
        }

        $query->andWhere(['between', 'tgl_registrasi', $start, $end]);
        $data = $query->all();

        $print = new DocoPrint();

        $print->attributes = [
            '#title#' => 'LAPORAN REKAPITULASI PASIEN BATAL REGISTRASI',
            '#periode#' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            '#instalasi_nama#' => $instalasi_nama,
            '#ruangan_nama#' => $ruangan_nama,
            '#data#' => $this->renderPartial('_cetak_pdf', [
                'data' => $data
            ]),
        ];
        $print->Output();
    }
}