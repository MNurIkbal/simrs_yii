<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\Services\InternalService;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanSensusHarianRjV;
use app\modules\v1\models\LaporanSensusHarianRjDetailV;
use app\modules\v1\models\LaporanSensusHarianRjDetailOnlineV;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\LaporanSensusHarianPasienRajalFn;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\models\UploadForm;
use Doco\rabbitmq\RabbitBgProcess;
use Doco\rabbitmq\SensusPasienRajalBgProcess;

class LapSensusHarianPasienRajalController extends DocoActiveController
{
    public $messageBroker = [
        'generate-data-serconn' => [
            'services' => [
                'Sirs' => [
                    'LapSensusHarianPasienRajal' => [
                        'query_params' => ['tgl_pendaftaran', 'jenis_laporan', 'unique_str'],
                    ]
                ],
            ]
        ],
    ];

    public $modelClass = '';
    const JK = 'jenis_kelamin';
    const JP = 'jenis_pendaftaran';
    const TITLE = 'Laporan Sensus Harian Pasien Rawat Jalan';
    const POLIKLINIK = 722;
    const PMED = 725;
    const IGD = 724;
    const MCU = 723;
    const RUANGAN_NAMA = 'ruangan_nama';
    const BARU_JML = 'baru_jml';
    const LAMA_JML = 'lama_jml';
    const JML_BARU = 'jml_baru';
    const JML_LAMA = 'jml_lama';
    const KUNJUNGAN = 'kunj';
    const HP = 'hp';
    const ADOA = 'adoa';
    const ADOAD = 'adoad';
    const ADOAPP = 'adoapp';
    const TGL_PENDAFTARAN = 'tgl_pendaftaran';
    const JENIS_PENDAFTARAN = 'jenis_pendaftaran';
    const TOTAL = 'TOTAL';
    const JENIS_RUANGAN = 'jenis_ruangan';
    const IS_HEADER = 'is_header';
    const SUBTOT = 'subtotal';
    const _LAMA_LAKI = '_LAMA_LAKI';
    const _LAMA_PEREMPUAN = '_LAMA_PEREMPUAN';
    const _BARU_LAKI = '_BARU_LAKI';
    const _BARU_PEREMPUAN = '_BARU_PEREMPUAN';
    const LAMA_LAKI = 'lama_laki';
    const LAMA_PEREMPUAN = 'lama_perempuan';
    const BARU_LAKI = 'baru_laki';
    const BARU_PEREMPUAN = 'baru_perempuan';
    const JENIS_RUANGAN_NAMA = 'jenis_ruangan_nama';
    const STRTOLOWER = 'strtolower';
    const STRTOUPPER = 'strtoupper';
    const UCFIRST = 'ucfirst';
    const UCWORD = 'ucword';

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
        $type = isset($_GET['advanced-filter'][self::JENIS_PENDAFTARAN]) ? $_GET['advanced-filter'][self::JENIS_PENDAFTARAN] : DocoConstants::J_P_L;

