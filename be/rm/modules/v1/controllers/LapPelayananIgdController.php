<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LapPelayananIgdFn;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LapPelayananIgdController extends DocoActiveController
{
    public $modelClass = '';
    const JENIS_PELAYANAN = [
        'non_trauma' => 'Non Trauma',
        'trauma' => 'Trauma',
        'kebidanan' => 'Kebidanan',
        'non_bedah' => 'Non Bedah',
        'psikiatri' => 'Psikiatri',
        'anak' => 'Anak',
        'bedah_trauma_kll' => 'Bedah Trauma Kll',
        'bedah_trauma_non_kll' => 'Bedah Trauma Non Kll',
        'bedah_non_trauma' => 'Bedah Non Trauma',
        'total' => 'TOTAL'
    ];
    const TOTAL = 'TOTAL';

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
        $data = $this->generateData();

        return [
            'data' => $data,
        ];
    }

    private function generateData()
    {
        $request = Yii::$app->request;
        $data = [];
        
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
        }

        return LapPelayananIgdFn::getData($start, $end);
    }

    public function actionExportExcel()
    {        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
        }

        $title   = Yii::t('app', 'LAPORAN PELAYANAN RAWAT DARURAT RUMAH SAKIT');
        $row     = $footer = [];
        try {
            $data = $this->generateData();
            $no     = 1;
            foreach ($data as $value) {
                $tmp[1]  = $no;
                $tmp['jenis_pelayanan']  = $value['jenis_pelayanan'];
                $tmp['rujukan']  = $value['pasienmasukrujukan'];
                $tmp['non_rujukan']  = $value['pasienmasuknonrujukan'];
                $tmp['resusitasi']  = $value['triase_resusitasi'];
                $tmp['emergent']  = $value['triase_emergent'];
                $tmp['urgent']  = $value['triase_urgent'];
                $tmp['non_urgent']  = $value['triase_nonurgent'];
                $tmp['false_emergency']  = $value['triase_falseemergency'];
                $tmp['dipulangkan']  = $value['pasientindaklanjut_dipulangkan'];
                $tmp['dirujuk_ke_RS_lain']  = $value['pasientindaklanjut_dirujukrslain'];
                $tmp['pulang_paksa']  = $value['pasientindaklanjut_pulangpaksa'];
                $tmp['meninggal']  = $value['pasientindaklanjut_meninggal'];
                $tmp['dirujuk_rawat_inap']  = $value['pasientindaklanjut_dirujukri'];
                $tmp['lain-lain']  = $value['pasientindaklanjut_lainlain'];
                $tmp['melarikan_diri']  = $value['pasientindaklanjut_melarikandiri'];

                $row[]   = $tmp;
                $no++;
            }
        } catch (Exception $e) {
            $row = [];
        }
        $header = [
            'PERIODE' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
        ];
        $custHeader = [
                [
                    [
                        'label'=>'No',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jenis Pelayanan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Pasien Masuk',
                        'rowspan'=>1,
                        'colspan'=>2,
                    ],
                    [
                        'label'=>'Triase',
                        'rowspan'=>1,
                        'colspan'=>5,
                    ],
                    [
                        'label'=>'Tindakan Lanjut',
                        'rowspan'=>1,
                        'colspan'=>7,
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
    * @attribute #periode# => Periode
    * @attribute #data# => Data
    **/
    public function actionExportPdf() 
    {
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }
        }

        $data = $this->generateData();
        $print = new DocoPrint();

        $print->attributes = [
            '#title#' => 'LAPORAN PELAYANAN RAWAT DARURAT RUMAH SAKIT',
            '#periode#' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            '#data#' => $this->renderPartial('_cetak_pdf', [
                'data' => $data
            ]),
        ];
        $print->Output();
    }
}
