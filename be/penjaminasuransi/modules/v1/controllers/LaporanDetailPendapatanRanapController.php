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
use app\modules\v1\models\LaporanPerbandinganKlaimView;
use app\modules\v1\models\LaporanRiHeaderView;
use app\modules\v1\models\LaporanRiView;
use app\modules\v1\models\SyKunjunganPasien;
use app\modules\v1\models\SyKunjunganView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KelompokLaporanRanapView;

class LaporanDetailPendapatanRanapController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanRiView';

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
        $ruangan = [];
        $dataRuangan = Ruangan::find()->where(['instalasi_id' => 2])->all();
        if ($dataRuangan) {
            foreach ($dataRuangan as $key => $value) {
                if (!isset($ruangan[$value->ruangan_namalainnya])) {
                    $ruangan[$value->ruangan_namalainnya] = [
                        'id' => $value->ruangan_namalainnya,
                        'label' => $value->ruangan_nama
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
        $model = new LaporanRiHeaderView;
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
            $model = KelompokLaporanRanapView::find()->orderBy(['urutan' => SORT_ASC]);
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
        $model = new LaporanRiView;
        $query = $model::find();
        $date = date('Y-m-d');
        $arr_nopendaftaran = $request->get('no_pendaftaran');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $start = date('Y-m-d', strtotime($_GET['advanced-filter']['tgl_pulang']));
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
        }

        $query->where(['in', 'no_pendaftaran', $arr_nopendaftaran]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query->asArray()->all();
    }

    public function actionExportExcel()
    {
        ini_set('memory_limit', '512M');
        $start = microtime(true);
        $result = [];
        $tmp_data = [];
        $header = [];
        $footer = [];
        $dataFoot = [];
        $klaim_inacbg = [];
        $klaim_dibayar = [];

        $request = Yii::$app->request;
        $model = new LaporanRiHeaderView;
        $query = $model::find();

        $date = date('Y-m-d');
        $instalasi = 'Rawat Ranap';

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pulang'])) {
                $date = date('Y-m-d', strtotime($_GET['advanced-filter']['tgl_pulang']));
                unset($_GET['advanced-filter']['tgl_pulang']);
            }
        }
        $query->where(['tgl_pulang' => $date]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        
        $arr_no_pendaftaran = [];
        $tmp = [];
        if ($data) {
            foreach ($data as $key => $value) {
                if (!in_array($value['no_pendaftaran'], $arr_no_pendaftaran)) {
                    $arr_no_pendaftaran[] = $value['no_pendaftaran'];
                }
                
                if (!isset($tmp[$value['no_pendaftaran']])) {
                    $tmp[$value['no_pendaftaran']] = [
                        'jumlah_perawatan' => $value['total_layanan'] ? $value['total_layanan'] : 0,
                        'arr_poli'         => [],
                        'arr_kamar'        => [],
                        'arr_dokter'       => [],
                        'total_maks'       => 0,
                        'list'             => []
                    ];
                }
            }
            $data_adjusment = [];
            $total_adjusment = [];
            $adjustment_kamar = [];
            $adjustment_hondok = [];
            $adjustment_poli = [];
            $header_static = DocoConstants::GROUP_HEADER;

            if ($tmp) {
                $model = new LaporanRiView;
                $query = $model::find();
                $query->andWhere(['in', 'no_pendaftaran', $arr_no_pendaftaran]);
                // $query->andWhere(['between', 'tgl_pulang', $start, $end]);
                $query = DocoRestActiveFilter::advancedFilter($model, $query);
                $detail = $query->all();
                $defaultColumn = $this->generateColumn();
                $item_total = $defaultColumn;
                if ($detail) {
                    $i = 1;
                    foreach ($detail as $key => $value) {
                        $insert = false;
                        $column = $defaultColumn;
                        $no_pendaftaran = $value['no_pendaftaran'];
                        $name =  $this->clean(strtolower($value['kelompok_nama']));
                        if (in_array($no_pendaftaran, $arr_no_pendaftaran)) {
                            if (isset($tmp[$no_pendaftaran])) {
                                
                                $kode_nota = strtoupper($value['kode_nota']); 
                               if (in_array($kode_nota, $header_static) || $value['is_poliklinik']) {
                                    if ($value['is_kamar'] && ($kode_nota == DocoConstants::KODE_NOTA_KAMAR)) {
                                        $nokamar = $this->clean(strtolower($value['no_kamar']));
                                        if (!isset($adjustment_kamar[$nokamar])) {
                                            $adjustment_kamar[$nokamar] = 0;
                                        }   
                                        $adjustment_kamar[$nokamar] += $value['total_adjust'];
                                    }

                                    if ($value['is_poliklinik']) {
                                        if ($kode_nota == DocoConstants::KODE_NOTA_DOKTER) {
                                            $dokter_kode = strtoupper($value['dokter_kode']);
                                            if (!isset($adjustment_hondok[$dokter_kode])) {
                                                $adjustment_hondok[$dokter_kode] = 0;
                                            }
                                            $adjustment_hondok[$dokter_kode] += $value['total_adjust'];
                                        } else {
                                            $poli_name = $this->clean(strtolower($value['poliklinik']));
                                            if (!isset($adjustment_poli[$poli_name])) {
                                                $adjustment_poli[$poli_name] = 0;
                                            }
                                            $adjustment_poli[$poli_name] += $value['total_adjust'];
                                        }
                                    }
                                } else {
                                    if (!isset($data_adjusment[$no_pendaftaran][$name])) {
                                        $data_adjusment[$no_pendaftaran][$name] = 0;
                                        $total_adjusment[$name] = 0;
                                    }
                                    $data_adjusment[$no_pendaftaran][$name] += $value['total_adjust'];
                                    $total_adjusment[$name] += $value['total_adjust'];
                                }

                                if ($value['is_kamar'] && !in_array($value['no_kamar'], $tmp[$no_pendaftaran]['arr_kamar'])) {
                                    $tmp[$no_pendaftaran]['arr_kamar'][] = $value['no_kamar'];
                                    $count_poli = count($tmp[$no_pendaftaran]['arr_poli']);
                                    $count_kamar = count($tmp[$no_pendaftaran]['arr_kamar']);
                                    $count_dokter = count($tmp[$no_pendaftaran]['arr_dokter']);
                                    if (($count_kamar > $count_poli) && ($count_kamar > $count_dokter)) {
                                        $insert = true;
                                    }
                                }
                                if ($value['is_poliklinik']  && ($value['kode_nota'] !== DocoConstants::KODE_NOTA_DOKTER) && !in_array($value['poliklinik'], $tmp[$no_pendaftaran]['arr_poli'])) {
                                    $tmp[$no_pendaftaran]['arr_poli'][] = $value['poliklinik'];

                                    $count_poli = count($tmp[$no_pendaftaran]['arr_poli']);
                                    $count_kamar = count($tmp[$no_pendaftaran]['arr_kamar']);
                                    $count_dokter = count($tmp[$no_pendaftaran]['arr_dokter']);

                                    if (($count_poli > $count_kamar) && ($count_poli > $count_dokter)) {
                                        $insert = true;
                                    }
                                }
                                if ($value['dokter_kode'] && !in_array($value['dokter_kode'], $tmp[$no_pendaftaran]['arr_dokter'])) {
                                    $tmp[$no_pendaftaran]['arr_dokter'][] = $value['dokter_kode'];
                                    $count_poli = count($tmp[$no_pendaftaran]['arr_poli']);
                                    $count_kamar = count($tmp[$no_pendaftaran]['arr_kamar']);
                                    $count_dokter = count($tmp[$no_pendaftaran]['arr_dokter']);

                                    if (($count_dokter > $count_poli) && ($count_dokter > $count_kamar)) {
                                        $insert = true;
                                    }
                                }
                                if ($insert) {
                                    $tmp[$no_pendaftaran]['list'][] = $column;
                                }
                            }
                        }
                    }

                    $i = 1;
                    $column = $defaultColumn;
                    foreach ($detail as $key => $value) {
                        $column = $defaultColumn;
                        $total = 0;
                        $name =  $this->clean(strtolower($value['kelompok_nama']));
                        $no_pendaftaran = $value['no_pendaftaran']; 
                        $insert = false;

                        if (in_array($no_pendaftaran, $arr_no_pendaftaran)) {
                            if (isset($tmp[$no_pendaftaran])) {
                                if ($tmp[$no_pendaftaran]['list']) {

                                    if (isset($column[$name])) {
                                        $total += $value['total_layanan'];
                                        $item_total[$name] += $value['total_layanan'];
                                    }

                                    if ($value['jasa_dokter']) {
                                        $total += $value['jasa_dokter'];
                                        $item_total['hutang_hondok_jumlah'] += $value['jasa_dokter'];
                                    }

                                    if ($value['is_kamar']) {
                                        $item_total['ruangan_jumlah_hari'] += $value['layanan_qty'];
                                        $item_total['ruangan_biaya'] += $value['tarifsatuan_kamar'] ? $value['tarifsatuan_kamar'] : 0;
                                        $item_total['ruangan_total'] += $value['total_layanan'];
                                        $total += $value['total_layanan'];
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

                                    $tmp[$no_pendaftaran]['list'][0]['tgl_pendaftaran'] =  date('d-m-Y', strtotime($value['tgl_pendaftaran']));
                                    $tmp[$no_pendaftaran]['list'][0]['tgl_pulang'] =  date('d-m-Y', strtotime($value['tgl_pulang']));
                                    $tmp[$no_pendaftaran]['list'][0]['nama_pasien'] =  $value['nama_pasien'];
                                    $tmp[$no_pendaftaran]['list'][0]['diagnosa_utama'] =  $string_diagnosa_utama ? $string_diagnosa_utama : '-';
                                    $tmp[$no_pendaftaran]['list'][0]['diagnosa_penyerta'] =  $string_diagnosa_penyerta ? $string_diagnosa_penyerta : '-';
                                    $tmp[$no_pendaftaran]['list'][0]['no_sep'] =  $value['no_sep'] ? $value['no_sep'] : '-';
                                    $tmp[$no_pendaftaran]['list'][0]['no_rekammedik'] =  $value['no_rekammedik'];
                                    $tmp[$no_pendaftaran]['list'][0]['jumlah_perawatan'] +=  $total;

                                    if ($value['klaim_inacbg']) { 
                                        $tmp[$no_pendaftaran]['list'][0]['klaim_inacbg'] =  $value['klaim_inacbg'];
                                    }
                                    if ($value['klaim_dibayar']) { 
                                        $tmp[$no_pendaftaran]['list'][0]['klaim_dibayar'] =  $value['klaim_dibayar'];
                                    }
                                    if (isset($column[$name])) {
                                        $tmp[$no_pendaftaran]['list'][0][$name] += $value['total_layanan'];
                                    }

                                    $index = array_search($value['no_kamar'], $tmp[$no_pendaftaran]['arr_kamar']);
                                    if ($index !== false) {
                                        $tmp[$no_pendaftaran]['list'][$index]['ruangan_kamar'] = $value['no_kamar'];
                                        $tmp[$no_pendaftaran]['list'][$index]['ruangan_biaya'] = 0;
                                        $tmp[$no_pendaftaran]['list'][$index]['ruangan_jumlah'] = 0;

                                        if ($value['is_kamar']) {
                                            $tmp[$no_pendaftaran]['list'][$index]['ruangan_jumlah_hari'] += $value['layanan_qty'] ? $value['layanan_qty'] : 0;
                                            $tmp[$no_pendaftaran]['list'][$index]['ruangan_biaya'] += $value['tarifsatuan_kamar'] ? $value['tarifsatuan_kamar'] : 0;
                                            $tmp[$no_pendaftaran]['list'][$index]['ruangan_total'] += $value['total_layanan'] ? $value['total_layanan'] : 0;
                                        }
                                    }
                                    
                                    $index = array_search($value['poliklinik'], $tmp[$no_pendaftaran]['arr_poli']);
                                    if ($index !== false) {
                                        $tmp[$no_pendaftaran]['list'][$index]['poli_nama'] = $value['poliklinik'] ? $value['poliklinik'] : '-';
                                        if ($value['is_poliklinik']) {
                                            $tmp[$no_pendaftaran]['list'][$index]['poli_jumlah'] += $value['total_layanan'] ? $value['total_layanan'] : 0;
                                        }
                                    }

                                    $index = array_search($value['dokter_kode'], $tmp[$no_pendaftaran]['arr_dokter']);
                                    if ($index !== false) {
                                        $tmp[$no_pendaftaran]['list'][$index]['hutang_hondok_dokter_kode'] = $value['dokter_kode'] ? strtoupper($value['dokter_kode']) : '-';
                                        $tmp[$no_pendaftaran]['list'][$index]['hutang_hondok_dokter'] = $value['dokter_nama'] ? $value['dokter_nama'] : '-';
                                        $tmp[$no_pendaftaran]['list'][$index]['hutang_hondok_jumlah'] += $value['jasa_dokter'] ? $value['jasa_dokter'] : 0;
                                    }
                                }
                                $i++;
                            }
                        }
                    }

                    $grand_total = 0;
                    $notallow = ['no_rekammedik', 'no_sep', 'rowspan', 'no_rekammedik', 'bagian_kode'];
                    $no = 1;
                    if ($tmp) {
                        foreach ($tmp as $key => $value) {
                            $count_kamar = count($value['arr_kamar']);
                            $count_poli  = count($value['arr_poli']);
                            $count_dokter = count($value['arr_dokter']);
                            $max_row = 0;

                            if ($max_row < $count_kamar) {
                                $max_row = $count_kamar;
                            } 
                            if ($max_row < $count_poli) {
                                $max_row = $count_poli;
                            } 
                            if ($max_row < $count_dokter) {
                                $max_row = $count_dokter;
                            }
                            
                            $mod_poli = 0;
                            $merge_poli = $max_row;
                            $mod_kamar = 0;
                            $merge_kamar = $max_row;
                            $mod_dokter = 0;
                            $merge_dokter = $max_row;

                            if ($count_poli > 1) {
                                $mod_poli = $max_row % $count_poli;
                                $merge_poli = floor($max_row / $count_poli);
                            }

                            if ($count_kamar > 1) {
                                $mod_kamar = $max_row % $count_kamar;
                                $merge_kamar = floor($max_row / $count_kamar);
                            }

                            if ($count_dokter > 1) {
                                $mod_dokter = $max_row % $count_dokter;
                                $merge_dokter = floor($max_row / $count_dokter);
                            }

                            $i = 1;
                            $column_poli = $mod_poli + $merge_poli - 1;
                            $column_kamar = $mod_kamar + $merge_kamar - 1;
                            $column_dokter = $mod_dokter + $merge_dokter - 1;

                            foreach ($value['list'] as $idx => $val) {
                                if ($adjustment_kamar) {
                                    $nokamar = $this->clean(strtolower($val['ruangan_kamar']));
                                    if (isset($adjustment_kamar[$nokamar])) {
                                        $jumlah = $adjustment_kamar[$nokamar];
                                        $total_adjust = $adjustment_kamar[$nokamar];
                                        $grand_total += $adjustment_kamar[$nokamar];
                                        unset($adjustment_kamar[$nokamar]);
                                        
                                        $ruangan_total = $value['list'][0]['ruangan_total'] - $jumlah;
                                        $value['list'][0]['ruangan_total'] = $ruangan_total;
                                        $value['list'][0]['jumlah_perawatan'] = $value['list'][0]['jumlah_perawatan'] - $total_adjust; 
                                        $item_total['ruangan_total'] -= $jumlah;

                                    }
                                }

                                if ($adjustment_hondok) {
                                    $dokterkode = $val['hutang_hondok_dokter_kode'];
                                    if (isset($adjustment_hondok[$dokterkode])) {
                                        $jumlah = $adjustment_hondok[$dokterkode];
                                        $total_adjust = $adjustment_hondok[$dokterkode];
                                        $grand_total += $adjustment_hondok[$dokterkode];
                                        unset($adjustment_hondok[$dokterkode]);

                                        $hutang_hondok_jumlah = $value['list'][$idx]['hutang_hondok_jumlah'] - $jumlah;
                                        $value['list'][$idx]['hutang_hondok_jumlah'] = $hutang_hondok_jumlah;
                                        $value['list'][0]['jumlah_perawatan'] = $value['list'][0]['jumlah_perawatan'] - $total_adjust; 
                                        $item_total['hutang_hondok_jumlah'] -= $jumlah;
                                    }   
                                }

                                if ($adjustment_poli) {
                                    $poli_name = $this->clean(strtolower($val['poli_nama']));
                                    if (isset($adjustment_poli[$poli_name])) {
                                        $jumlah = $adjustment_poli[$poli_name];
                                        $total_adjust = $adjustment_poli[$poli_name];
                                        $grand_total += $adjustment_poli[$poli_name];
                                        unset($adjustment_poli[$poli_name]);

                                        $poli_jumlah = $value['list'][$idx]['poli_jumlah'] - $jumlah;
                                        $value['list'][$idx]['poli_jumlah'] = $poli_jumlah;
                                        $value['list'][0]['jumlah_perawatan'] = $value['list'][0]['jumlah_perawatan'] - $total_adjust; 
                                        $item_total['poli_jumlah'] -= $jumlah;
                                    }   
                                }
                            }

                            foreach ($value['list'] as $idx => $val) {
                                $total_adjust = 0;
                                
                                foreach ($val as $colname => $val1) {
                                    if (!in_array($colname, $notallow)) {
                                        if (is_numeric($val1)) {
                                            $jumlah = $val1;
                                            if (isset($data_adjusment[$key][$colname])) {
                                                $jumlah = $val1 - $data_adjusment[$key][$colname];
                                                $total_adjust += $data_adjusment[$key][$colname];
                                                $grand_total += $data_adjusment[$key][$colname];
                                                unset($data_adjusment[$key][$colname]);
                                            }  
                                            $val[$colname] = $jumlah;
                                        }
                                    }
                                }
                                $val['rowspan'] = $max_row;
                                if ($i == 1) {
                                    $val['no'] = $no;
                                    $val['colmerge_16'] = $max_row;
                                    $val['jumlah_perawatan'] = $val['jumlah_perawatan'] - $total_adjust;
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

                                if ($count_kamar < 2) {
                                    if ($i == 1) {
                                        $val['colmerge_12'] = $max_row;
                                        $val['colmerge_13'] = $max_row;
                                        $val['colmerge_14'] = $max_row;
                                        $val['colmerge_15'] = $max_row;
                                        
                                    } else {
                                        $val['colmerge_12'] = 0;
                                        $val['colmerge_13'] = 0;
                                        $val['colmerge_14'] = 0;
                                        $val['colmerge_15'] = 0;
                                    }
                                    
                                } else if ($count_kamar > 1) { 
                                    if ($count_kamar == $max_row) {
                                        if ($i < $count_kamar) {
                                            $val['colmerge_12'] = 1;
                                            $val['colmerge_13'] = 1;
                                            $val['colmerge_14'] = 1;
                                            $val['colmerge_15'] = 1;
                                        } elseif ($i == $count_kamar) {
                                            $merge = $max_row - $i;
                                            if ($merge > 0) {
                                                $val['colmerge_12'] = $merge + 1; 
                                                $val['colmerge_13'] = $merge + 1; 
                                                $val['colmerge_14'] = $merge + 1; 
                                                $val['colmerge_15'] = $merge + 1; 
                                            } else {
                                                $val['colmerge_12'] = 1;
                                                $val['colmerge_13'] = 1;
                                                $val['colmerge_14'] = 1;
                                                $val['colmerge_15'] = 1;
                                            }
                                            
                                        } else {
                                            $val['colmerge_12'] = 0;
                                            $val['colmerge_13'] = 0;
                                            $val['colmerge_14'] = 0;
                                            $val['colmerge_15'] = 0;
                                        }   
                                    } else {
                                        if ($i < $count_kamar) {
                                            $val['colmerge_12'] = 1;
                                            $val['colmerge_13'] = 1;
                                            $val['colmerge_14'] = 1;
                                            $val['colmerge_15'] = 1;
                                        } else if($i == $count_kamar){
                                            $merge = $max_row - $i;
                                            $val['colmerge_12'] = $merge + 1;
                                            $val['colmerge_13'] = $merge + 1;
                                            $val['colmerge_14'] = $merge + 1;
                                            $val['colmerge_15'] = $merge + 1;
                                        }else {
                                            $val['colmerge_12'] = 0;
                                            $val['colmerge_13'] = 0;
                                            $val['colmerge_14'] = 0;
                                            $val['colmerge_15'] = 0;
                                        }
                                    }
                                }

                                 if ($count_dokter < 2) {
                                    if ($i == 1) {
                                        $val['colmerge_17'] = $max_row;
                                        $val['colmerge_18'] = $max_row;
                                    } else {
                                        $val['colmerge_17'] = 0;
                                        $val['colmerge_18'] = 0;
                                    }
                                    
                                } else if ($count_dokter > 1) { 
                                    if ($count_dokter == $max_row) {
                                        if ($i < $count_dokter) {
                                            $val['colmerge_17'] = 1;
                                            $val['colmerge_18'] = 1;
                                        } elseif ($i == $count_dokter) {
                                            $merge = $max_row - $i;
                                            if ($merge > 0) {
                                                $val['colmerge_17'] = $merge + 1;
                                                $val['colmerge_18'] = $merge + 1;
                                            } else {
                                                $val['colmerge_17'] = 1;
                                                $val['colmerge_18'] = 1;
                                            }
                                        } else {
                                            $val['colmerge_17'] = 0;
                                            $val['colmerge_18'] = 0;
                                        }   
                                    } else {
                                        if ($i < $count_dokter) {
                                            $val['colmerge_17'] = 1;
                                            $val['colmerge_18'] = 1;
                                        } else if($i == $count_dokter){
                                            $merge = $max_row - $i;
                                            $val['colmerge_17'] = $merge + 1; 
                                            $val['colmerge_18'] = $merge + 1; 
                                        }else {
                                            $val['colmerge_17'] = 0;
                                            $val['colmerge_18'] = 0;
                                        }
                                    }
                                }
                                unset($val['hutang_hondok_dokter_kode']);
                                $result[] = $val;
                                $i++;
                            }
                            $no++;
                        }
                    }
                }

                if ($result) {
                    $item_total['no'] = 'TOTAL';
                    $item_total['colspan'] = 5;
                    $item_total['jumlah_perawatan'] = $item_total['jumlah_perawatan'] - $grand_total; 
                    foreach ($item_total as $key => $value) {
                        if (is_numeric($value)) {
                            $jumlah = $value;
                            if (isset($total_adjusment[$key])) {
                                $jumlah = $value - $total_adjusment[$key];
                            }
                            $item_total[$key] = $jumlah;
                        }
                    }
                    
                    $result[] = $item_total;
                }

            }
        }
        $custHeader = $this->generateTableHeader(); 
        $static_header = $custHeader['static_header'];
        $column = $custHeader['column'];
        $header = [
            'Instalasi' => 'Rawat Ranap',
            'Periode' => date('d-m-Y', strtotime($date)),
        ];
        
        $col_merge = [0, 1, 2, 3, 4 , 5, 6, 7, 8, 9, 10];
        for ($i=20; $i <= $column ; $i++) { 
            $col_merge[] = $i;
        }
        return [
            'title'         => 'RINCIAN DETAIL PENDAPATAN RAWAT RAJAL',
            'periode'       => 'Tanggal : ' . date('d-m-Y', strtotime($date)),
            'header_sheet'  => $header, 
            'table_header'  => $static_header, 
            'table_data'    =>$result,
            'col_merge'     => $col_merge
        ];
        // $filePath = DocoHelpers::exportExcel('RINCIAN DETAIL PENDAPATAN RAWAT RANAP', $result, $header,  array(
        //         "skipIncrement" => true,
        //         "skipHeader"    => true,
        //         "sorting"       => false,
        //         'customHeader' => $static_header,
        //     ),$footer,[], true, $mergeBody);
        // // return $filePath;
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
            'ruangan_kamar' => '-',
            'ruangan_jumlah_hari' => 0,
            'ruangan_biaya' => 0,
            'ruangan_jumlah' => 0,
            'ruangan_total' => 0,
            'hutang_hondok_dokter'=> '-',
            'hutang_hondok_jumlah'=> 0,
            'rowspan' => 1,
        ];

        $model = KelompokLaporanRanapView::find()->orderBy(['urutan' => SORT_ASC]);
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
                    ['label'=>'RUANGAN', 'colspan'=>5],
                    ['label'=>'UTANG HONOR DOKTER', 'colspan'=>2],
                ],
            1 => [
                    ['label'=>'UTAMA','startfrom'=>6],
                    ['label'=>'PENYERTA'],
                    ['label'=>'NAMA POLI', 'startfrom'=>4],
                    ['label'=>'JUMLAH (Rp.)'],
                    ['label'=>'KAMAR'],
                    ['label'=>'JUMLAH HARI RAWAT'],
                    ['label'=>'BIAYA (Rp.)'],
                    ['label'=>'JUMLAH'],
                    ['label'=>'TOTAL (Rp.)'],
                    ['label'=>'NAMA DOKTER'],
                    ['label'=>'JUMLAH (Rp.)']
                ]
        ];
        $column = 20;
        $model = KelompokLaporanRanapView::find()->orderBy(['urutan' => SORT_ASC]);
        $data =  $model->all();
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
                        $subItem = ['label' => $value1['kelompoktindakan_nama'] .' (Rp.)'];
                        if (!$reset) {
                            // $subItem['startfrom'] = $startfrom;
                            // $reset = true;
                        }
                        $static_header[1][] = $subItem;
                        $column++;
                    }
                } else {
                    $reset = false;
                    $startfrom = 1;
                    $item['rowspan'] = 2;
                }
                $static_header[0][] = $item;
            }
            $static_header[0][] = ['label'=>'TOTAL (Rp.)', 'rowspan'=>2];
        }
        $column += 2;
        $res = [
            'static_header' => $static_header,
            'column'    => $column
        ];
        return $res;
    }
}