        if(!empty($type)){
            if($type == DocoConstants::J_P_O){
                return $this->getDataDetailOnline();
            } else {
                return $this->getDataDetail();
            }
        }
    }

    public function actionGetHeader($isGroup = false)
    {
        $data = [];
        $model   = new LaporanSensusHarianRjV;
        if($isGroup) {
            $query   = $model::find()->select(['jenis_ruangan', 'jenis_ruangan_nama'])->all();
        } else{
            $query   = $model::find()->all();
        }
            
        foreach($query as $k => $v) {
            if($isGroup) {
                $data[$v['jenis_ruangan']] = $v;
            } else {
                $data[$v['ruangan_id']] = $v;
            }
        }

        return $data;
    }

    public function actionGenerateApi()
    {
        $jk = $this->getLookupByType(self::JK)->all();
        $jenis_pendaftaran = $this->getLookupByType(self::JP)->orderBy('lookup_id', SORT_ASC)->all();
        $caraBayar = $this->generateData(null, null, null, null, true);
        $dataCaraBayar = $this->generateHeaderColumns($caraBayar['carabayar'], $caraBayar['carabayarList']);

        return [
            self::JK => $jk,
            self::JP => $jenis_pendaftaran,
            'cara_bayar' => $dataCaraBayar['carabayar'],
            'columns' => $dataCaraBayar['columns'],
            'header' => $dataCaraBayar['header'],
        ];
    }

    private function getDataDetail()
    {
        try {
            $countDay = 1;
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
    
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_PENDAFTARAN])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_PENDAFTARAN]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                        $countDay = self::countHp($start,$end);
                    }
                    unset($_GET['advanced-filter'][self::TGL_PENDAFTARAN]);
                }
            }

            $data = $this->generateData($start,$end, DocoConstants::J_P_L , $countDay);
            return [
                'data' => $data['data'],
                'header' => $this->generateHeaderColumns($data['carabayar'], $data['carabayarList'])
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

    private function getDataDetailOnline()
    {
        try {
            $start   = date('Y-m-d 00:00:00');
            $end     = date('Y-m-d 23:59:59');
            $countDay = 1;
    
            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter'][self::TGL_PENDAFTARAN])) {
                    $explode = explode(" - ", $_GET['advanced-filter'][self::TGL_PENDAFTARAN]);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                        $countDay = self::countHp($start,$end);
                    }
                    unset($_GET['advanced-filter'][self::TGL_PENDAFTARAN]);
                }
            }
    
            $data = $this->generateData($start,$end, DocoConstants::J_P_O, $countDay);
            return [
                'data' => $data['data'],
                'header' => $this->generateHeaderColumns($data['carabayar'], $data['carabayarList'])
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

    private function data()
    {
        return LaporanSensusHarianRjDetailV::find();
    }

    private function dataOnline()
    {
        return LaporanSensusHarianRjDetailOnlineV::find();
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data table
    * @attribute #title# => Judul
    * @attribute #periode# => Periode
    * @attribute #jenis# => Jenis Pendaftaran
    */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request->get();
            $title = self::TITLE;
            $countDay = 1;
            $tmp = [];
            $data = [];

            $jk = $this->getLookupByType(self::JK)->all();
            $jenisLookup = $this->getLookupByType(self::JP)->all();
    
            $start   = date('Y-m-d');
            $end     = date('Y-m-d');
            $jenis = DocoConstants::J_P_L;
           
            if(isset($request['advanced-filter'][self::JENIS_PENDAFTARAN]) && $request['advanced-filter'][self::JENIS_PENDAFTARAN] == DocoConstants::J_P_O) {
                $jenis = DocoConstants::J_P_O;
            }
    
            if(isset($request['advanced-filter']['tgl_pendaftaran_awal']) && isset($request['advanced-filter']['tgl_pendaftaran_akhir'])) {
                $start = $request['advanced-filter']['tgl_pendaftaran_awal'];
                $end = $request['advanced-filter']['tgl_pendaftaran_akhir'];
                $countDay = self::countHp($start,$end);
            }

            $data = $this->generateData($start,$end, $jenis, $countDay);
            $header = $this->generateHeaderColumns($data['carabayar'], $data['carabayarList']);
            unset($header['header'][self::TGL_PENDAFTARAN]);
            unset($header['header'][self::JENIS_PENDAFTARAN]);

            foreach($header['header'] as $key => $val) {
                $newCol[] = $val;
            }

            $jenisLookup = ($jenis == $jenisLookup[0]['lookup_id']) ? $jenisLookup[0]['lookup_name'] : $jenisLookup[1]['lookup_name'];

            $countCarabayar = 1;
            $countCarabayar = count($header['carabayar']);
            $colspanCounter = $countCarabayar * 2;
            $coljumlahpasien = ($colspanCounter * 2) + 2;

            $print = new DocoPrint();
            error_reporting(0); 
            $print->attributes = [
                '#title#' => $title,
                '#periode#' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
                '#jenis#' => $jenisLookup,
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $data['data'],
                    'columns' => $newCol,
                    self::JK => $jk,
                    'carabayar' => $header['carabayar'],
                    'countCarabayar' => $countCarabayar,
                    'colspanCounter' => $colspanCounter,
                    'coljumlahpasien' => $coljumlahpasien,
                ]),
            ];
            $print->Output();
        } catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        try {
            ini_set('memory_limit','-1');
            ini_set('max_execution_time', 300);
            $request = Yii::$app->request;
            $title = self::TITLE;
            $result = [];
            $data = [];
            $countDay = 1;
            $jenisLookup = $this->getLookupByType(self::JP)->all();
            $jk = $this->getLookupByType(self::JK)->all();
            
            $start = date('Y-m-d');
            $end = date('Y-m-d');
            $jenis = DocoConstants::J_P_L;
            $advancedFilters = $request->get('advanced-filter', []);

            if (isset($advancedFilters)) {
                if (isset($advancedFilters[self::TGL_PENDAFTARAN])) {
                    if($advancedFilters[self::TGL_PENDAFTARAN] != ' - '){
                        $explode = explode(' - ', $advancedFilters[self::TGL_PENDAFTARAN]);
                        $start = date('Y-m-d', strtotime($explode[0]));
                        $end = date('Y-m-d', strtotime($explode[1]));
                        $countDay = self::countHp($start,$end);
                        unset($advancedFilters[self::TGL_PENDAFTARAN]);
                    }
                }

                if (isset($advancedFilters[self::JENIS_PENDAFTARAN])) {
                    $jenis = $advancedFilters[self::JENIS_PENDAFTARAN];
                }
            }
            
            $data = $this->generateData($start,$end, $jenis, $countDay, null, true);
            $dataHeader = $this->generateHeaderColumns($data['carabayar'], $data['carabayarList']);

            unset($dataHeader['header'][self::TGL_PENDAFTARAN]);
            unset($dataHeader['header'][self::JENIS_PENDAFTARAN]);
            foreach($dataHeader['header'] as $key => $val) {
                $newCol[] = $val;
            }

            foreach($data['data'] as $key => $value) {
                $no = 1;
                for($i=0; $i < count($dataHeader['header']); $i++) {
                    $tmp[$no] = $value[$newCol[$i]];
                    $no++;
                }
                $result[] = $tmp;
            }

            $jenisLookup = ($jenis == $jenisLookup[0]['lookup_id']) ? $jenisLookup[0]['lookup_name'] : $jenisLookup[1]['lookup_name'];

            $header = [
                'Periode' => date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end)),
                'Jenis' => $jenisLookup
            ];

            $x = count($result[0]);
            $y = count($result);
            $startCoordinate = 10;
            $endCoordinate = $startCoordinate + $y;
            $customFormatCode = [
                [
                    'strCoordinate' => $x - 2,
                    'startNumCoordinate' => $startCoordinate,
                    'endNumCoordinate' => $endCoordinate,
                ],
                [
                    'strCoordinate' => $x - 1,
                    'startNumCoordinate' => $startCoordinate,
                    'endNumCoordinate' => $endCoordinate,
                ],
                [
                    'strCoordinate' => $x,
                    'startNumCoordinate' => $startCoordinate,
                    'endNumCoordinate' => $endCoordinate,
                ],
            ];

            $countCarabayar = count($dataHeader['carabayar']);
            $colspanCounter = $countCarabayar * 2;
            $coljumlahpasien = ($colspanCounter * 2) + 2;
            $staticFirstRow = [
                [
                    'label' => 'NO',
                    'rowspan' => 4
                ],
                [
                    'label' => 'DEPARTMENT',
                    'rowspan' => 4
                ],
                [
                    'label' => 'JUMLAH PASIEN',
                    'colspan' => $coljumlahpasien
                ],
                [
                    'label' => 'JUMLAH',
                    'colspan' => 2
                ],
                [
                    'label' => strtoupper(self::ADOA),
                    'rowspan' => 4
                ],
                [
                    'label' => strtoupper(self::ADOAD),
                    'rowspan' => 4
                ],
                [
                    'label' => strtoupper(self::ADOAPP),
                    'rowspan' => 4
                ],
            ];

            $staticSecondRow = [
                [
                    'label' => 'BARU',
                    'colspan' => $colspanCounter,
                    'startfrom' => 3
                ],
                [
                    'label' => 'JUMLAH BARU',
                    'rowspan' => 3,
                ],
                [
                    'label' => 'LAMA',
                    'colspan' => $colspanCounter,
                ],
                [
                    'label' => 'JUMLAH LAMA',
                    'rowspan' => 3,
                ],
                [
                    'label' => 'KUNJUNGAN',
                    'rowspan' => 3,
                ],
                [
                    'label' => 'HP',
                    'rowspan' => 3,
                ],
            ];

            foreach($dataHeader['carabayar'] as $k => $v) {
                $tmpCabarBaru[] = [
                    'label' => strtoupper($v),
                    'colspan' => 2,
                ];
                $tmpCabarLama[] = [
                    'label' => strtoupper($v),
                    'colspan' => 2,
                ];
                if($k == 0) {
                    $tmpCabarBaru[0]['startfrom'] = 3;
                    $tmpCabarLama[0]['startfrom'] = 2;
                }
            }
            $listCaraBayar = array_merge($tmpCabarBaru, $tmpCabarLama);

            $listJk = [];
            for ($i=0; $i < $colspanCounter; $i++) { 
                $jkCodeL = $jk[0]['lookup_kode'];
                $jkCodeP = $jk[1]['lookup_kode'];
                $tmpJkL = [
                    'label' => $jkCodeL,
                ];
                $tmpJkP = [
                    'label' => $jkCodeP,
                ];

                if($i == 0) {
                    $tmpJkL['startfrom'] = 3;
                }

                if($i == $countCarabayar) {
                    $tmpJkL['startfrom'] = 2;
                }
                array_push($listJk, $tmpJkL);
                array_push($listJk, $tmpJkP);
            }

            $custHeader = [
                $staticFirstRow,
                $staticSecondRow,
                $listCaraBayar,
                $listJk
            ];
    
            $filePath = DocoHelpers::exportExcel(self::TITLE, $result, $header,  array("uploadPath" => "./uploads", "skipIncrement" => true, 'customFormatCode' => $customFormatCode,'customHeader' => $custHeader),[],[],true);
            $filePath->save('php://output');
            die;

        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    private function generateData($start = null, $end = null, $jenis = null, $countDay = 1, $getOnlyCarabayar = false, $isExcel = false)
    {
        $data = $tmp = $result = $carabayarList = $carabayar = [];
        $kunjungan = $hp = 0;

        if(!$start) {
            $start   = date('Y-m-d 00:00:00');
        }

        if(!$end) {
            $end     = date('Y-m-d 23:59:59');
        }

        if(!$jenis) {
            $jenis = DocoConstants::J_P_L;
        }

        if($getOnlyCarabayar) {
            $command = Yii::$app->db->createCommand('SELECT * FROM header_sensus_harian_rajal_fn()');
            $data = $command->queryAll();

            foreach($data as $key => $value) {
                $tmp[$value['ruangan_id']][$value['carabayar_id']] = $value;
                $carabayar[$value['carabayar_nama']] = null;
            }

            foreach($tmp as $k => $v) {
                foreach($v as $kk => $vv) {
                    $ruanganId = $vv['ruangan_id'];
                    $caraBayarName = str_replace(' ', '_', $vv['carabayar_nama']);

                    if(!isset($carabayarList[$caraBayarName.self::_BARU_LAKI])) {
                        $carabayarList[$caraBayarName.self::_BARU_LAKI] = null;
                    }

                    if(!isset($carabayarList[$caraBayarName.self::_BARU_PEREMPUAN])) {
                        $carabayarList[$caraBayarName.self::_BARU_PEREMPUAN] = null;
                    }

                    if(!isset($carabayarList[$caraBayarName.self::_LAMA_LAKI])) {
                        $carabayarList[$caraBayarName.self::_LAMA_LAKI] = null;
                    }

                    if(!isset($carabayarList[$caraBayarName.self::_LAMA_PEREMPUAN])) {
                        $carabayarList[$caraBayarName.self::_LAMA_PEREMPUAN] = null;
                    }
                }
            }
            return [
                'carabayarList' => $carabayarList,
                'carabayar' => $carabayar,
            ];
        } else {
            $tmpData = [];

            $data = LaporanSensusHarianPasienRajalFn::getData($start, $end, $jenis);

            foreach($data as $key => $value) {
                $tmp[$value['ruangan_id']][$value['carabayar_id']] = $value;
                $carabayar[$value['carabayar_nama']] = null;
            }

            foreach($tmp as $k => $v) {
                foreach($v as $kk => $vv) {
                    $ruanganId = $vv['ruangan_id'];
                    $instalasi = $vv['instalasi_id'];
                    $caraBayarName = str_replace(' ', '_', $vv['carabayar_nama']);

                    if(isset($tmpData[$ruanganId])) {
                        $tmpData[$ruanganId][self::HP] = $vv[self::HP] ;
                        $tmpData[$ruanganId][self::JML_BARU] += $vv[self::JML_BARU] ;
                        $tmpData[$ruanganId][self::JML_LAMA] += $vv[self::JML_LAMA] ;
                        $tmpData[$ruanganId][self::KUNJUNGAN] += $vv['kunjungan'];
                    } else {
                        $tmpData[$ruanganId]['no'] = null;
                        $tmpData[$ruanganId][self::RUANGAN_NAMA] = $vv[self::RUANGAN_NAMA];
                        $tmpData[$ruanganId][self::JENIS_RUANGAN] = $vv[self::JENIS_RUANGAN];
                        $tmpData[$ruanganId][self::JML_BARU] = $vv[self::JML_BARU];
                        $tmpData[$ruanganId][self::KUNJUNGAN] = $vv['kunjungan'];
                        $tmpData[$ruanganId][self::JML_LAMA] = $vv[self::JML_LAMA];
                        $tmpData[$ruanganId][self::ADOA] = 0;
                        $tmpData[$ruanganId][self::ADOAD] = 0;
                        $tmpData[$ruanganId][self::ADOAPP] = 0;
                        $tmpData[$ruanganId][self::HP] = 0; 
                        $tmpData[$ruanganId][self::IS_HEADER] = 1; 
                        $tmpData[$ruanganId][self::JENIS_PENDAFTARAN] = null;
                        $tmpData[$ruanganId][self::TGL_PENDAFTARAN] = null; 
                    }

                    if($instalasi == DocoConstants::VAR_IGD_ID) {
                        $tmpData[$ruanganId][self::HP] = $countDay;
                    }

                    if(isset($tmpData[self::TOTAL])) {
                        $tmpData[self::TOTAL][self::JML_BARU] += $vv[self::JML_BARU] ;
                        $tmpData[self::TOTAL][self::JML_LAMA] += $vv[self::JML_LAMA] ;
                        $tmpData[self::TOTAL][self::KUNJUNGAN] += $vv['kunjungan'];
                    } else {
                        $tmpData[self::TOTAL]['no'] = null;
                        $tmpData[self::TOTAL][self::RUANGAN_NAMA] = self::TOTAL;
                        $tmpData[self::TOTAL][self::JENIS_RUANGAN] = 99999;
                        $tmpData[self::TOTAL][self::JML_BARU] = $vv[self::JML_BARU];
                        $tmpData[self::TOTAL][self::KUNJUNGAN] = $vv['kunjungan'];
                        $tmpData[self::TOTAL][self::JML_LAMA] = $vv[self::JML_LAMA];
                        $tmpData[self::TOTAL][self::ADOA] = 0;
                        $tmpData[self::TOTAL][self::ADOAD] = 0;
                        $tmpData[self::TOTAL][self::ADOAPP] = 0;
                        $tmpData[self::TOTAL][self::HP] = $countDay; 
                        $tmpData[self::TOTAL][self::IS_HEADER] = 2; 
                        $tmpData[self::TOTAL][self::JENIS_PENDAFTARAN] = null;
                        $tmpData[self::TOTAL][self::TGL_PENDAFTARAN] = null; 
                    }

                    if(isset($tmpData[$ruanganId][$caraBayarName.self::_BARU_LAKI])) {
                        $tmpData[$ruanganId][$caraBayarName.self::_BARU_LAKI] += $vv[self::BARU_LAKI];
                    }else{
                        $tmpData[$ruanganId][$caraBayarName.self::_BARU_LAKI] = $vv[self::BARU_LAKI];
                    }

                    if(isset($tmpData[$ruanganId][$caraBayarName.self::_BARU_PEREMPUAN])) {
                        $tmpData[$ruanganId][$caraBayarName.self::_BARU_PEREMPUAN] += $vv[self::BARU_PEREMPUAN];
                    }else{
                        $tmpData[$ruanganId][$caraBayarName.self::_BARU_PEREMPUAN] = $vv[self::BARU_PEREMPUAN];
                    }
                    
                    if(isset($tmpData[$ruanganId][$caraBayarName.self::_LAMA_LAKI])) {
                        $tmpData[$ruanganId][$caraBayarName.self::_LAMA_LAKI] += $vv[self::LAMA_LAKI];
                    }else{
                        $tmpData[$ruanganId][$caraBayarName.self::_LAMA_LAKI] = $vv[self::LAMA_LAKI];
                    }

                    if(isset($tmpData[$ruanganId][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                        $tmpData[$ruanganId][$caraBayarName.self::_LAMA_PEREMPUAN] += $vv[self::LAMA_PEREMPUAN];
                    }else{
                        $tmpData[$ruanganId][$caraBayarName.self::_LAMA_PEREMPUAN] = $vv[self::LAMA_PEREMPUAN];
                    }

                    if(!isset($tmpData[self::TOTAL][$caraBayarName.self::_BARU_LAKI])) {
                        $tmpData[self::TOTAL][$caraBayarName.self::_BARU_LAKI] = $vv[self::BARU_LAKI];
                    } else {
                        $tmpData[self::TOTAL][$caraBayarName.self::_BARU_LAKI] += $vv[self::BARU_LAKI];
                    }

                    if(!isset($tmpData[self::TOTAL][$caraBayarName.self::_BARU_PEREMPUAN])) {
                        $tmpData[self::TOTAL][$caraBayarName.self::_BARU_PEREMPUAN] = $vv[self::BARU_PEREMPUAN];
                    } else {
                        $tmpData[self::TOTAL][$caraBayarName.self::_BARU_PEREMPUAN] += $vv[self::BARU_PEREMPUAN];
                    }

                    if(!isset($tmpData[self::TOTAL][$caraBayarName.self::_LAMA_LAKI])) {
                        $tmpData[self::TOTAL][$caraBayarName.self::_LAMA_LAKI] = $vv[self::LAMA_LAKI];
                    } else {
                        $tmpData[self::TOTAL][$caraBayarName.self::_LAMA_LAKI] += $vv[self::LAMA_LAKI];
                    }

                    if(!isset($tmpData[self::TOTAL][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                        $tmpData[self::TOTAL][$caraBayarName.self::_LAMA_PEREMPUAN] = $vv[self::LAMA_PEREMPUAN];
                    } else {
                        $tmpData[self::TOTAL][$caraBayarName.self::_LAMA_PEREMPUAN] += $vv[self::LAMA_PEREMPUAN];
                    }

                    if(!isset($carabayarList[$caraBayarName.self::_BARU_LAKI])) {
                        $carabayarList[$caraBayarName.self::_BARU_LAKI] = null;
                    }

                    if(!isset($carabayarList[$caraBayarName.self::_BARU_PEREMPUAN])) {
                        $carabayarList[$caraBayarName.self::_BARU_PEREMPUAN] = null;
                    }

                    if(!isset($carabayarList[$caraBayarName.self::_LAMA_LAKI])) {
                        $carabayarList[$caraBayarName.self::_LAMA_LAKI] = null;
                    }

                    if(!isset($carabayarList[$caraBayarName.self::_LAMA_PEREMPUAN])) {
                        $carabayarList[$caraBayarName.self::_LAMA_PEREMPUAN] = null;
                    }

                    foreach($jenisRuangan as $key => $value) { 
                        if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]])) {
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]]['no'] = null;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::RUANGAN_NAMA] =  $value[self::JENIS_RUANGAN_NAMA];
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::JENIS_RUANGAN] =  (int) $value['jenis_ruangan'];
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::JML_BARU] =  null;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::KUNJUNGAN] =  null;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::HP] =  null;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::JML_LAMA] =  null;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::ADOA] =  null;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::ADOAD] =  null;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::ADOAPP] =  null;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::IS_HEADER] = 2;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::JENIS_PENDAFTARAN] =  null;
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][self::TGL_PENDAFTARAN] =  null;
                        }

                        if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI])) {
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI] = null;
                        }
        
                        if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN])) {
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN] = null;
                        }
        
                        if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI])) {
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI] = null;
                        }
        
                        if(!isset($tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                            $tmpData[$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN] = null;
                        }

                        if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]])) {
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]]['no'] = null;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::RUANGAN_NAMA] = ucfirst(self::SUBTOT).' '. $value[self::JENIS_RUANGAN_NAMA];
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JENIS_PENDAFTARAN] = null;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_BARU] = 0;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::KUNJUNGAN] = null;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::HP] = $countDay;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_LAMA] = 0;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::ADOA] = 0;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::ADOAD] = 0;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::ADOAPP] = 0;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::IS_HEADER] = 0;
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JENIS_RUANGAN] = (int) $value['jenis_ruangan'];
                            $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::TGL_PENDAFTARAN] = null;

                            if($value['jenis_ruangan'] == $vv['jenis_ruangan']) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_BARU] += $vv[self::JML_BARU];
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_LAMA] += $vv[self::JML_LAMA];
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::KUNJUNGAN] += $vv['kunjungan'];
        
                                if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI])) {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI] = $vv[self::BARU_LAKI];
                                }

                                if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN])) {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN] = $vv[self::BARU_PEREMPUAN];
                                }
                
                                if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI])) {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI] = $vv[self::LAMA_LAKI];
                                } 

                                if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN] = $vv[self::LAMA_PEREMPUAN];
                                }     
                            }
                        } else {
                            if($value['jenis_ruangan'] == $vv['jenis_ruangan']) {
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_BARU] += $vv[self::JML_BARU];
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::JML_LAMA] += $vv[self::JML_LAMA];
                                $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][self::KUNJUNGAN] += $vv['kunjungan'];
        
                                if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI])) {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI] = $vv[self::BARU_LAKI];
                                } else {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_LAKI] += $vv[self::BARU_LAKI];
                                }
                
                                if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN])) {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN] = $vv[self::BARU_PEREMPUAN];
                                } else {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_BARU_PEREMPUAN] += $vv[self::BARU_PEREMPUAN];
                                }
                
                                if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI])) {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI] = $vv[self::LAMA_LAKI];
                                } else {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_LAKI] += $vv[self::LAMA_LAKI];
                                }
                
                                if(!isset($tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN])) {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN] = $vv[self::LAMA_PEREMPUAN];
                                } else {
                                    $tmpData[self::SUBTOT.$value[self::JENIS_RUANGAN_NAMA]][$caraBayarName.self::_LAMA_PEREMPUAN] += $vv[self::LAMA_PEREMPUAN];
                                }        
                            }
                        } 
                    }
                }
            }

            ArrayHelper::multisort($tmpData, [self::JENIS_RUANGAN, self::IS_HEADER, self::RUANGAN_NAMA], [SORT_ASC, SORT_DESC, SORT_ASC]);

            $no = 0;
            foreach($tmpData as $key => $value) {
                $baru = $value[self::JML_BARU];
                $lama = $value[self::JML_LAMA];
                if($value[self::KUNJUNGAN] != 0 && $value[self::HP] != 0) {
                    $value[self::ADOA] = number_format($value[self::KUNJUNGAN] / $value[self::HP],2);
                }

                if($baru != 0 && $value[self::HP] != 0) {
                    $value[self::ADOAD] = number_format($baru / $value[self::HP], 2);
                }

                if($baru != 0 && $lama != 0) {
                    $value[self::ADOAPP] = number_format(($baru + $lama) / $baru, 2);
                }

                if($value[self::IS_HEADER] == 1) {
                    $no++;
                    $value['no'] = $no;
                } else {
                    if(!$isExcel) {
                        if($value[self::IS_HEADER] == 2 || $value[self::IS_HEADER] == 0) {
                            $value[self::RUANGAN_NAMA] = '<b>'.$value[self::RUANGAN_NAMA].'</b>';
                        }
                    }
                    $value['no'] = '';
                }
                unset($value[self::JENIS_RUANGAN]);
                // unset($value[self::IS_HEADER]);
                $result[] = $value;
            }

            return [
                'data' => $result,
                'carabayarList' => $carabayarList,
                'carabayar' => $carabayar
            ];
        }
    }

    private function generateHeaderColumns($carabayar, $carabayarList) 
    {
        $header = $columns = $tmpCarabayar = $tmpBaru = $tmpLama = $tmpHeader = $newCabar = [];

        $staticHeaderAwal = [
            'no' => 'no',
            self::RUANGAN_NAMA => self::RUANGAN_NAMA
        ];

        $staticHeaderTengah = [
            self::JML_BARU => self::JML_BARU
        ];

        $staticHeaderAkhir = [
            self::JML_LAMA => self::JML_LAMA,
            self::KUNJUNGAN =>self::KUNJUNGAN,
            self::HP =>self::HP,
            self::ADOA =>self::ADOA,
            self::ADOAD =>self::ADOAD,
            self::ADOAPP =>self::ADOAPP,
            self::JENIS_PENDAFTARAN => self::JENIS_PENDAFTARAN,
            self::TGL_PENDAFTARAN => self::TGL_PENDAFTARAN,
        ];
        
        foreach($carabayarList as $k => $v) {
            $nameLaki = strtolower(substr($k, -9));
            $nameCewe = strtolower(substr($k, -14));
            if($nameLaki == self::BARU_LAKI || $nameCewe == self::BARU_PEREMPUAN) {
                $tmpBaru[$k] = $k;
            } else {
                $tmpLama[$k] = $k;
            }
        }

        $tmpHeader = array_merge($staticHeaderAwal, $tmpBaru);
        $tmpHeader = array_merge($tmpHeader, $staticHeaderTengah);
        $tmpHeader = array_merge($tmpHeader, $tmpLama);
        $tmpHeader = array_merge($tmpHeader, $staticHeaderAkhir);

        foreach($tmpHeader as $k => $v) {
            $visible = true;
            $search = false;
            $title = $k;
            $nameLaki = strtolower(substr($k, -9));
            $nameCewe = strtolower(substr($k, -14));

            if($k == self::JENIS_PENDAFTARAN || $k == self::TGL_PENDAFTARAN) {
                $visible = false;
                $search = true;
            }

            if($nameLaki == self::BARU_LAKI || $nameLaki == self::LAMA_LAKI) {
                $title = 'L';
            }

            if($nameCewe == self::BARU_PEREMPUAN || $nameCewe == self::LAMA_PEREMPUAN) {
                $title = 'P';
            }

            if($k == self::IS_HEADER) {
                $visible = false;
                $search = false;
            }

            $columns[] =[
                'title' => self::formatToReadable($title, self::STRTOUPPER),
                'data' => $k,
                'searchable' => $search,
                'orderable' => false,
                'visible' => $visible,
            ];
        }

        foreach($carabayar as $kei => $val) {
            $newCabar[] = $kei;
        }

        return [
            'header' => $tmpHeader,
            'columns' => $columns,
            'carabayar' => $newCabar,
        ];
    }

    private static function formatToReadable($string , $format = null)
    {
        switch ($string) {
            case self::RUANGAN_NAMA:
                $string = 'Department';
                break;
            case self::JML_BARU:
                $string = 'Jumlah Baru';
                break;
            case self::JML_LAMA:
                $string = 'Jumlah Lama';
                break;
            case self::KUNJUNGAN:
                $string = 'Kunjungan';
                break;
            case self::TGL_PENDAFTARAN:
                $string = 'Tanggal Pendaftaran';
                break;
            case self::JENIS_PENDAFTARAN:
                $string = 'Jenis Pendaftaran';
                break;
            default:
                $string = $string;
                break;
        }

        switch ($format) {
            case self::UCFIRST:
                $string = ucfirst($string);
                break;
            case self::UCWORD:
                $string = ucwords($string);
                break;
            case self::STRTOUPPER:
                $string = strtoupper($string);
                break;
            case self::STRTOLOWER:
                $string = strtolower($string);
                break;
            default:
                $string = $string;
                break;
        }

        return $string;
    }

    private static function countHp($start, $end)
    {
        $dateOne = new \DateTime($start);
        $dateTwo = new \DateTime($end);
        $countDay = ($dateTwo->diff($dateOne)->days) + 1;

        return $countDay;
    }

    public function actionGenerateDataSerconn()
    {
        $request = Yii::$app->request->get();
        $data['status'] = 'update';
        $randString = isset($request['unique_str']) ? $request['unique_str'] : null;
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'sensus-harian-rajal:'.$randString,
            'message' => json_encode($data),
        ]);
        return [
            'randString' => $randString
        ];       
    }

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        (new InternalService)->sendTo([
            'Sirs' => [
                'LapSensusHarianPasienRajalExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'ExportLapSensusHarianPasienRajal' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'UploadLapSensusHarianPasienRajalExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);

        return [
            'randString' => $randString
        ];
    }

    public function actionSyncExportExcelRabbitmq()
    {
        try {
            $request = Yii::$app->request;
            $randString = $request->get('randString');
            $range_tanggal = $request->get('tgl_pendaftaran');
            $jenis_laporan = $request->get('jenis_laporan');
            $start = date('Y-m-01 00:00:00');
            $end = date('Y-m-d 23:59:00');
            
            if ($range_tanggal) {
                $explode = explode(" - ", $range_tanggal);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
            }

            $dataCount = LaporanSensusHarianPasienRajalFn::getData($start, $end, $jenis_laporan, true);

            $countData = $totalPerPage = $dataCount;
            $headerExcel = [
                'Tanggal' => $request->get('tgl_pendaftaran'),
            ];

            (new SensusPasienRajalBgProcess())->send([
                'unique_str' => $randString,
                'filter' => $request->get(),
                'totalPerPage' => $countData,
                'headerExcel' => $headerExcel,
                'countData' => $countData, 
                'jenis_laporan' => $jenis_laporan,
                'sendToUrl' => 'lap-sensus-harian-pasien-rajal/drop-file',
                'base_uri' => Yii::$app->docoRest->getBaseUri('rm'),
            ], 'laporan_sensus_harian_rajal');

            return [
                'totalPerPage' => $totalPerPage,
                'unique_str' => $randString,
                'countData' => $countData,
            ];
            
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ];
        }
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost)
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;

            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $fileName = $rootPath.'/' . $no_request . '.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }
}