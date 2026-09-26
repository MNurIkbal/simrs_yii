<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\LaporanPendapatanRanapHeaderView;
use app\modules\v1\models\LaporanPendapatanRanapView;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\BagianMasterView;


class LaporanPendapatanRanapController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPendapatanRanapView';

    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        return $actions;
    }

    public function actionGetDataFilter()
    {
        $bulan = DocoConstants::$bulan;
        $tahun = DocoConstants::$tahun;
        $pendapatan = BagianMasterView::find()
            ->where(['jenis' => 'nota'])->orderBy(['bagian_nama' => SORT_ASC])->all();
        return [
            'pendapatan' => $pendapatan,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        $model = new LaporanPendapatanRanapHeaderView;
        $query = $model::find();
        $tgl_pendaftaran = date('Y-m');

        if (isset($_GET['advanced-filter'])) {
            $filter = $_GET['advanced-filter'];
            if (isset($filter['bulan']) && $filter['bulan'] && isset($filter['tahun']) && $filter['tahun']) {
                $bulan = $filter['bulan'];
                $tahun = $filter['tahun'];
                $tgl_pendaftaran = $tahun .'-' . $bulan;

                $tgl_pendaftaran = date('Y-m', strtotime($tgl_pendaftaran));
                unset($_GET['advanced-filter']['bulan']);
                unset($_GET['advanced-filter']['tahun']);
            }
        }
        $query->where(['tgl_pendaftaran' => $tgl_pendaftaran]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetDetail()
    {
        $request = Yii::$app->request;
        $model = new LaporanPendapatanRanapView;
        $query = $model::find();
        $date = date('Y-m-d');
        $last = date('t', strtotime($date));
        $start = date('Y-m-1').' 00:00:00';
        $end = date('Y-m-'.$last).' 23:59:59';

        if (isset($_GET['advanced-filter'])) {
            $filter = $_GET['advanced-filter'];
            if (isset($filter['bulan']) && $filter['bulan'] && isset($filter['tahun']) && $filter['tahun']) {
                    $bulan = $filter['bulan'];
                    $tahun = $filter['tahun'];
                    $start = $tahun .'-' . $bulan . '-1 00:00:00';
                    $total_date = date('t', strtotime($start));
                    $end = $tahun .'-' . $bulan . '-'. $total_date .' 00:00:00';

                    unset($_GET['advanced-filter']['bulan']);
                    unset($_GET['advanced-filter']['tahun']);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->asArray()->all();
        
        return $data;
    }

    public function actionGetTotal()
    {
        $request = Yii::$app->request;
        $model = new LaporanPendapatanRanapView;
        $query = $model::find();
        $date = date('Y-m-d');
        $last = date('t', strtotime($date));
        $start = date('Y-m-1').' 00:00:00';
        $end = date('Y-m-'.$last).' 23:59:59';

        if (isset($_GET['advanced-filter'])) {
            $filter = $_GET['advanced-filter'];
            if (isset($filter['bulan']) && $filter['bulan'] && isset($filter['tahun']) && $filter['tahun']) {
                    $bulan = $filter['bulan'];
                    $tahun = $filter['tahun'];
                    $start = $tahun .'-' . $bulan . '-1 00:00:00';
                    $total_date = date('t', strtotime($start));
                    $end = $tahun .'-' . $bulan . '-'. $total_date .' 00:00:00';

                    unset($_GET['advanced-filter']['bulan']);
                    unset($_GET['advanced-filter']['tahun']);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new LaporanPendapatanRanapView;
        $query = $model::find();

        $date = date('Y-m-d');
        $last = date('t', strtotime($date));
        $start = date('Y-m-1').' 00:00:00';
        $end = date('Y-m-'.$last).' 23:59:59';
        $bulan = date('m');
        $tahun = date('Y');

       if (isset($_GET['advanced-filter'])) {
            $filter = $_GET['advanced-filter'];
            if (isset($filter['bulan']) && $filter['bulan'] && isset($filter['tahun']) && $filter['tahun']) {
                    $bulan = $filter['bulan'];
                    $tahun = $filter['tahun'];
                    $start = $tahun .'-' . $bulan . '-1 00:00:00';
                    $total_date = date('t', strtotime($start));
                    $end = $tahun .'-' . $bulan . '-'. $total_date .' 00:00:00';

                    unset($_GET['advanced-filter']['bulan']);
                    unset($_GET['advanced-filter']['tahun']);
            }
        }

        $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();

        $result = $header = $data_header = $arr_tgl = [];
        $tmp = [];
        $tmp['Jumlah (Rp.)'] = 0;
        $total_pasien = 0;
        $total = 0;

        $month = (int) date('m', strtotime($start));
        $year = date('Y', strtotime($start));

        $header = ['No', 'Pendapatan', 'Kode'];
        for ($i=1; $i <= $total_date ; $i++) { 
            $monthName = DocoConstants::$bulanSingkatan[$month];
            $tgl = $i;
            $keyTgl = $tgl .'-'. $monthName . '-' . $year .' (Rp.)';
            $header[] = $keyTgl;
        }
        $header[] = 'Jumlah (Rp.)';

        if ($data) {
            $date = $data[0]['tgl_pendaftaran'];
            $total_date = (int) date('t', strtotime($date));
            $month = date('M', strtotime($date));
            $year = date('Y', strtotime($date));
            $no = 1;
            foreach ($data as $key => $value) {
                if (!in_array($value['tgl_pendaftaran'], $arr_tgl)) {
                    $arr_tgl[] = $value['tgl_pendaftaran'];
                    $total_pasien += $value['total_pasien']; 
                }
                
                if(!isset($data_header[$value['kode']])) {
                    $data_header[$value['kode']] = [
                        'no'         => $no,
                        'pendapatan' => $value['pendapatan'],
                        'kode'       => $value['kode'],  
                    ];

                    for ($i=1; $i <= $total_date ; $i++) { 
                        $tgl = $i;
                        $keyTgl = $tgl .'-'. $month . '-' . $year .' (Rp.)';
                        $data_header[$value['kode']][$keyTgl] = 0;

                        if (!isset($tmp[$keyTgl])) {
                            $tmp[$keyTgl] = 0;
                        }
                    }
                    $no++;
                }
                $tgldb = (int) date('d', strtotime($value['tgl_pendaftaran']));
                $keyTgl = $tgldb .'-'. $month . '-' . $year .' (Rp.)';
                $data_header[$value['kode']][$keyTgl] += $value['total_layanan'];

                if (!isset($data_header[$value['kode']]['Jumlah (Rp.)'])) {
                    $data_header[$value['kode']]['Jumlah (Rp.)'] = 0 ;
                }
                $data_header[$value['kode']]['Jumlah (Rp.)'] += $value['total_layanan'];
                $tmp[$keyTgl] += (int) $value['total_layanan'];
                $tmp['Jumlah (Rp.)'] += $value['total_layanan'];
                
            }
        }
        
        if ($data_header) {
            foreach ($data_header as $key => $value) {
                $result[] = $value;
            }
        }
        $dataFoot = ['no'=>'', 'pendapatan'=>'TOTAL','kode'=> ''];
        $i = 1;
        foreach ($result[0] as $key => $value) {

            if ($i > 2) {
                if (isset($tmp[$key])) {
                    $dataFoot[$key] = (string) $tmp[$key];    
                }
            } 
            $i++;
        }
        if ($dataFoot) {
            $result[] = $dataFoot;
        }
        return [
            'title'         => 'Laporan Pendapatan Ranap',
            'periode'       => 'Tanggal : ' . date('d-m-Y', strtotime($start)) . ' Sampai ' . date('d-m-Y', strtotime($end)),
            'table_header'  => $header, 
            'table_data'    => $result,
        ];
        // return $dataFoot
        // $footer = [
        //     'title'=> ['Total', 2],
        //     'data'=> $dataFoot
        // ];
        // $header['periode'] = date('d-m-Y', strtotime($start)) .' Sampai dengan '. date('d-m-Y', strtotime($end));
        // $header['Total Pasien'] = $total_pasien;
        // $filePath = DocoHelpers::exportExcel('Laporan Pendapatan Ranap', $result, $header, [], $footer, [], true);
        // $filePath->save('php://output');
        // die;
    }
}