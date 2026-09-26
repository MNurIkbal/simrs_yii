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
use app\modules\v1\models\LaporanRjHeaderView;
use app\modules\v1\models\LaporanRjView;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganView;
use app\modules\v1\models\KelompokLaporanRajalView;
use app\modules\v1\models\MasterPoliklinikView;

class LaporanDetailPendapatanRajalController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanRjView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    public function actionGetDataFilter()
    {
        
        $request = Yii::$app->request;
        $instalasi = $ruangan = [];
        $data = MasterPoliklinikView::find()->all();
        if ($data) {
            foreach ($data as $key => $value) {
                if (!isset($ruangan[$value->bagian_kode])) {
                    $ruangan[$value->bagian_kode] = [
                        'id' => $value->bagian_kode,
                        'label' => $value->bagian_nama
                    ];
                }
            }
        }

        return $response = [
            'ruangan'   => ArrayHelper::map($ruangan, 'id', 'label'),
        ];
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $model = new LaporanRjHeaderView;
        $query = $model::find();
        
        $date = date('Y-m-d');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $date = date('Y-m-d', strtotime($_GET['advanced-filter']['tgl_pulang']));
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
        }
        $query->where(['tgl_pulang' => $date]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetKelompokLaporan()
    {
        try {
            $model = KelompokLaporanRajalView::find()->orderBy(['urutan' => SORT_ASC]);
            return $model->asArray()->all();
        } catch (\Exception $e) {
            return [];
        } catch (\yii\db\Exception $e) {
            return [];
        }
    }

    public function actionGetDetail()
    {
        ini_set('memory_limit', '512M');
        $request = Yii::$app->request;

        $model = new LaporanRjView;
        $query = $model::find();
        $date = date('Y-m-d');
        $arr_norm = $request->get('no_rm');
        
        $advanced_filter = $request->get('advanced_filter');
        if(isset($advanced_filter['advanced-filter'])) {
            if(isset($advanced_filter['advanced-filter']['tgl_pulang'])) {
                $date = date('Y-m-d', strtotime($advanced_filter['advanced-filter']['tgl_pulang']));
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
        }
        $query->where(['tgl_pulang' => $date]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }

    public function actionExportExcel()
    {
        ini_set('memory_limit', '512M');
        // $version = phpversion();
        // return $version;
        $timestart = microtime(true);
        $exheader = 0;
        $exdetail = 0;
        $exproses = 0;

        $result = [];
        $tmp_data = [];
        $header = [];
        $footer = [];
        $dataFoot = [];
        $klaim_inacbg = [];
        $klaim_dibayar = [];

        $request = Yii::$app->request;
        $model = new LaporanRjHeaderView;
        $query = $model::find();

        $date = date('Y-m-d');
        $instalasi = 'Rawat Ranap';

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $date = date('Y-m-d', strtotime($_GET['advanced-filter']['tgl_pulang']));
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
            if(isset($_GET['advanced-filter']['instalasi'])) {
                if ($_GET['advanced-filter']['instalasi']) {
                    $name = $_GET['advanced-filter']['instalasi'];
                    if ($name == 'RINP') {
                        $instalasi = 'Rawat Inap';
                    } else if($name == 'RJAL') {
                        $instalasi = 'Rawat Ranap';
                    } 
                }
            }
        }
        $query->where(['tgl_pulang' => $date]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        
        $arr_no_rm = [];
        $tmp = [];

        if ($data) {
            foreach ($data as $key => $value) {
                if (!in_array($value['no_rekammedik'], $arr_no_rm)) {
                    $arr_no_rm[] = $value['no_rekammedik'];
                }
                $tgl_pendaftaran = $value['tgl_pendaftaran'];
                $no_rekammedik = $value['no_rekammedik'];
                if (!isset($tmp[$tgl_pendaftaran][$no_rekammedik])) {
                    $tmp[$tgl_pendaftaran][$no_rekammedik] = [
                        'total'            => 0, 
                        'arr_poli'         => [],
                        'arr_dokter'       => [],
                        'total_maks'       => 0,
                        'list'             => []
                    ];
                }
            }
            $headerend = microtime(true);
            $exheader = number_format($headerend - $timestart, 2);
            $detail_start = microtime(true);
            
            if ($tmp) {
                $model = new LaporanRjView;
                $query = $model::find();

                $query->where(['tgl_pulang' => $date]);
                $query = DocoRestActiveFilter::advancedFilter($model, $query);
                $detail = $query->all();

                $defaultColumn = $this->generateColumn();
                $item_total = $defaultColumn;

                $detailend = microtime(true);
                $exdetail = number_format($detailend - $detail_start, 2);

                $proses_start = microtime(true);
                if ($detail) {
                    $i = 1;
                    foreach ($detail as $key => $value) {
                        $insert = false;
                        $column = $defaultColumn;
                        $tgl_pendaftaran = $value['tgl_pendaftaran'];
                        $no_rekammedik = $value['no_rekammedik'];

                        if (isset($tmp[$tgl_pendaftaran][$no_rekammedik])) {
                            if ($value['is_poliklinik'] && !in_array($value['poliklinik'], $tmp[$tgl_pendaftaran][$no_rekammedik]['arr_poli'])) {
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['arr_poli'][] = $value['poliklinik'];

                                $count_poli = count($tmp[$tgl_pendaftaran][$no_rekammedik]['arr_poli']);
                                $count_dokter = count($tmp[$tgl_pendaftaran][$no_rekammedik]['arr_dokter']);

                                if (($count_poli > $count_dokter)) {
                                    $insert = true;
                                }
                            }
                            if ($value['dokter_kode'] && !in_array($value['dokter_kode'], $tmp[$tgl_pendaftaran][$no_rekammedik]['arr_dokter'])) {
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['arr_dokter'][] = $value['dokter_kode'];
                                $count_poli = count($tmp[$tgl_pendaftaran][$no_rekammedik]['arr_poli']);
                                $count_dokter = count($tmp[$tgl_pendaftaran][$no_rekammedik]['arr_dokter']);

                                if (($count_dokter > $count_poli)) {
                                    $insert = true;
                                }
                            }
                            if ($insert) {
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][] = $column;
                            }
                        }
                    }

                    $i = 1;
                    $column = $defaultColumn;
                    foreach ($detail as $key => $value) {
                        $total = 0;
                        $name =  $this->clean(strtolower($value['kelompok_nama']));
                        $tgl_pendaftaran = $value['tgl_pendaftaran'];
                        $no_rekammedik = $value['no_rekammedik']; 
                        
                        if (isset($tmp[$tgl_pendaftaran][$no_rekammedik])) {
                            if ($tmp[$tgl_pendaftaran][$no_rekammedik]['list']) {

                                if (isset($column[$name])) {
                                    $total += $value['total_layanan'];
                                    $item_total[$name] += $value['total_layanan'];
                                }

                                if ($value['jasa_dokter']) {
                                    $total += $value['jasa_dokter'];
                                    $item_total['hutang_hondok_jumlah'] += $value['jasa_dokter'];
                                }

                                if ($value['is_poliklinik']) {
                                     $item_total['poli_jumlah'] += $value['total_layanan'];
                                     $total += $value['total_layanan'];
                                }

                                if ($value['klaim_inacbg']) { 
                                    if (!in_array($value['no_pendaftaran'], $klaim_inacbg)) {
                                        $item_total['klaim_inacbg'] = $value['klaim_inacbg'];
                                        // $total += $value['klaim_inacbg'];   
                                        $klaim_inacbg[] = $value['no_pendaftaran'];
                                    }
                                }

                                if ($value['klaim_dibayar']) { 
                                    if (!in_array($value['no_pendaftaran'], $klaim_dibayar)) {
                                        $item_total['klaim_dibayar'] = $value['klaim_dibayar'];
                                        // $total += $value['klaim_dibayar'];
                                        $klaim_dibayar[] = $value['no_pendaftaran'];
                                    }
                                }
                                $item_total['jumlah_perawatan'] += $total;

                                $string_diagnosa_utama = '';
                                $string_diagnosa_penyerta = '';

                                if ($value['diagnosa_utama']) {
                                    $diagnosa_utama = $value['diagnosa_utama'];
                                    foreach ($diagnosa_utama as $_key => $_value) {
                                        $string_diagnosa_utama .= $_value['diagnosa_kode'] . " " . $_value['diagnosa_nama'];
                                        $next = $_key + 1;
                                        if (isset($diagnosa_utama[$next]) && $diagnosa_utama[$next]) {
                                            $string_diagnosa_utama .= "\n";
                                        }
                                    }
                                }

                                if ($value['diagnosa_penyerta']) {
                                    $diagnosa_penyerta = $value['diagnosa_penyerta'];
                                    foreach ($diagnosa_penyerta as $_key => $_value) {
                                        $string_diagnosa_penyerta .= $_value['diagnosa_kode'] . " " . $_value['diagnosa_nama'];
                                        $next = $_key + 1;
                                        if (isset($diagnosa_penyerta[$next]) && $diagnosa_penyerta[$next]) {
                                            $string_diagnosa_penyerta .= "\n";
                                        }
                                    }
                                }

                                $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['tgl_pendaftaran'] =  date('d-m-Y', strtotime($value['tgl_pendaftaran']));
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['tgl_pulang'] =  date('d-m-Y', strtotime($value['tgl_pulang']));
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['nama_pasien'] =  $value['nama_pasien'];
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['diagnosa_utama'] =  $string_diagnosa_utama ? $string_diagnosa_utama : '-';
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['diagnosa_penyerta'] .= $string_diagnosa_penyerta;
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['no_sep'] =  $value['no_sep'] ? $value['no_sep'] : '-';
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['no_rekammedik'] =  (string)$value['no_rekammedik'];
                                $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['jumlah_perawatan'] +=  $total;

                                if ($value['klaim_inacbg']) { 
                                    $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['klaim_inacbg'] =  $value['klaim_inacbg'];
                                }
                                if ($value['klaim_dibayar']) { 
                                    $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0]['klaim_dibayar'] =  $value['klaim_dibayar'];
                                }
                                if (isset($column[$name])) {
                                    $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][0][$name] += $value['total_layanan'];
                                }

                                $index = array_search($value['poliklinik'], $tmp[$tgl_pendaftaran][$no_rekammedik]['arr_poli']);
                                if ($index !== false) {
                                    $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][$index]['poli_nama'] = $value['poliklinik'] ? $value['poliklinik'] : '-';
                                    if ($value['is_poliklinik']) {
                                        $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][$index]['poli_jumlah'] += $value['total_layanan'] ? $value['total_layanan'] : 0;
                                    }
                                }

                                $index = array_search($value['dokter_kode'], $tmp[$tgl_pendaftaran][$no_rekammedik]['arr_dokter']);
                                if ($index !== false) {
                                    $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][$index]['hutang_hondok_dokter'] = $value['dokter_nama'] ? $value['dokter_nama'] : '-';
                                    $tmp[$tgl_pendaftaran][$no_rekammedik]['list'][$index]['hutang_hondok_jumlah'] += $value['jasa_dokter'] ? $value['jasa_dokter'] : 0;
                                }
                            }

                            $i++;
                        }
                    }

                    $notallow = ['no_rekammedik', 'no_sep', 'rowspan', 'no_rekammedik', 'bagian_kode'];
                    $no = 1;
                    if ($tmp) {
                        foreach ($tmp as $tgl => $no_rekammedik) {
                            foreach ($no_rekammedik as $key => $value) {
                                $count_poli  = count($value['arr_poli']);
                                $count_dokter = count($value['arr_dokter']);
                                $max_row = 0;

                                if ($max_row < $count_poli) {
                                    $max_row = $count_poli;
                                } 
                                if ($max_row < $count_dokter) {
                                    $max_row = $count_dokter;
                                }
                                
                                $mod_poli = 0;
                                $merge_poli = $max_row;
                                $mod_dokter = 0;
                                $merge_dokter = $max_row;

                                if ($count_poli > 1) {
                                    $mod_poli = $max_row % $count_poli;
                                    $merge_poli = floor($max_row / $count_poli);
                                }

                                if ($count_dokter > 1) {
                                    $mod_dokter = $max_row % $count_dokter;
                                    $merge_dokter = floor($max_row / $count_dokter);
                                }

                                $i = 1;
                                $column_poli = $mod_poli + $merge_poli - 1;
                                $column_dokter = $mod_dokter + $merge_dokter - 1;

                                foreach ($value['list'] as $idx => $val) {
                                    foreach ($val as $colname => $val1) {
                                        if (!in_array($colname, $notallow)) {
                                            if (is_numeric($val1)) {
                                                $val[$colname] = $val1;
                                            }
                                        }
                                    }

                                    $val['rowspan'] = $max_row;
                                    if ($i == 1) {
                                        $val['no'] = $no;
                                    } else {
                                        $val['no'] = '';
                                    }
                                    
                                    if ($count_poli < 2) {
                                        if ($i == 1) {
                                            $val['colmerge_10'] = $max_row;
                                            $val['colmerge_11'] = $max_row;
                                        } else {
                                            $val['colmerge_10'] = 0;
                                            $val['colmerge_11'] = 0;
                                        }
                                        
                                    } else if ($count_poli > 1) { 
                                        if ($count_poli == $max_row) {
                                            if ($i < $count_poli) {
                                                $val['colmerge_10'] = 1;
                                                $val['colmerge_11'] = 1;
                                            } elseif ($i == $count_poli) {
                                                $merge = $max_row - $i;
                                                if ($merge > 0) {
                                                    $val['colmerge_10'] = $merge + 1;
                                                    $val['colmerge_11'] = $merge + 1;
                                                } else {
                                                    $val['colmerge_10'] = 1;
                                                    $val['colmerge_11'] = 1;
                                                }
                                                
                                            } else {
                                                $val['colmerge_10'] = 0;
                                                $val['colmerge_11'] = 0;
                                            }   
                                        } else {
                                            if ($i < $count_poli) {
                                                $val['colmerge_10'] = 1;
                                                $val['colmerge_11'] = 1;
                                            } else if($i == $count_poli){
                                                $merge = $max_row - $i;
                                                $val['colmerge_10'] = $merge + 1; 
                                                $val['colmerge_11'] = $merge + 1; 
                                            }else {
                                                $val['colmerge_10'] = 0;
                                                $val['colmerge_11'] = 0;
                                            }
                                        }
                                    }

                                    if ($count_dokter < 2) {
                                        if ($i == 1) {
                                            $val['colmerge_12'] = $max_row;
                                            $val['colmerge_13'] = $max_row;
                                        } else {
                                            $val['colmerge_12'] = 0;
                                            $val['colmerge_13'] = 0;
                                        }
                                        
                                    } else if ($count_dokter > 1) { 
                                        if ($count_dokter == $max_row) {
                                            if ($i < $count_dokter) {
                                                $val['colmerge_12'] = 1;
                                                $val['colmerge_13'] = 1;
                                            } elseif ($i == $count_dokter) {
                                                $merge = $max_row - $i;
                                                if ($merge > 0) {
                                                    $val['colmerge_12'] = $merge + 1;
                                                    $val['colmerge_13'] = $merge + 1;
                                                } else {
                                                    $val['colmerge_12'] = 1;
                                                    $val['colmerge_13'] = 1;
                                                }
                                            } else {
                                                $val['colmerge_12'] = 0;
                                                $val['colmerge_13'] = 0;
                                            }   
                                        } else {
                                            if ($i < $count_dokter) {
                                                $val['colmerge_12'] = 1;
                                                $val['colmerge_13'] = 1;
                                            } else if($i == $count_dokter){
                                                $merge = $max_row - $i;
                                                $val['colmerge_12'] = $merge + 1;
                                                $val['colmerge_13'] = $merge + 1;
                                            }else {
                                                $val['colmerge_12'] = 0;
                                                $val['colmerge_13'] = 0;
                                            }
                                        }
                                    }

                                    $result[] = $val;
                                    $i++;
                                }
                                $no++;
                            }
                        }
                    }
                }
                unset($tmp);
                if ($result) {
                    $item_total['no'] = 'TOTAL';
                    $item_total['colspan'] = 5;
                    $result[] = $item_total;
                }
            }
        }
        $prosesend = microtime(true);
        $exproses = number_format($prosesend - $proses_start, 2);

        $generate = $this->generateTableHeader();   
        $custHeader = $generate['static_header'];
        $column = $generate['column'];
        $header = [
            'Instalasi' => 'Rawat Rajal',
            'Periode' => $date,
        ];

        $col_merge = [0, 1, 2, 3, 4 , 5, 6, 7, 8, 9, 10];

        for ($i=15; $i <= $column ; $i++) { 
            $col_merge[] = $i;
        }
        return [
            'header-time'   => $exheader,
            'detail-time'   => $exdetail,
            'proses-time'   => $exproses,
            'title'         => 'Laporan Detail Pendapatan Ranap',
            'periode'       => $date,
            'header_sheet'  => $header, 
            'table_header'  => $custHeader, 
            'table_data'    =>$result,
            'col_merge'     => $col_merge
        ];
        // $filePath = DocoHelpers::exportExcel('RINCIAN DETAIL PENDAPATAN RAWAT RAJAL', $result, $header,  array(
        //         "skipIncrement" => true,
        //         "skipHeader"    => true,
        //         "sorting"       => false,
        //         'customHeader' => $custHeader,
        //     ),$footer,[], true, $mergeBody);
        // $filePath->save('php://output');
        // die;
    }

    public function generateColumn()
    {
        $data_column = [
            'no' => '',
            'tgl_pendaftaran' => '-',
            'tgl_pulang'=> '-',
            'no_rekammedik'=> '-',
            'nama_pasien'    => '-',
            'diagnosa_utama'    => '-',
            'diagnosa_penyerta' => '-',
            'no_sep' => '-',
            'klaim_inacbg' => 0,
            'klaim_dibayar' => 0,
            'poli_nama'    => '-',
            'poli_jumlah' => 0,
            'hutang_hondok_dokter'=> '-',
            'hutang_hondok_jumlah'=> 0,
            'rowspan' => 1,
        ];

        $model = KelompokLaporanRajalView::find()->orderBy(['urutan' => SORT_ASC]);
        $data =  $model->all();
        if ($data) {
            foreach ($data as $key => $value) {
                if (isset($value->kelompok_detail) && $value->kelompok_detail) {
                    foreach ($value->kelompok_detail as $key1 => $value1) {
                        $name = $this->clean(strtolower($value1['kelompoktindakan_nama']));
                        if (!isset($data_column[$name])) {
                            $data_column[$name] = 0;
                        }
                    }
                }
            }
        }
        $data_column['jumlah_perawatan'] = 0;
        return $data_column;
    }

    public function clean($string) {
       $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.

       return preg_replace('/[^A-Za-z0-9]/', '', $string); // Removes special chars.
    }

    public function generateTableHeader()
    {
        $static_header = [
            0 => [
                    ['label'=>'NO', 'rowspan'=>2],
                    ['label'=>'TANGGAL MASUK', 'rowspan'=>2],
                    ['label'=>'TANGGAL PULANG', 'rowspan'=>2],
                    ['label'=>'NO REKAM MEDIK', 'rowspan'=>2],
                    ['label'=>'NAMA PASIEN', 'rowspan'=>2],
                    ['label'=>'DIAGNOSA', 'colspan'=>2],
                    ['label'=>'NO SEP', 'rowspan'=>2],
                    ['label'=>'KLAIM INACBG (Rp.)', 'rowspan'=>2],
                    ['label'=>'KLAIM DIBAYAR (Rp.)', 'rowspan'=>2],
                    ['label'=>'POLIKLINIK', 'colspan'=>2],
                    ['label'=>'HUTANG HONOR DOKTER', 'colspan'=>2],
                ],
            1 => [
                    ['label'=>'UTAMA','startfrom'=>6],
                    ['label'=>'PENYERTA'],
                    ['label'=>'NAMA POLI', 'startfrom'=>4],
                    ['label'=>'JUMLAH (Rp.)'],
                    ['label'=>'NAMA DOKTER'],
                    ['label'=>'JUMLAH (Rp.)']
                ]
        ];
        $model = KelompokLaporanRajalView::find()->orderBy(['urutan' => SORT_ASC]);
        $data =  $model->all();
        $column = 15;
        if ($data) {
            $startfrom = 1;
            $reset = false;
            foreach ($data as $key => $value) {
                $item = [
                    'label' => $value->kelompok_header ? $value->kelompok_header : '-'
                ];
                if (isset($value->kelompok_detail) && $value->kelompok_detail) {
                    $startfrom++;
                    $item['colspan'] = count($value->kelompok_detail);
                    foreach ($value->kelompok_detail as $key1 => $value1) {
                        $subItem = ['label' => $value1['kelompoktindakan_nama'] . ' (Rp.)'];
                        if (!$reset) {
                            // $subItem['startfrom'] = $startfrom;
                            // $reset = true;
                        }
                        $static_header[1][] = $subItem;
                    }
                } else {
                    $reset = false;
                    $startfrom = 1;
                    $item['rowspan'] = 2;
                }
                $column++;
                $static_header[0][] = $item;
            }
        }
        $static_header[0][] = ['label'=>'TOTAL (Rp.)', 'rowspan'=>2];
        $column += 2;
        $res = [
            'static_header' => $static_header,
            'column'    => $column
        ];
        return $res;
    }
}