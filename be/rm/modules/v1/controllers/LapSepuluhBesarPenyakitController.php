<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Laporan10BesarPenyakitView;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

class LapSepuluhBesarPenyakitController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Laporan10BesarPenyakitView';

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
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new Laporan10BesarPenyakitView;
        $query = $model::find(true)
            ->select([
                'diagnosa_id', 
                'tglmorbiditas', 
                'tglmorbiditas', 
                'diagnosa_kode', 
                'diagnosa_nama', 
                'instalasi_id', 
                'instalasi_nama', 
                'ruangan_id', 
                'ruangan_nama', 
                'count(diagnosa_id) as jumlah'
            ])
            ->groupBy([
                'diagnosa_id', 
                'tglmorbiditas', 
                'diagnosa_kode', 
                'diagnosa_nama', 
                'instalasi_id', 
                'instalasi_nama', 
                'ruangan_id', 
                'ruangan_nama'
            ])->asArray();

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmorbiditas'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmorbiditas']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmorbiditas']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tglmorbiditas', $start, $end]);
        // }
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    protected $_title = 'Laporan Sepuluh Besar Penyakit';
    public function actionExportExcel()
    {
        $model = new Laporan10BesarPenyakitView;
        $query = $model::find(true)->select([
            'diagnosa_id', 
            'tglmorbiditas', 
            'tglmorbiditas', 
            'diagnosa_kode', 
            'diagnosa_nama', 
            'instalasi_id', 
            'instalasi_nama', 
            'ruangan_id', 
            'ruangan_nama', 
            'count(diagnosa_id) as jumlah'
        ])
        ->groupBy([
            'diagnosa_id', 
            'tglmorbiditas', 
            'diagnosa_kode', 
            'diagnosa_nama', 
            'instalasi_id', 
            'instalasi_nama', 
            'ruangan_id', 
            'ruangan_nama'
        ]);

        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmorbiditas'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmorbiditas']);

                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmorbiditas']);
            }
        }

        $query->andWhere(['between', 'tglmorbiditas', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        $result = [];

        $startPeriode = DocoHelpers::convDateTime($start, false, false);
        $endPeriode = DocoHelpers::convDateTime($end, false, false);

        if ($startPeriode == $endPeriode) {
            $periode = $startPeriode;
        } else {
            $periode = $startPeriode.' - '.$endPeriode;
        }
        
        foreach ($data as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal pemeriksaan')] = DocoHelpers::convDateTime(date("Y-m-d H:i:s", strtotime($value['tglmorbiditas'])), false, false);
            $newValue[\Yii::t('app', 'Kode diagnosa')] = $value['diagnosa_kode'];
            $newValue[\Yii::t('app', 'Nama diagnosa')] = $value['diagnosa_nama'];
            $newValue[\Yii::t('app', 'Jumlah')] = $value['jumlah'];
            $result[$key] = $newValue;
        }
            
        $header = array(
            Yii::t('app', "Tanggal pemeriksaan") => $periode,
            Yii::t('app', "Nama diagnosa") => (@$_GET['advanced-filter']['diagnosa_nama']),
            Yii::t('app', "Instalasi") => (@$_GET['advanced-filter']['instalasi_nama']),
            Yii::t('app', "Ruangan") => (@$_GET['advanced-filter']['ruangan_nama']),
        );
        $footer = array();

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #periode# => Periode laporan
    * @attribute #table# => Tabel laporan
    **/
    public function actionExportPdf()
    {
        $model = new Laporan10BesarPenyakitView;
        $query = $model::find(true)->select([
            'diagnosa_id', 
            'tglmorbiditas', 
            'tglmorbiditas', 
            'diagnosa_kode', 
            'diagnosa_nama', 
            'instalasi_id', 
            'instalasi_nama', 
            'ruangan_id', 
            'ruangan_nama', 
            'count(diagnosa_id) as jumlah'
        ])
        ->groupBy([
            'diagnosa_id', 
            'tglmorbiditas', 
            'diagnosa_kode', 
            'diagnosa_nama', 
            'instalasi_id', 
            'instalasi_nama', 
            'ruangan_id', 
            'ruangan_nama'
        ]);

        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglmorbiditas'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmorbiditas']);

                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmorbiditas']);
            }
        }

        $query->andWhere(['between', 'tglmorbiditas', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();

        $startPeriode = DocoHelpers::convDateTime($start, false, false);
        $endPeriode = DocoHelpers::convDateTime($end, false, false);

        if ($startPeriode == $endPeriode) {
            $periode = $startPeriode;
        } else {
            $periode = $startPeriode.' - '.$endPeriode;
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#periode#' => $periode,
            '#table#' => $this->renderPartial('pdf', [
                'data' => $data,
            ]),
        ];
        $print->Output();
    }
}