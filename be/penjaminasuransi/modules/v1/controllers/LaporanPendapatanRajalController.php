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
use app\modules\v1\models\LaporanPendapatanRajalHeaderView;
use app\modules\v1\models\LaporanPendapatanRajalView;
use app\modules\v1\models\SyKunjungan;
use app\modules\v1\models\BagianMasterView;


class LaporanPendapatanRajalController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanPendapatanRajalView';

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
            ->where(['jenis' => 'bagian'])->orderBy(['bagian_nama' => SORT_ASC])->all();
        return [
            'pendapatan' => $pendapatan,
            'bulan' => $bulan,
            'tahun' => $tahun
        ];
    }

    public function actionGetData()
    {
        $request = Yii::$app->request;
        $model = new LaporanPendapatanRajalHeaderView;
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
        $model = new LaporanPendapatanRajalView;
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
        $model = new LaporanPendapatanRajalView;
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
        $model = new LaporanPendapatanRajalView;
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
        // $query->orderBy(['tgl_pendaftaran' => SORT_ASC]);
        $data = $query->all();

        $result = $header = $data_header = $arr_tgl = [];
        $tmp = [];
        $tmp['jumlah'] = 0;
        $hondok = [];
        $hondok['jumlah'] = 0;
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

        $no = 1;
        if ($data) {
            $date = $data[0]['tgl_pendaftaran'];
            $total_date = (int) date('t', strtotime($date));
            $month = date('M', strtotime($date));
            $year = date('Y', strtotime($date));
            
            foreach ($data as $key => $value) {
                $jasa_dokter = 0;
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
                        $keyTgl = $tgl .'-'. $month . '-' . $year . ' (Rp.)';
                        $data_header[$value['kode']][$keyTgl] = 0;

                        if (!isset($tmp[$keyTgl])) {
                            $tmp[$keyTgl] = 0;
                            $hondok[$keyTgl] = 0;
                        }
                    }
                    $no++;
                }

                if ($value['jasa_dokter']) {
                    $jasa_dokter = $value['jasa_dokter'];
                }
                $tgldb = (int) date('d', strtotime($value['tgl_pendaftaran']));
                $keyTgl = $tgldb .'-'. $month . '-' . $year .' (Rp.)';
                $data_header[$value['kode']][$keyTgl] += $value['total_layanan'];

                if (!isset($data_header[$value['kode']]['jumlah'])) {
                    $data_header[$value['kode']]['jumlah'] = 0 ;
                }

                $data_header[$value['kode']]['jumlah'] += $value['total_layanan'];
                $tmp['jumlah']  += $value['total_layanan'] + $jasa_dokter;
                $tmp[$keyTgl]  += $value['total_layanan'] + $jasa_dokter;
                $hondok[$keyTgl] += $jasa_dokter;
                $hondok['jumlah'] +=  $jasa_dokter;
            }
        }
        if ($data_header) {
            foreach ($data_header as $key => $value) {
                $result[] = $value;
            }
        }
        $dataHondok = ['no'=> $no, 'pendapatan'=>'Honor Dokter', 'kode'=>''];
        $dataFoot = ['no'=>'', 'pendapatan'=>'TOTAL','kode'=> ''];
        $i = 1;
        foreach ($result[0] as $key => $value) {
            if ($i > 2) {
                if (isset($tmp[$key])) {
                    $dataFoot[$key] = (string) $tmp[$key];    
                    $dataHondok[$key] = (string) $hondok[$key];
                }
            } 
            $i++;
        }
        if ($result) {
            $result[] = $dataHondok;

            if ($dataFoot) {
                $result[] = $dataFoot;
            }
        }

        
        return [
            'title'         => 'Laporan Pendapatan Rajal',
            'periode'       => 'Tanggal : ' . date('d-m-Y', strtotime($start)) . ' Sampai ' . date('d-m-Y', strtotime($end)),
            'table_header'  => $header, 
            'table_data'    => $result,
        ];
        // $footer = [
        //     'title'=> [['Total', 2]],
        //     'data'=> [$dataFoot]
        // ];
        // $header['periode'] = date('d-m-Y', strtotime($start)) .' Sampai dengan '. date('d-m-Y', strtotime($end));
        // $header['Total Pasien'] = $total_pasien;
        // $filePath = DocoHelpers::exportExcel('Laporan Pendapatan Rajal', $result, $header, [], $footer, [], true);
        // $filePath->save('php://output');
        // die;
    }
}