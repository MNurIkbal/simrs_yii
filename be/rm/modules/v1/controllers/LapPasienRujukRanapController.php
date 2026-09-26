<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPasienRujukRiDetV;
use app\modules\v1\models\NewLaporanPasienRujukKeRawatInapFn;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Lookup;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LapPasienRujukRanapController extends DocoActiveController
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
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglpasienpulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpasienpulang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpasienpulang']);
            }
        }

        $data = $this->getData($start, $end);

        return [
            'data' => $data,
        ];
    }

    public function actionGetDataDetail()
    {
        $request = Yii::$app->request;
        $instalasi_id = $request->get('instalasi_id');
        $tgl_pulang = $request->get('tgl_pulang');
        $explode = explode(' - ', $tgl_pulang);
                
        if (count($explode) == 2) {
            $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
            $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
        } else {
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
        }
        unset($_GET['advanced-filter']['tglpasienpulang']); 
        
        $model = new LaporanPasienRujukRiDetV;
        $query = $model::find()->andWhere(['instalasi_id' => $instalasi_id]);
        $query->andWhere(['between', 'tglpasienpulang', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglpasienpulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpasienpulang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpasienpulang']);
            }
        }

        $title   = Yii::t('app', 'LAPORAN PASIEN RUJUK RAWAT INAP RUMAH SAKIT');
        $row     = $footer = [];
        try {
            $data = $this->getData($start, $end);
            $tmp[1]  = $data['pasienrjkeri'];
            $tmp[2]  = $data['pasienrdkeri'];
            $tmp[3]  = $data['jumlah'];

            $row[0]   = $tmp;
        } catch (Exception $e) {
            $row = [];
        }
        $header = [
            'PERIODE' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
        ];
        $custHeader = [
                [
                    [
                        'label'=>'Rawat Jalan',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Rawat Darurat',
                        'rowspan'=>2,
                    ],
                    [
                        'label'=>'Jumlah',
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
    * @attribute #periode# => Periode
    * @attribute #data# => Data
    **/
    public function actionExportPdf() 
    {
        $request = Yii::$app->request;
        
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglpasienpulang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpasienpulang']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpasienpulang']);
            }
        }

        $data = $this->getData($start, $end);
        $print = new DocoPrint();
        $print->attributes = [
            '#title#' => 'LAPORAN PASIEN RUJUK RAWAT INAP',
            '#periode#' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
            '#data#' => $this->renderPartial('_cetak_pdf', [
                'data' => $data
            ]),
        ];
        $print->Output();
    }

    private function getData($starDate = null, $endDate = null)
    {
        $start = $starDate == null ? date('Y-m-d 00:00:00') : $starDate;
        $end = $endDate == null ? date('Y-m-d 23:59:59') : $endDate;

        return NewLaporanPasienRujukKeRawatInapFn::getData($start, $end)->asArray()->one();
    }
}